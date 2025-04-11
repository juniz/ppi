<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\Bangsal;
use Filament\Forms\Form;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class LajuHAIs extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $title = 'Laju HAIs';
    protected static ?string $slug = 'laju-hais';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.laju-hais';

    public $tanggal_mulai;
    public $tanggal_selesai;
    public $ruangan;
    public $dataHAP = [];
    public $dataIAD = [];
    public $dataILO = [];
    public $dataISK = [];
    public $dataPLEB = [];
    public $dataVAP = [];

    public function mount()
    {
        $this->tanggal_mulai = Carbon::now()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->format('Y-m-d');
        $this->ruangan = 'all';
        $this->loadData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData()),
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData()),
                Select::make('ruangan')
                    ->label('Ruangan')
                    ->options(Bangsal::pluck('nm_bangsal', 'kd_bangsal'))
                    ->placeholder('Semua Ruangan')
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData())
            ])
            ->columns(3);
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

        // Load HAP Data
        $this->dataHAP = $baseQuery->clone()
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.HAP != 0 THEN data_HAIs.no_rawat END) as jumlah_pasien'),
                DB::raw('SUM(data_HAIs.HAP) as hari_rawat'),
                DB::raw('COUNT(CASE WHEN data_HAIs.HAP > 0 THEN 1 END) as hap'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.HAP > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.HAP),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.HAP > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.HAP != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->get();

        // Load IAD Data dengan format yang sama
        $this->dataIAD = $baseQuery->clone()
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.IAD != 0 THEN data_HAIs.no_rawat END) as jumlah_pasien'),
                DB::raw('SUM(data_HAIs.IAD) as hari_terpasang'),
                DB::raw('COUNT(CASE WHEN data_HAIs.IAD > 0 THEN 1 END) as iad'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.IAD > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.IAD),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.IAD > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.IAD != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->get();

        // Load ILO Data
        $this->dataILO = $baseQuery->clone()
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.ILO != 0 THEN data_HAIs.no_rawat END) as jumlah_pasien'),
                DB::raw('SUM(data_HAIs.ILO) as hari_operasi'),
                DB::raw('COUNT(CASE WHEN data_HAIs.ILO > 0 THEN 1 END) as ilo'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.ILO > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.ILO),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.ILO > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.ILO != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->get();

        // Load ISK Data
        $this->dataISK = $baseQuery->clone()
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.UC != 0 THEN data_HAIs.no_rawat END) as jumlah_pasien'),
                DB::raw('SUM(data_HAIs.UC) as hari_uc'),
                DB::raw('SUM(data_HAIs.ISK) as isk'),
                DB::raw("CONCAT(ROUND((SUM(data_HAIs.ISK)/NULLIF(SUM(data_HAIs.UC),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((SUM(data_HAIs.ISK)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.UC != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->get();

        // Load PLEBITIS Data
        $this->dataPLEB = $baseQuery->clone()
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.IVL != 0 THEN data_HAIs.no_rawat END) as jumlah_pasien'),
                DB::raw('SUM(data_HAIs.IVL) as hari_infus'),
                DB::raw('SUM(data_HAIs.PLEB) as pleb'),
                DB::raw("CONCAT(ROUND((SUM(data_HAIs.PLEB)/NULLIF(SUM(data_HAIs.IVL),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((SUM(data_HAIs.PLEB)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.IVL != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->get();

        // Load VAP Data
        $this->dataVAP = $baseQuery->clone()
            ->select([
                'bangsal.nm_bangsal',
                DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.VAP != 0 THEN data_HAIs.no_rawat END) as jumlah_pasien'),
                DB::raw('SUM(data_HAIs.VAP) as hari_ventilator'),
                DB::raw('COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END) as vap'),
                DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.VAP),0))*1000), ' ‰') as laju"),
                DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.VAP != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
            ])
            ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->get();
    }
} 