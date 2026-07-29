<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use App\Models\Setting;
use Filament\Notifications\Notification;

class SettingInstitusi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Institusi';
    protected static ?string $title = 'Setting Institusi';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.setting-institusi';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = Setting::first();
        if ($setting) {
            $this->form->fill($setting->toArray());
        } else {
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Institusi')
                    ->description('Pengaturan data institusi/fasilitas kesehatan.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama_instansi')->label('Nama Instansi')->required(),
                            TextInput::make('alamat_instansi')->label('Alamat')->required(),
                            TextInput::make('kabupaten')->label('Kabupaten'),
                            TextInput::make('propinsi')->label('Provinsi'),
                            TextInput::make('kontak')->label('Kontak'),
                            TextInput::make('email')->label('Email')->email(),
                            TextInput::make('kode_ppk')->label('Kode PPK BPJS'),
                            TextInput::make('kode_ppkinhealth')->label('Kode PPK Inhealth'),
                            TextInput::make('kode_ppkkemenkes')->label('Kode PPK Kemenkes'),
                        ])
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = Setting::first();
        if ($setting) {
            $setting->update($data);
        } else {
            Setting::create($data);
        }

        Notification::make()
            ->success()
            ->title('Tersimpan')
            ->body('Data Institusi berhasil diperbarui.')
            ->send();
    }
}
