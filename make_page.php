<?php
$content = <<<'PHP'
<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\DataHais;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use App\Filament\Widgets\HaisPerPasienChart;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

class HaisPerPasien extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $title = 'HAIs Per Pasien';
    protected static ?string $slug = 'hais-per-pasien';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?int $navigationSort = 3;

    protected function getHeaderWidgets(): array
    {
        return [
            HaisPerPasienChart::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DataHais::query()->with(['regPeriksa.pasien'])
            )
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ETT')
                    ->label('ETT')
                    ->alignCenter(),
                TextColumn::make('CVL')
                    ->label('CVL')
                    ->alignCenter(),
                TextColumn::make('IVL')
                    ->label('IVL')
                    ->alignCenter(),
                TextColumn::make('UC')
                    ->label('UC')
                    ->alignCenter(),
                IconColumn::make('VAP')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),
                IconColumn::make('IAD')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),
                IconColumn::make('PLEB')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),
                IconColumn::make('ISK')
                    ->label('CAUTI')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),
                IconColumn::make('ILO')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),
                IconColumn::make('HAP')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal'),
                        DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
            ])
            ->defaultSort('tanggal', 'desc')
            ->striped();
    }
}
PHP;

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Pages/HaisPerPasien.php', $content);
echo "Page updated.\n";
