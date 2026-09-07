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
            ->filters([])
            ->actions([])
            ->columns([
                Tables\Columns\TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('regPeriksa.pasien.jk')
                    ->label('JK')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ETT')->label('ETT')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('CVL')->label('CVL')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('IVL')->label('IVL')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('UC')->label('UC')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('VAP')->label('VAP')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('IAD')->label('IAD')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('PLEB')->label('PLEB')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('ISK')->label('ISK / CAUTI')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('ILO')->label('ILO')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('HAP')->label('HAP')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('Tinea')->label('Tinea')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('Scabies')->label('Scabies')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('Deku')->label('Deku'),
                Tables\Columns\TextColumn::make('SPUTUM')->label('SPUTUM'),
                Tables\Columns\TextColumn::make('DARAH')->label('DARAH'),
                Tables\Columns\TextColumn::make('URINE')->label('URINE'),
                Tables\Columns\TextColumn::make('ANTIBIOTIK')->label('ANTIBIOTIK'),
                Tables\Columns\TextColumn::make('kamar.bangsal.nm_bangsal')->label('Bangsal'),
            ]);
    }
}
