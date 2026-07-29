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
        $this->form->fill([
            'dari_tanggal' => Carbon::now()->startOfMonth()->toDateString(),
            'sampai_tanggal' => Carbon::now()->endOfMonth()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(['md' => 5, 'default' => 1])
                            ->schema([
                                TextInput::make('search')->label('Cari Data (Nama/No.Rawat)')->placeholder('Ketik pencarian...')->columnSpan(1),
                                Select::make('kd_bangsal')->label('Bangsal')->options(\App\Models\Bangsal::pluck('nm_bangsal', 'kd_bangsal'))->searchable()->columnSpan(1),
                                DatePicker::make('dari_tanggal')->label('Dari Tanggal')->native(false)->required()->columnSpan(1),
                                DatePicker::make('sampai_tanggal')->label('Sampai Tanggal')->native(false)->required()->columnSpan(1),
                                Actions::make([
                                    Action::make('cari')
                                        ->label('Cari')
                                        ->submit('applyFilters')
                                        ->icon('heroicon-m-magnifying-glass')
                                ])->columnSpan(1)
                            ])
                            ->extraAttributes(['class' => 'items-end'])
                    ])
                    ->compact()
            ])
            ->statePath('filters');
    }

    public function applyFilters(): void
    {
        // This method acts as the form submit trigger.
        // It causes the Livewire component to re-render.
        
        // Since we are using Filament widgets (which are separate Livewire components)
        // we need to dispatch an event to them so they update their data.
        $this->dispatch('updateWidgets');
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?string $navigationLabel = 'HAIs Harian';
    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.hais-harian';

    public function getSubheading(): \Illuminate\Contracts\Support\Htmlable|string|null
    {
        return view('filament.pages.partials.hais-harian-filter');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HaisHarianInfeksiChart::class,
            HaisHarianAlatChart::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'filters' => $this->filters,
        ];
    }

    public function table(Table $table): Table
    {
        \Illuminate\Support\Facades\Log::info('Filters in table:', $this->filters ?? []);
        return $table
            ->query(
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
                Tables\Columns\TextColumn::make('ISK')->label('CAUTI')->summarize([Sum::make()]),
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
