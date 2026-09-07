<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Models\DataHais;

class HaisBulanan extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?string $navigationLabel = 'HAIs Bulanan';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.hais-bulanan';

    public ?array $filters = [];

    public function mount(): void
    {
        $this->filters = [
            'dari_tanggal' => Carbon::now()->startOfMonth()->toDateString(),
            'sampai_tanggal' => Carbon::now()->endOfMonth()->toDateString(),
            'kd_bangsal' => null,
        ];
        $this->form->fill($this->filters);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Rekapitulasi Surveilans HAIs Bulanan')
                    ->description('Saring rekapitulasi data surveilans HAIs harian per bulan berdasarkan periode tanggal dan ruangan/bangsal.')
                    ->icon('heroicon-o-funnel')
                    ->collapsible()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 3])
                            ->schema([
                                DatePicker::make('dari_tanggal')
                                    ->label('Dari Tanggal')
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->required(),
                                DatePicker::make('sampai_tanggal')
                                    ->label('Sampai Tanggal')
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->required(),
                                Select::make('kd_bangsal')
                                    ->label('Ruang / Bangsal')
                                    ->placeholder('Semua Bangsal')
                                    ->prefixIcon('heroicon-m-building-office-2')
                                    ->options(fn () => \Illuminate\Support\Facades\Cache::remember(
                                        'bangsal_options', 300, fn () => \App\Models\Bangsal::pluck('nm_bangsal', 'kd_bangsal')
                                    ))
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->footerActions([
                        Action::make('bulan_ini')
                            ->label('Bulan Ini')
                            ->color('gray')
                            ->icon('heroicon-m-calendar')
                            ->action('setFilterThisMonth'),
                        Action::make('bulan_lalu')
                            ->label('Bulan Lalu')
                            ->color('gray')
                            ->icon('heroicon-m-calendar-days')
                            ->action('setFilterLastMonth'),
                        Action::make('tahun_ini')
                            ->label('Tahun Ini')
                            ->color('gray')
                            ->icon('heroicon-m-calendar')
                            ->action('setFilterThisYear'),
                        Action::make('reset')
                            ->label('Reset')
                            ->color('gray')
                            ->icon('heroicon-m-arrow-path')
                            ->action('resetFilters'),
                        Action::make('cari')
                            ->label('Terapkan Filter')
                            ->color('primary')
                            ->icon('heroicon-m-magnifying-glass')
                            ->action('applyFilters'),
                        Action::make('cetak_pdf')
                            ->label('Cetak PDF')
                            ->color('success')
                            ->icon('heroicon-m-printer')
                            ->action('printPdf'),
                    ])
            ])
            ->statePath('filters');
    }

    public function printPdf(): void
    {
        $filters = $this->form->getState();
        $query = http_build_query(array_filter([
            'dari_tanggal' => $filters['dari_tanggal'] ?? $this->filters['dari_tanggal'] ?? null,
            'sampai_tanggal' => $filters['sampai_tanggal'] ?? $this->filters['sampai_tanggal'] ?? null,
            'kd_bangsal' => $filters['kd_bangsal'] ?? $this->filters['kd_bangsal'] ?? null,
        ]));

        $url = route('export.hais-bulanan.pdf') . ($query ? '?' . $query : '');
        $this->js("window.open('{$url}', '_blank')");
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('cetak_pdf_header')
                ->label('Cetak PDF (A4)')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->action('printPdf'),
        ];
    }

    public function applyFilters(): void
    {
        $this->filters = $this->form->getState();
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->filters = [
            'dari_tanggal' => Carbon::now()->startOfMonth()->toDateString(),
            'sampai_tanggal' => Carbon::now()->endOfMonth()->toDateString(),
            'kd_bangsal' => null,
        ];
        $this->form->fill($this->filters);
        $this->resetPage();
    }

    public function setFilterThisMonth(): void
    {
        $this->filters['dari_tanggal'] = Carbon::now()->startOfMonth()->toDateString();
        $this->filters['sampai_tanggal'] = Carbon::now()->endOfMonth()->toDateString();
        $this->form->fill($this->filters);
        $this->applyFilters();
    }

    public function setFilterLastMonth(): void
    {
        $this->filters['dari_tanggal'] = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $this->filters['sampai_tanggal'] = Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $this->form->fill($this->filters);
        $this->applyFilters();
    }

    public function setFilterThisYear(): void
    {
        $this->filters['dari_tanggal'] = Carbon::now()->startOfYear()->toDateString();
        $this->filters['sampai_tanggal'] = Carbon::now()->endOfYear()->toDateString();
        $this->form->fill($this->filters);
        $this->applyFilters();
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->tanggal;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DataHais::query()
                    ->when(!empty($this->filters['kd_bangsal']), function ($q) {
                        $q->join('kamar', 'data_HAIs.kd_kamar', '=', 'kamar.kd_kamar')
                          ->where('kamar.kd_bangsal', $this->filters['kd_bangsal']);
                    })
                    ->when(!empty($this->filters['dari_tanggal']), fn($q) => $q->where('data_HAIs.tanggal', '>=', $this->filters['dari_tanggal']))
                    ->when(!empty($this->filters['sampai_tanggal']), fn($q) => $q->where('data_HAIs.tanggal', '<=', $this->filters['sampai_tanggal']))
                    ->groupBy('data_HAIs.tanggal')
                    ->orderBy('data_HAIs.tanggal', 'desc')
                    ->selectRaw('data_HAIs.tanggal,
                        COUNT(data_HAIs.no_rawat) AS jml,
                        SUM(data_HAIs.ETT) AS ETT,
                        SUM(data_HAIs.CVL) AS CVL,
                        SUM(data_HAIs.IVL) AS IVL,
                        SUM(data_HAIs.UC) AS UC,
                        SUM(data_HAIs.VAP) AS VAP,
                        SUM(data_HAIs.IAD) AS IAD,
                        SUM(data_HAIs.PLEB) AS PLEB,
                        SUM(data_HAIs.ISK) AS ISK,
                        SUM(data_HAIs.ILO) AS ILO,
                        SUM(data_HAIs.HAP) AS HAP,
                        SUM(data_HAIs.Tinea) AS Tinea,
                        SUM(data_HAIs.Scabies) AS Scabies,
                        SUM(data_HAIs.DEKU = "IYA") AS DEKU,
                        SUM(data_HAIs.SPUTUM <> "") AS SPUTUM,
                        SUM(data_HAIs.DARAH <> "") AS DARAH,
                        SUM(data_HAIs.URINE <> "") AS URINE,
                        SUM(data_HAIs.ANTIBIOTIK <> "") AS ANTIBIOTIK')
            )
            ->striped()
            ->defaultPaginationPageOption(31)
            ->paginated([10, 31, 50, 100])
            ->filters([])
            ->actions([])
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->weight('bold')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jml')
                    ->label('Jml. Pasien')
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->summarize([
                        Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])
                    ]),

                // GROUP: PEMASANGAN ALAT
                Tables\Columns\ColumnGroup::make(
                    new \Illuminate\Support\HtmlString('
                        <span style="background-color: #0284c7 !important; color: #ffffff !important; padding: 6px 18px; border-radius: 9999px; font-weight: 800; font-size: 12px; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.25); text-shadow: 0 1px 1px rgba(0,0,0,0.2);">
                            <svg style="width: 14px; height: 14px; stroke: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            PEMASANGAN ALAT
                        </span>
                    '),
                    [
                        Tables\Columns\TextColumn::make('ETT')
                            ->label('ETT')
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'border-left: 2px solid #0284c7 !important;'])
                            ->extraCellAttributes(['style' => 'border-left: 2px solid #bae6fd !important;'])
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('CVL')
                            ->label('CVL')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('IVL')
                            ->label('IVL')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('UC')
                            ->label('UC')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                    ]
                )
                    ->alignment(\Filament\Support\Enums\Alignment::Center)
                    ->extraHeaderAttributes(['style' => 'background-color: #f0f9ff !important; border-bottom: 2px solid #0284c7 !important; padding: 8px 0;']),

                // GROUP: INFEKSI HAIs
                Tables\Columns\ColumnGroup::make(
                    new \Illuminate\Support\HtmlString('
                        <span style="background-color: #e11d48 !important; color: #ffffff !important; padding: 6px 18px; border-radius: 9999px; font-weight: 800; font-size: 12px; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.25); text-shadow: 0 1px 1px rgba(0,0,0,0.2);">
                            <svg style="width: 14px; height: 14px; stroke: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            INFEKSI HAIs
                        </span>
                    '),
                    [
                        Tables\Columns\TextColumn::make('VAP')
                            ->label('VAP')
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'border-left: 2px solid #e11d48 !important;'])
                            ->extraCellAttributes(['style' => 'border-left: 2px solid #fecdd3 !important;'])
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('IAD')
                            ->label('IAD')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('PLEB')
                            ->label('PLEB')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('ISK')
                            ->label('ISK/CAUTI')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('ILO')
                            ->label('ILO')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('HAP')
                            ->label('HAP')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('Tinea')
                            ->label('Tinea')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('Scabies')
                            ->label('Scabies')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                    ]
                )
                    ->alignment(\Filament\Support\Enums\Alignment::Center)
                    ->extraHeaderAttributes(['style' => 'background-color: #fff1f2 !important; border-bottom: 2px solid #e11d48 !important; padding: 8px 0;']),

                // DEKUBITUS
                Tables\Columns\TextColumn::make('DEKU')
                    ->label('Dekubitus')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'border-left: 2px solid #d1d5db !important;'])
                    ->extraCellAttributes(['style' => 'border-left: 2px solid #e5e7eb !important;'])
                    ->badge(fn ($state): bool => (int)$state > 0)
                    ->color('warning')
                    ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                    ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),

                // GROUP: KULTUR & ANTIBIOTIK
                Tables\Columns\ColumnGroup::make(
                    new \Illuminate\Support\HtmlString('
                        <span style="background-color: #059669 !important; color: #ffffff !important; padding: 6px 18px; border-radius: 9999px; font-weight: 800; font-size: 12px; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.25); text-shadow: 0 1px 1px rgba(0,0,0,0.2);">
                            <svg style="width: 14px; height: 14px; stroke: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            KULTUR & ANTIBIOTIK
                        </span>
                    '),
                    [
                        Tables\Columns\TextColumn::make('SPUTUM')
                            ->label('Sputum')
                            ->alignCenter()
                            ->extraHeaderAttributes(['style' => 'border-left: 2px solid #059669 !important;'])
                            ->extraCellAttributes(['style' => 'border-left: 2px solid #a7f3d0 !important;'])
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('success')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('DARAH')
                            ->label('Darah')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('success')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('URINE')
                            ->label('Urine')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('success')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                        Tables\Columns\TextColumn::make('ANTIBIOTIK')
                            ->label('Antibiotik')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('success')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')->extraAttributes(['style' => 'color: #000000 !important; font-weight: 800 !important; font-size: 13px !important;'])]),
                    ]
                )
                    ->alignment(\Filament\Support\Enums\Alignment::Center)
                    ->extraHeaderAttributes(['style' => 'background-color: #ecfdf5 !important; border-bottom: 2px solid #059669 !important; padding: 8px 0;']),
            ]);
    }
}
