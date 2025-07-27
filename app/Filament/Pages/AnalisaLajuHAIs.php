<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Actions\Action as PageAction;
use App\Models\Bangsal;
use App\Models\AnalisaRekomendasi;
use App\Services\ChartImageService;
use Filament\Forms\Form;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification as NotificationAlias;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Pages\Page;

class AnalisaLajuHAIs extends Page implements HasForms, HasTable
{
    protected static string $view = 'filament.pages.analisa-laju-hais';
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?string $navigationLabel = 'Analisis dan Rekomendasi';
    protected static ?string $title = 'Analisis dan Rekomendasi Laju HAIs';

    public $tanggal_mulai;
    public $tanggal_selesai;
    public $ruangan;
    public $data;
    public $analisa;
    public $rekomendasi;
    public $showPreviewModal = false;
    public $isLoading = false;
    public $dataHAP = [];
    public $dataIAD = [];
    public $dataILO = [];
    public $dataISK = [];
    public $dataPLEB = [];
    public $dataVAP = [];
    public $summaryData = []; // Tambahkan properti untuk ringkasan data

    protected function getViewData(): array
    {
        return [
            'form' => $this->form,
            'table' => $this->table,
            'dataHAP' => $this->dataHAP,
            'dataIAD' => $this->dataIAD,
            'dataILO' => $this->dataILO,
            'dataISK' => $this->dataISK,
            'dataPLEB' => $this->dataPLEB,
            'dataVAP' => $this->dataVAP,
        ];
    }

    public function mount(): void
    {
        $this->tanggal_mulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->ruangan = 'all'; // Set default ruangan
        
        // Inisialisasi form dengan nilai default
        $this->form->fill([
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'ruangan' => $this->ruangan,
        ]);
        
        // Inisialisasi data kosong - data akan dimuat saat klik "Terapkan Filter"
        $this->dataHAP = collect();
        $this->dataIAD = collect();
        $this->dataILO = collect();
        $this->dataISK = collect();
        $this->dataPLEB = collect();
        $this->dataVAP = collect();
        
        // Hanya update session tanpa memuat data
        $this->updateChartSession();
        $this->loadAnalisaRekomendasi();
    }

    public function loadData()
    {
        $baseQuery = DB::table('data_HAIs')
            ->join('kamar', 'data_HAIs.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->whereBetween('data_HAIs.tanggal', [$this->tanggal_mulai, $this->tanggal_selesai]);

        if ($this->ruangan !== 'all' && $this->ruangan !== null) {
            $baseQuery->where('bangsal.kd_bangsal', $this->ruangan);
        }

        // Query untuk HAP
        $this->dataHAP = (clone $baseQuery)
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m") as bulan'),
                DB::raw('CASE 
                    WHEN MONTH(data_HAIs.tanggal) = 1 THEN "Januari"
                    WHEN MONTH(data_HAIs.tanggal) = 2 THEN "Februari" 
                    WHEN MONTH(data_HAIs.tanggal) = 3 THEN "Maret"
                    WHEN MONTH(data_HAIs.tanggal) = 4 THEN "April"
                    WHEN MONTH(data_HAIs.tanggal) = 5 THEN "Mei"
                    WHEN MONTH(data_HAIs.tanggal) = 6 THEN "Juni"
                    WHEN MONTH(data_HAIs.tanggal) = 7 THEN "Juli"
                    WHEN MONTH(data_HAIs.tanggal) = 8 THEN "Agustus"
                    WHEN MONTH(data_HAIs.tanggal) = 9 THEN "September"
                    WHEN MONTH(data_HAIs.tanggal) = 10 THEN "Oktober"
                    WHEN MONTH(data_HAIs.tanggal) = 11 THEN "November"
                    WHEN MONTH(data_HAIs.tanggal) = 12 THEN "Desember"
                END as nama_bulan'),
                DB::raw('YEAR(data_HAIs.tanggal) as tahun'),
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.HAP != 0 THEN data_HAIs.no_rawat END) as numerator'),
                DB::raw('SUM(data_HAIs.HAP) as hari_rawat'),
                DB::raw('COUNT(CASE WHEN data_HAIs.HAP > 0 THEN 1 END) as denumerator'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.HAP > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.HAP),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.HAP > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.HAP != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal', DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m")'), DB::raw('YEAR(data_HAIs.tanggal)'), DB::raw('MONTH(data_HAIs.tanggal)'))
            ->orderBy('tahun')
            ->orderBy(DB::raw('MONTH(data_HAIs.tanggal)'))
            ->get();

