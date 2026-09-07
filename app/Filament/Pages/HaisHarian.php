<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Tabs\Tab;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use App\Filament\Resources\DataHaisResource\Widgets\HaisHarianChart;
use App\Filament\Resources\DataHaisResource\Widgets\HaisHarianInfeksiChart;
use App\Filament\Resources\DataHaisResource\Widgets\HaisHarianAlatChart;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use App\Models\DataHais;

class HaisHarian extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public ?array $filters = [];

    public function mount(): void
    {
        $this->filters = [
            'dari_tanggal' => Carbon::today()->toDateString(),
            'sampai_tanggal' => Carbon::today()->toDateString(),
            'kd_bangsal' => null,
            'search' => null,
        ];
        $this->form->fill($this->filters);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter & Pencarian Laporan')
                    ->description('Saring data surveilans HAIs harian berdasarkan periode tanggal, ruangan, atau nama/rekam medis pasien.')
                    ->icon('heroicon-o-funnel')
                    ->collapsible()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])
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
                                TextInput::make('search')
                                    ->label('Pencarian Pasien')
                                    ->placeholder('Nama / No.RM / No.Rawat...')
                                    ->prefixIcon('heroicon-m-magnifying-glass'),
                            ]),
                    ])
                    ->footerActions([
                        Action::make('hari_ini')
                            ->label('Hari Ini')
                            ->color('gray')
                            ->icon('heroicon-m-calendar-days')
                            ->action('setFilterToday'),
                        Action::make('bulan_ini')
                            ->label('Bulan Ini')
                            ->color('gray')
                            ->icon('heroicon-m-calendar')
                            ->action('setFilterThisMonth'),
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
                    ])
            ])
            ->statePath('filters');
    }

    public function applyFilters(): void
    {
        $this->filters = $this->form->getState();
        $this->resetPage();
        $this->dispatch('updateWidgets', filters: $this->filters);
    }

    public function resetFilters(): void
    {
        $this->filters = [
            'dari_tanggal' => Carbon::today()->toDateString(),
            'sampai_tanggal' => Carbon::today()->toDateString(),
            'kd_bangsal' => null,
            'search' => null,
        ];
        $this->form->fill($this->filters);
        $this->resetPage();
        $this->dispatch('updateWidgets', filters: $this->filters);
    }

    public function setFilterToday(): void
    {
        $this->filters['dari_tanggal'] = Carbon::today()->toDateString();
        $this->filters['sampai_tanggal'] = Carbon::today()->toDateString();
        $this->form->fill($this->filters);
        $this->applyFilters();
    }

    public function setFilterThisMonth(): void
    {
        $this->filters['dari_tanggal'] = Carbon::now()->startOfMonth()->toDateString();
        $this->filters['sampai_tanggal'] = Carbon::now()->endOfMonth()->toDateString();
        $this->form->fill($this->filters);
        $this->applyFilters();
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?string $navigationLabel = 'HAIs Harian';
    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.hais-harian';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder =>
                \App\Models\DataHais::query()
                    ->with('regPeriksa.pasien')
                    ->with('kamar.bangsal')
                    ->when(!empty($this->filters['dari_tanggal']), fn($q) => $q->whereDate('tanggal', '>=', $this->filters['dari_tanggal']))
                    ->when(!empty($this->filters['sampai_tanggal']), fn($q) => $q->whereDate('tanggal', '<=', $this->filters['sampai_tanggal']))
                    ->when(!empty($this->filters['kd_bangsal']), fn($q) => $q->whereHas('kamar', fn($k) => $k->where('kd_bangsal', $this->filters['kd_bangsal'])))
                    ->when(!empty(trim($this->filters['search'] ?? '')), function ($q) {
                        $search = trim($this->filters['search']);
                        $q->where(function ($query) use ($search) {
                            $query->whereHas('regPeriksa.pasien', fn($p) => $p->where('nm_pasien', 'like', "%{$search}%"))
                                  ->orWhereHas('regPeriksa', fn($r) => $r->where('no_rkm_medis', 'like', "%{$search}%"))
                                  ->orWhere('no_rawat', 'like', "%{$search}%")
                                  ->orWhereHas('kamar', fn($k) => $k->where('kd_kamar', 'like', "%{$search}%"));
                        });
                    })
                    ->orderByDesc('tanggal')
            )
            ->striped()
            ->defaultPaginationPageOption(25)
            ->paginated([10, 25, 50, 100])
            ->filters([])
            ->actions([])
            ->columns([
                Tables\Columns\TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Pasien')
                    ->description(fn ($record) => ($record->regPeriksa?->no_rkm_medis ? 'RM: ' . $record->regPeriksa->no_rkm_medis : '') . ($record->no_rawat ? ' • ' . $record->no_rawat : ''))
                    ->weight('medium')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('regPeriksa.pasien.jk')
                    ->label('JK')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'L' => 'info',
                        'P' => 'warning',
                        default => 'gray'
                    })
                    ->sortable(),
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
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('CVL')
                            ->label('CVL')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('IVL')
                            ->label('IVL')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('UC')
                            ->label('UC')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                    ]
                )
                    ->alignment(\Filament\Support\Enums\Alignment::Center)
                    ->extraHeaderAttributes(['style' => 'background-color: #f0f9ff !important; border-bottom: 2px solid #0284c7 !important; padding: 8px 0;']),
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
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('IAD')
                            ->label('IAD')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('PLEB')
                            ->label('PLEB')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('ISK')
                            ->label('ISK/CAUTI')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('ILO')
                            ->label('ILO')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('HAP')
                            ->label('HAP')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('Tinea')
                            ->label('Tinea')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                        Tables\Columns\TextColumn::make('Scabies')
                            ->label('Scabies')
                            ->alignCenter()
                            ->badge(fn ($state): bool => (int)$state > 0)
                            ->color('danger')
                            ->formatStateUsing(fn ($state): string => (int)$state > 0 ? (string)$state : '—')
                            ->summarize([Sum::make()->label('')]),
                    ]
                )
                    ->alignment(\Filament\Support\Enums\Alignment::Center)
                    ->extraHeaderAttributes(['style' => 'background-color: #fff1f2 !important; border-bottom: 2px solid #e11d48 !important; padding: 8px 0;']),
                Tables\Columns\TextColumn::make('Deku')
                    ->label('Dekubitus')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'border-left: 2px solid #d1d5db !important;'])
                    ->extraCellAttributes(['style' => 'border-left: 2px solid #e5e7eb !important;'])
                    ->badge(fn ($state): bool => $state === 'IYA')
                    ->color('warning')
                    ->formatStateUsing(fn ($state): string => $state === 'IYA' ? 'IYA' : '—'),
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
                            ->extraHeaderAttributes(['style' => 'border-left: 2px solid #059669 !important;'])
                            ->extraCellAttributes(['style' => 'border-left: 2px solid #a7f3d0 !important;'])
                            ->placeholder('—')
                            ->limit(15)
                            ->tooltip(fn ($state) => $state),
                        Tables\Columns\TextColumn::make('DARAH')
                            ->label('Darah')
                            ->placeholder('—')
                            ->limit(15)
                            ->tooltip(fn ($state) => $state),
                        Tables\Columns\TextColumn::make('URINE')
                            ->label('Urine')
                            ->placeholder('—')
                            ->limit(15)
                            ->tooltip(fn ($state) => $state),
                        Tables\Columns\TextColumn::make('ANTIBIOTIK')
                            ->label('Antibiotik')
                            ->placeholder('—')
                            ->limit(20)
                            ->tooltip(fn ($state) => $state),
                    ]
                )
                    ->alignment(\Filament\Support\Enums\Alignment::Center)
                    ->extraHeaderAttributes(['style' => 'background-color: #ecfdf5 !important; border-bottom: 2px solid #059669 !important; padding: 8px 0;']),
                Tables\Columns\TextColumn::make('kamar.bangsal.nm_bangsal')
                    ->label('Bangsal')
                    ->description(fn ($record) => $record->kamar?->kd_kamar ? 'Kamar: ' . $record->kamar->kd_kamar : null)
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ]);
    }
}
