<?php

namespace App\Filament\Resources\PasienResource\Pages;

use App\Filament\Resources\PasienResource;
use App\Models\Pasien;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ManagePasiens extends ManageRecords
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('Pasien Baru')
                ->label('Pasien Baru')
                ->modal('Pasien Baru')
                ->icon('heroicon-o-plus-circle')
                ->modalHeading('Pasien Baru')
                ->action(function (array $data): void {
                    try {
                        // $data['no_rkm_medis'] = Pasien::generateNoRm();
                        $data['umur'] = Pasien::calculateAge($data['tgl_lahir']);
                        // Pasien::create($data);
                        $pasien = new Pasien();
                        $pasien->fill($data);
                        $pasien->save();
                        Notification::make()
                            ->title('Data Pasien Berhasil Disimpan')
                            ->success()
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
                    } catch (\Exception $e) {
                        dd($e->getMessage());
                        Notification::make()
                            ->title('Data Pasien Gagal Disimpan')
                            ->body($e->getMessage())
                            ->danger()
                            // ->icon('heroicon-o-exclamation')
                            // ->iconColor('error')
                            ->send();
                    }
                })
                ->form([
                    TextInput::make('no_rkm_medis')
                        ->label('No Rekam Medis')
                        ->unique()
                        ->validationMessages([
                            'unique' => 'No Rekam Medis sudah terdaftar',
                        ])
                        ->required(),
                    TextInput::make('nm_pasien')
                        ->label('Nama Pasien')
                        ->minLength(3)
                        ->maxLength(50)
                        ->string()
                        ->required()
                        ->validationMessages([
                            'required' => 'Nama Pasien tidak boleh kosong',
                            'string' => 'Nama Pasien harus berupa huruf',
                            'max' => 'Nama Pasien tidak boleh lebih dari 50 karakter',
                            'min' => 'Nama Pasien tidak boleh kurang dari 3 karakter',
                        ]),
                    Select::make('jk')
                        ->label('Jenis Kelamin')
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ])
                        ->required()
                        ->in('L', 'P')
                        ->validationMessages([
                            'required' => 'Jenis Kelamin tidak boleh kosong',
                            'in' => 'Jenis Kelamin tidak valid',
                        ]),
                    // TextInput::make('tmp_lahir')
                    //     ->label('Tempat Lahir')
                    //     ->maxLength(15),
                    DatePicker::make('tgl_lahir')
                        ->label('Tanggal Lahir')
                        ->default(now())
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $birthDate = \Carbon\Carbon::parse($state);
                            $age = $birthDate->age;
                            $month = $birthDate->month;
                            $day = $birthDate->day;
                            $set('umur', $age . ' Th ' . $month . ' Bl ' . $day . ' Hr');
                        })
                        ->required(),
                    TextInput::make('umur')
                        ->disabled()
                        ->label('Umur')
                        ->required()
                        ->maxLength(30),
                    Textarea::make('alamat')
                        ->label('Alamat')
                        ->maxLength(100)
                        ->required(),
                ])
                // ->modalWidth(MaxWidth::Full)
                ->modalCancelActionLabel('Batal')
                ->modalSubmitActionLabel('Simpan'),
        ];
    }

    // public function getTabs(): array
    // {
    //     return [
    //         'Pasien' => Tab::make('Pasien'),
    //         'Ralan' => Tab::make('Ralan'),
    //         'Ranap' => Tab::make('Ranap'),
    //     ];
    // }
}