        // Query untuk IAD (menggunakan CVL untuk hari terpasang)
        $this->dataIAD = (clone $baseQuery)
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m") as bulan'),
                DB::raw('CASE 
                    WHEN MONTH(data_HAIs.tanggal) = 1 THEN "Januari"
                    WHEN MONTH(data_HAIs.tanggal) = 2 THEN "Februari" 
                    WHEN MONTH(data_HAIs.tanggal) = 3 THEN "Maret"
                    WHEN MONTH(data_HAIs.tanggal) = 4 THEN "April"
                    WHEN MONTH(data_HAIs.tanggal) = 5 THEN "Mei"
                    WHEN MONTH(data_HAIs.tanggal) = 6 THEN "Juni"
                    WHEN MONTH(data_HAIs.tanggal) = 7 THEN "Juli"
                    WHEN MONTH(data_HAIs.tanggal) = 8 THEN "Agustus"
                    WHEN MONTH(data_HAIs.tanggal) = 9 THEN "September"
                    WHEN MONTH(data_HAIs.tanggal) = 10 THEN "Oktober"
                    WHEN MONTH(data_HAIs.tanggal) = 11 THEN "November"
                    WHEN MONTH(data_HAIs.tanggal) = 12 THEN "Desember"
                END as nama_bulan'),
                DB::raw('YEAR(data_HAIs.tanggal) as tahun'),
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.CVL != 0 THEN data_HAIs.no_rawat END) as numerator'),
                DB::raw('SUM(data_HAIs.CVL) as hari_terpasang'),
                DB::raw('COUNT(CASE WHEN data_HAIs.IAD > 0 THEN 1 END) as denumerator'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.IAD > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.CVL),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.IAD > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.CVL != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal', DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m")'), DB::raw('YEAR(data_HAIs.tanggal)'), DB::raw('MONTH(data_HAIs.tanggal)'))
            ->orderBy('tahun')
            ->orderBy(DB::raw('MONTH(data_HAIs.tanggal)'))
            ->get();

        // Query untuk ILO
        $this->dataILO = (clone $baseQuery)
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m") as bulan'),
                DB::raw('CASE 
                    WHEN MONTH(data_HAIs.tanggal) = 1 THEN "Januari"
                    WHEN MONTH(data_HAIs.tanggal) = 2 THEN "Februari" 
                    WHEN MONTH(data_HAIs.tanggal) = 3 THEN "Maret"
                    WHEN MONTH(data_HAIs.tanggal) = 4 THEN "April"
                    WHEN MONTH(data_HAIs.tanggal) = 5 THEN "Mei"
                    WHEN MONTH(data_HAIs.tanggal) = 6 THEN "Juni"
                    WHEN MONTH(data_HAIs.tanggal) = 7 THEN "Juli"
                    WHEN MONTH(data_HAIs.tanggal) = 8 THEN "Agustus"
                    WHEN MONTH(data_HAIs.tanggal) = 9 THEN "September"
                    WHEN MONTH(data_HAIs.tanggal) = 10 THEN "Oktober"
                    WHEN MONTH(data_HAIs.tanggal) = 11 THEN "November"
                    WHEN MONTH(data_HAIs.tanggal) = 12 THEN "Desember"
                END as nama_bulan'),
                DB::raw('YEAR(data_HAIs.tanggal) as tahun'),
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.ILO != 0 THEN data_HAIs.no_rawat END) as numerator'),
                DB::raw('SUM(data_HAIs.ILO) as hari_operasi'),
                DB::raw('COUNT(CASE WHEN data_HAIs.ILO > 0 THEN 1 END) as denumerator'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.ILO > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.ILO),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.ILO > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.ILO != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal', DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m")'), DB::raw('YEAR(data_HAIs.tanggal)'), DB::raw('MONTH(data_HAIs.tanggal)'))
            ->orderBy('tahun')
            ->orderBy(DB::raw('MONTH(data_HAIs.tanggal)'))
            ->get();

        // Query untuk ISK (menggunakan UC untuk hari kateter)
        $this->dataISK = (clone $baseQuery)
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m") as bulan'),
                DB::raw('CASE 
                    WHEN MONTH(data_HAIs.tanggal) = 1 THEN "Januari"
                    WHEN MONTH(data_HAIs.tanggal) = 2 THEN "Februari" 
                    WHEN MONTH(data_HAIs.tanggal) = 3 THEN "Maret"
                    WHEN MONTH(data_HAIs.tanggal) = 4 THEN "April"
                    WHEN MONTH(data_HAIs.tanggal) = 5 THEN "Mei"
                    WHEN MONTH(data_HAIs.tanggal) = 6 THEN "Juni"
                    WHEN MONTH(data_HAIs.tanggal) = 7 THEN "Juli"
                    WHEN MONTH(data_HAIs.tanggal) = 8 THEN "Agustus"
                    WHEN MONTH(data_HAIs.tanggal) = 9 THEN "September"
                    WHEN MONTH(data_HAIs.tanggal) = 10 THEN "Oktober"
                    WHEN MONTH(data_HAIs.tanggal) = 11 THEN "November"
                    WHEN MONTH(data_HAIs.tanggal) = 12 THEN "Desember"
                END as nama_bulan'),
                DB::raw('YEAR(data_HAIs.tanggal) as tahun'),
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.UC != 0 THEN data_HAIs.no_rawat END) as numerator'),
                DB::raw('SUM(data_HAIs.UC) as hari_kateter'),
                DB::raw('COUNT(CASE WHEN data_HAIs.ISK > 0 THEN 1 END) as denumerator'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.ISK > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.UC),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.ISK > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.UC != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal', DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m")'), DB::raw('YEAR(data_HAIs.tanggal)'), DB::raw('MONTH(data_HAIs.tanggal)'))
            ->orderBy('tahun')
            ->orderBy(DB::raw('MONTH(data_HAIs.tanggal)'))
            ->get();

        // Query untuk PLEB
        $this->dataPLEB = (clone $baseQuery)
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m") as bulan'),
                DB::raw('CASE 
                    WHEN MONTH(data_HAIs.tanggal) = 1 THEN "Januari"
                    WHEN MONTH(data_HAIs.tanggal) = 2 THEN "Februari" 
                    WHEN MONTH(data_HAIs.tanggal) = 3 THEN "Maret"
                    WHEN MONTH(data_HAIs.tanggal) = 4 THEN "April"
                    WHEN MONTH(data_HAIs.tanggal) = 5 THEN "Mei"
                    WHEN MONTH(data_HAIs.tanggal) = 6 THEN "Juni"
                    WHEN MONTH(data_HAIs.tanggal) = 7 THEN "Juli"
                    WHEN MONTH(data_HAIs.tanggal) = 8 THEN "Agustus"
                    WHEN MONTH(data_HAIs.tanggal) = 9 THEN "September"
                    WHEN MONTH(data_HAIs.tanggal) = 10 THEN "Oktober"
                    WHEN MONTH(data_HAIs.tanggal) = 11 THEN "November"
                    WHEN MONTH(data_HAIs.tanggal) = 12 THEN "Desember"
                END as nama_bulan'),
                DB::raw('YEAR(data_HAIs.tanggal) as tahun'),
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.IVL != 0 THEN data_HAIs.no_rawat END) as numerator'),
                DB::raw('SUM(data_HAIs.IVL) as hari_infus'),
                DB::raw('SUM(data_HAIs.PLEB) as denumerator'),
                DB::raw("CONCAT(ROUND((SUM(data_HAIs.PLEB)/NULLIF(SUM(data_HAIs.IVL),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((SUM(data_HAIs.PLEB)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.IVL != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal', DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m")'), DB::raw('YEAR(data_HAIs.tanggal)'), DB::raw('MONTH(data_HAIs.tanggal)'))
            ->orderBy('tahun')
            ->orderBy(DB::raw('MONTH(data_HAIs.tanggal)'))
            ->get();

        // Query untuk VAP (menggunakan ETT untuk hari ventilator)
        $this->dataVAP = (clone $baseQuery)
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m") as bulan'),
                DB::raw('CASE 
                    WHEN MONTH(data_HAIs.tanggal) = 1 THEN "Januari"
                    WHEN MONTH(data_HAIs.tanggal) = 2 THEN "Februari" 
                    WHEN MONTH(data_HAIs.tanggal) = 3 THEN "Maret"
                    WHEN MONTH(data_HAIs.tanggal) = 4 THEN "April"
                    WHEN MONTH(data_HAIs.tanggal) = 5 THEN "Mei"
                    WHEN MONTH(data_HAIs.tanggal) = 6 THEN "Juni"
                    WHEN MONTH(data_HAIs.tanggal) = 7 THEN "Juli"
                    WHEN MONTH(data_HAIs.tanggal) = 8 THEN "Agustus"
                    WHEN MONTH(data_HAIs.tanggal) = 9 THEN "September"
                    WHEN MONTH(data_HAIs.tanggal) = 10 THEN "Oktober"
                    WHEN MONTH(data_HAIs.tanggal) = 11 THEN "November"
                    WHEN MONTH(data_HAIs.tanggal) = 12 THEN "Desember"
                END as nama_bulan'),
                DB::raw('YEAR(data_HAIs.tanggal) as tahun'),
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.ETT != 0 THEN data_HAIs.no_rawat END) as numerator'),
                DB::raw('SUM(data_HAIs.ETT) as hari_ventilator'),
                DB::raw('COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END) as denumerator'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.ETT),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.ETT != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal', DB::raw('DATE_FORMAT(data_HAIs.tanggal, "%Y-%m")'), DB::raw('YEAR(data_HAIs.tanggal)'), DB::raw('MONTH(data_HAIs.tanggal)'))
            ->orderBy('tahun')
            ->orderBy(DB::raw('MONTH(data_HAIs.tanggal)'))
            ->get();
    }

    public function loadAnalisaRekomendasi(): void
    {
        $this->data = AnalisaRekomendasi::query()
            ->where('tanggal_mulai', $this->tanggal_mulai)
            ->where('tanggal_selesai', $this->tanggal_selesai)
            ->when($this->ruangan, function ($query) {
                $query->where('ruangan', $this->ruangan);
            })
            ->first();

        if ($this->data) {
            $this->analisa = $this->data->analisa;
            $this->rekomendasi = $this->data->rekomendasi;
        } else {
            $this->analisa = '';
            $this->rekomendasi = '';
        }
    }

    // Hapus atau comment properti showPreviewModal karena tidak diperlukan lagi
    // public $showPreviewModal = false;

    public function showPreview(): void
    {
        Log::info('showPreview called', [
            'ruangan' => $this->ruangan,
            'analisa' => $this->analisa,
            'rekomendasi' => $this->rekomendasi
        ]);

        // Validasi ruangan harus dipilih
        if (empty($this->ruangan)) {
            Log::info('Ruangan empty, showing warning');
            NotificationAlias::make()
                ->warning()
                ->title('Peringatan')
                ->body('Silakan pilih ruangan terlebih dahulu sebelum menyimpan analisis dan rekomendasi.')
                ->send();
            return;
        }

        // Validasi analisa dan rekomendasi tidak boleh kosong
        if (empty(trim($this->analisa)) || empty(trim($this->rekomendasi))) {
            Log::info('Analisa or rekomendasi empty, showing warning');
            NotificationAlias::make()
                ->warning()
                ->title('Peringatan')
                ->body('Analisis dan rekomendasi tidak boleh kosong.')
                ->send();
            return;
        }

        Log::info('Setting showPreviewModal to true');
        $this->showPreviewModal = true;
    }

    private function cleanDataForSaving($data): array
    {
        return collect($data)->map(function ($item) {
            $cleanItem = [];
            foreach ($item as $key => $value) {
                // Konversi nilai string "0" menjadi integer 0
                if ($value === "0") {
                    $cleanItem[$key] = 0;
                }
                // Konversi nilai numerik string menjadi integer/float
                elseif (is_string($value) && is_numeric($value)) {
                    $cleanItem[$key] = strpos($value, '.') !== false ? (float) $value : (int) $value;
                }
                // Konversi null menjadi 0 untuk field numerik
                elseif (is_null($value) && in_array($key, ['numerator', 'denumerator', 'hari_rawat', 'hari_operasi', 'hari_kateter', 'hari_infus', 'hari_ventilator'])) {
                    $cleanItem[$key] = 0;
                }
                // Konversi null menjadi "0" untuk field laju dan persentase
                elseif (is_null($value) && in_array($key, ['laju', 'persentase'])) {
                    $cleanItem[$key] = "0";
                }
                // Biarkan nilai lainnya apa adanya
                else {
                    $cleanItem[$key] = $value;
                }
            }
            return $cleanItem;
        })->toArray();
    }

    private function calculateSummaryData($data, $numeratorField, $denominatorField): array
    {
        $totalKasus = collect($data)->sum('denumerator');
        $totalDenominator = collect($data)->sum($denominatorField);
        $rataLaju = $this->calculateAverageLaju(collect($data));
        
        return [
            'kasus' => $totalKasus,
            'denominator' => $totalDenominator,
            'rata_laju' => $rataLaju
        ];
    }

    public function saveAnalisaRekomendasi(): void
    {
        try {
            $this->isLoading = true;
            Log::info('saveAnalisaRekomendasi method called');
            
            // Validasi input yang lebih komprehensif
            $errors = [];
            
            if (empty($this->ruangan)) {
                $errors[] = 'Silakan pilih ruangan terlebih dahulu.';
            }
            
            if (empty(trim($this->analisa))) {
                $errors[] = 'Analisis tidak boleh kosong.';
            }
            
            if (empty(trim($this->rekomendasi))) {
                $errors[] = 'Rekomendasi tidak boleh kosong.';
            }
            
            // Validasi minimal panjang teks
            if (strlen(trim($this->analisa)) < 10) {
                $errors[] = 'Analisis minimal harus 10 karakter.';
            }
            
            if (strlen(trim($this->rekomendasi)) < 10) {
                $errors[] = 'Rekomendasi minimal harus 10 karakter.';
            }
            
            if (!empty($errors)) {
                NotificationAlias::make()
                    ->warning()
                    ->title('Validasi Gagal')
                    ->body(implode(' ', $errors))
                    ->send();
                return;
            }

            // Langsung simpan tanpa preview
            // Siapkan data summary laju untuk disimpan
            $summaryLaju = $this->prepareSummaryLaju();

            // Bersihkan data sebelum disimpan
            $cleanDataHAP = $this->cleanDataForSaving($this->dataHAP);
            $cleanDataIAD = $this->cleanDataForSaving($this->dataIAD);
            $cleanDataILO = $this->cleanDataForSaving($this->dataILO);
            $cleanDataISK = $this->cleanDataForSaving($this->dataISK);
            $cleanDataPLEB = $this->cleanDataForSaving($this->dataPLEB);
            $cleanDataVAP = $this->cleanDataForSaving($this->dataVAP);

            // Hitung summary data untuk setiap jenis HAI
            $hapSummary = $this->calculateSummaryData($this->dataHAP, 'numerator', 'hari_rawat');
            $iadSummary = $this->calculateSummaryData($this->dataIAD, 'numerator', 'hari_terpasang');
            $iloSummary = $this->calculateSummaryData($this->dataILO, 'numerator', 'hari_operasi');
            $iskSummary = $this->calculateSummaryData($this->dataISK, 'numerator', 'hari_kateter');
            $plebitisSummary = $this->calculateSummaryData($this->dataPLEB, 'numerator', 'hari_infus');
            $vapSummary = $this->calculateSummaryData($this->dataVAP, 'numerator', 'hari_ventilator');

            // Selalu buat record baru alih-alih update
            $analisaRekomendasi = AnalisaRekomendasi::create([
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'ruangan' => $this->ruangan,
                'analisa' => $this->analisa,
                'rekomendasi' => $this->rekomendasi,
                'data_hap' => $cleanDataHAP,
                'data_iad' => $cleanDataIAD,
                'data_ilo' => $cleanDataILO,
                'data_isk' => $cleanDataISK,
                'data_plebitis' => $cleanDataPLEB,
                'data_vap' => $cleanDataVAP,
                'summary_laju' => $summaryLaju,
                // Summary fields yang mudah dibaca
                'total_hap_kasus' => $hapSummary['kasus'],
                'total_hap_hari_rawat' => $hapSummary['denominator'],
                'rata_hap_laju' => $hapSummary['rata_laju'],
                'total_iad_kasus' => $iadSummary['kasus'],
                'total_iad_hari_terpasang' => $iadSummary['denominator'],
                'rata_iad_laju' => $iadSummary['rata_laju'],
                'total_ilo_kasus' => $iloSummary['kasus'],
                'total_ilo_hari_operasi' => $iloSummary['denominator'],
                'rata_ilo_laju' => $iloSummary['rata_laju'],
                'total_isk_kasus' => $iskSummary['kasus'],
                'total_isk_hari_kateter' => $iskSummary['denominator'],
                'rata_isk_laju' => $iskSummary['rata_laju'],
                'total_plebitis_kasus' => $plebitisSummary['kasus'],
                'total_plebitis_hari_infus' => $plebitisSummary['denominator'],
                'rata_plebitis_laju' => $plebitisSummary['rata_laju'],
                'total_vap_kasus' => $vapSummary['kasus'],
                'total_vap_hari_ventilator' => $vapSummary['denominator'],
                'rata_vap_laju' => $vapSummary['rata_laju'],
            ]);

            // Reset form setelah berhasil simpan
            $this->analisa = '';
            $this->rekomendasi = '';

            // Dispatch event untuk trigger penyimpanan chart
            $this->dispatch('analisa-saved', ['id' => $analisaRekomendasi->id]);

            NotificationAlias::make()
                ->success()
                ->title('Berhasil Disimpan!')
                ->body('Data analisis dan rekomendasi berhasil disimpan. Grafik sedang diproses dan akan tersimpan otomatis.')
                ->send();

        } catch (\Exception $e) {
            Log::error('Error in saveAnalisaRekomendasi: ' . $e->getMessage());
            
            NotificationAlias::make()
                ->danger()
                ->title('Error')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function confirmSave(): void
    {
        try {
            $this->isLoading = true;
            
            // Siapkan data summary laju untuk disimpan
            $summaryLaju = $this->prepareSummaryLaju();

            // Bersihkan data sebelum disimpan
            $cleanDataHAP = $this->cleanDataForSaving($this->dataHAP);
            $cleanDataIAD = $this->cleanDataForSaving($this->dataIAD);
            $cleanDataILO = $this->cleanDataForSaving($this->dataILO);
            $cleanDataISK = $this->cleanDataForSaving($this->dataISK);
            $cleanDataPLEB = $this->cleanDataForSaving($this->dataPLEB);
            $cleanDataVAP = $this->cleanDataForSaving($this->dataVAP);

            // Hitung summary data untuk setiap jenis HAI
            $hapSummary = $this->calculateSummaryData($this->dataHAP, 'numerator', 'hari_rawat');
            $iadSummary = $this->calculateSummaryData($this->dataIAD, 'numerator', 'hari_terpasang');
            $iloSummary = $this->calculateSummaryData($this->dataILO, 'numerator', 'hari_operasi');
            $iskSummary = $this->calculateSummaryData($this->dataISK, 'numerator', 'hari_kateter');
            $plebitisSummary = $this->calculateSummaryData($this->dataPLEB, 'numerator', 'hari_infus');
            $vapSummary = $this->calculateSummaryData($this->dataVAP, 'numerator', 'hari_ventilator');

            // Selalu buat record baru alih-alih update
            $analisaRekomendasi = AnalisaRekomendasi::create([
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'ruangan' => $this->ruangan,
                'analisa' => $this->analisa,
                'rekomendasi' => $this->rekomendasi,
                'data_hap' => $cleanDataHAP,
                'data_iad' => $cleanDataIAD,
                'data_ilo' => $cleanDataILO,
                'data_isk' => $cleanDataISK,
                'data_plebitis' => $cleanDataPLEB,
                'data_vap' => $cleanDataVAP,
                'summary_laju' => $summaryLaju,
                // Summary fields yang mudah dibaca
                'total_hap_kasus' => $hapSummary['kasus'],
                'total_hap_hari_rawat' => $hapSummary['denominator'],
                'rata_hap_laju' => $hapSummary['rata_laju'],
                'total_iad_kasus' => $iadSummary['kasus'],
                'total_iad_hari_terpasang' => $iadSummary['denominator'],
                'rata_iad_laju' => $iadSummary['rata_laju'],
                'total_ilo_kasus' => $iloSummary['kasus'],
                'total_ilo_hari_operasi' => $iloSummary['denominator'],
                'rata_ilo_laju' => $iloSummary['rata_laju'],
                'total_isk_kasus' => $iskSummary['kasus'],
                'total_isk_hari_kateter' => $iskSummary['denominator'],
                'rata_isk_laju' => $iskSummary['rata_laju'],
                'total_plebitis_kasus' => $plebitisSummary['kasus'],
                'total_plebitis_hari_infus' => $plebitisSummary['denominator'],
                'rata_plebitis_laju' => $plebitisSummary['rata_laju'],
                'total_vap_kasus' => $vapSummary['kasus'],
                'total_vap_hari_ventilator' => $vapSummary['denominator'],
                'rata_vap_laju' => $vapSummary['rata_laju'],
            ]);

            $this->showPreviewModal = false;

            // Reset form setelah berhasil simpan
            $this->analisa = '';
            $this->rekomendasi = '';

            // Dispatch event untuk trigger penyimpanan chart
            $this->dispatch('analisa-saved', ['id' => $analisaRekomendasi->id]);

            NotificationAlias::make()
                ->success()
                ->title('Berhasil Disimpan!')
                ->body('Data analisis dan rekomendasi berhasil disimpan. Grafik sedang diproses dan akan tersimpan otomatis.')
                ->send();
                
        } catch (\Exception $e) {
            Log::error('Error in confirmSave: ' . $e->getMessage());
            
            NotificationAlias::make()
                ->danger()
                ->title('Error')
                ->body('Terjadi kesalahan saat menyimpan: ' . $e->getMessage())
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function cancelPreview(): void
    {
        $this->showPreviewModal = false;
    }

    private function prepareSummaryLaju(): array
    {
        $summary = [
            'periode' => [
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'ruangan' => $this->ruangan,
                'nama_ruangan' => $this->ruangan ? Bangsal::where('kd_bangsal', $this->ruangan)->first()?->nm_bangsal : 'Semua Ruangan'
            ],
            'total_data' => [
                'hap' => collect($this->dataHAP)->count(),
                'iad' => collect($this->dataIAD)->count(),
                'ilo' => collect($this->dataILO)->count(),
                'isk' => collect($this->dataISK)->count(),
                'plebitis' => collect($this->dataPLEB)->count(),
                'vap' => collect($this->dataVAP)->count(),
            ],
            'rata_rata_laju' => [
                'hap' => $this->calculateAverageLaju(collect($this->dataHAP)),
                'iad' => $this->calculateAverageLaju(collect($this->dataIAD)),
                'ilo' => $this->calculateAverageLaju(collect($this->dataILO)),
                'isk' => $this->calculateAverageLaju(collect($this->dataISK)),
                'plebitis' => $this->calculateAverageLaju(collect($this->dataPLEB)),
                'vap' => $this->calculateAverageLaju(collect($this->dataVAP)),
            ],
            'created_at' => now()->toDateTimeString()
        ];

        return $summary;
    }

    private function calculateAverageLaju($data): float
    {
        if ($data->isEmpty()) {
            return 0;
        }

        $totalLaju = 0;
        $count = 0;

        foreach ($data as $item) {
            // Ekstrak angka dari string laju (contoh: "15 ‰" -> 15)
            $laju = preg_replace('/[^0-9.]/', '', $item->laju);
            if (is_numeric($laju)) {
                $totalLaju += (float) $laju;
                $count++;
            }
        }

        return $count > 0 ? round($totalLaju / $count, 2) : 0;
    }

    // Menambahkan method untuk redirect ke halaman Data Analisa & Rekomendasi
    public function redirectToDataAnalisa(): void
    {
        $this->redirect('/admin/analisa-rekomendasis');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Tombol header dihapus
        ];
    }

    protected function updateChartSession(): void
    {
        session([
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'ruangan' => $this->ruangan
        ]);
        
        // Force session save
        session()->save();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AnalisaRekomendasi::query()
                    ->join('bangsal', 'bangsal.kd_bangsal', '=', 'analisa_rekomendasi_hais.ruangan')
            )
            ->columns([
                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date(),
                TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date(),
                TextColumn::make('nm_bangsal')
                    ->label('Ruangan'),
                TextColumn::make('analisa')
                    ->label('Analisa')
                    ->wrap(),
                TextColumn::make('rekomendasi')
                    ->label('Rekomendasi')
                    ->wrap()
            ]);
    }

    public function resetFilters(): void
    {
        $this->tanggal_mulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->ruangan = 'all'; // Set default ruangan
        
        // Update form dengan nilai yang direset
        $this->form->fill([
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'ruangan' => $this->ruangan,
        ]);
        
        // Reset data menjadi kosong - pengguna harus klik "Terapkan Filter" untuk memuat data
        $this->dataHAP = collect();
        $this->dataIAD = collect();
        $this->dataILO = collect();
        $this->dataISK = collect();
        $this->dataPLEB = collect();
        $this->dataVAP = collect();
        
        $this->updateChartSession();
        $this->loadAnalisaRekomendasi();
        
        // Emit event untuk refresh widget
        $this->dispatch('refreshCharts');
        
        NotificationAlias::make()
            ->success()
            ->title('Filter Direset')
            ->body('Filter telah direset. Klik "Terapkan Filter" untuk memuat data.')
            ->send();
    }

    public function applyFilters(): void
    {
        $formData = $this->form->getState();
        $this->tanggal_mulai = $formData['tanggal_mulai'];
        $this->tanggal_selesai = $formData['tanggal_selesai'];
        $this->ruangan = $formData['ruangan'] ?? 'all';
        
        // Memuat data hanya saat tombol "Terapkan Filter" diklik
        $this->loadData();
        $this->loadAnalisaRekomendasi();
        $this->updateChartSession();
        
        // Emit event untuk refresh widget
        $this->dispatch('refreshCharts');
        
        NotificationAlias::make()
            ->success()
            ->title('Filter Diterapkan')
            ->body('Data laju HAIs telah dimuat sesuai filter yang dipilih.')
            ->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required(),
                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required(),
                        Select::make('ruangan')
                            ->label('Ruangan')
                            ->options(Bangsal::pluck('nm_bangsal', 'kd_bangsal'))
                            ->placeholder('Semua Ruangan'),
                        Actions::make([
                            Action::make('apply_filters')
                                ->label('Terapkan Filter')
                                ->action('applyFilters')
                                ->color('primary')
                                ->icon('heroicon-o-funnel')
                                ->size('sm'),
                            Action::make('reset_filters')
                                ->label('Reset')
                                ->action('resetFilters')
                                ->color('gray')
                                ->icon('heroicon-o-arrow-path')
                                ->size('sm')
                        ])
                        ->alignStart()
                        ->fullWidth(false)
                        ->extraAttributes([
                            'class' => 'mt-6',
                            'style' => 'align-self: end;'
                        ])
                    ])
                    ->columns(4)
            ]);
    }
}