<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BridgingMappingResource;
use App\Models\BridgingSetting;
use App\Services\SimrsBridgeService;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class BridgingSettingPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'bridging-setting';

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'Bridging SIMRS';

    protected static ?string $navigationLabel = 'Pengaturan Bridging';

    protected static ?string $title = 'Pengaturan Bridging SIMRS';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.bridging-setting';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public ?array $data = [];

    public ?BridgingSetting $settingRecord = null;

    public function mount(): void
    {
        $this->settingRecord = BridgingSetting::current();
        $data = $this->settingRecord->toArray();
        $data['api_token'] = null;
        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Status & Informasi Sistem')
                    ->description('Status koneksi dan identitas SIMRS yang dihubungkan.')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('is_active')
                                ->label('Aktifkan Bridging SIMRS')
                                ->helperText('Jika aktif, SiHAI\'s akan memprioritaskan pengambilan data dari SIMRS ini.')
                                ->default(false),
                            TextInput::make('simrs_name')
                                ->label('Nama SIMRS / Vendor')
                                ->placeholder('Contoh: SIMRS Medis Utama / Vendor X')
                                ->required()
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Konfigurasi Endpoint REST API')
                    ->description('Tentukan Base URL dan metode autentikasi yang diberikan oleh tim pengembang SIMRS.')
                    ->schema([
                        TextInput::make('base_url')
                            ->label('Base URL API SIMRS')
                            ->placeholder('https://simrs.namafaskes.co.id/api/v1')
                            ->helperText('URL dasar tanpa tanda garis miring di akhir. Contoh: https://simrs.domain.com/api/v1')
                            ->url()
                            ->required()
                            ->maxLength(2048),

                        Grid::make(3)->schema([
                            Select::make('auth_type')
                                ->label('Tipe Autentikasi')
                                ->options([
                                    'bearer' => 'Bearer Token (Header: Authorization)',
                                    'api_key' => 'Custom API Key Header',
                                    'basic' => 'Basic Auth (username:password)',
                                    'none' => 'Tanpa Autentikasi (None)',
                                ])
                                ->default('bearer')
                                ->reactive()
                                ->required(),

                            TextInput::make('api_key_header')
                                ->label('Nama Header API Key')
                                ->placeholder('X-API-KEY / X-SIMRS-KEY')
                                ->default('X-API-KEY')
                                ->rules(['regex:/^[A-Za-z0-9-]+$/'])
                                ->visible(fn (callable $get) => $get('auth_type') === 'api_key')
                                ->required(fn (callable $get) => $get('auth_type') === 'api_key'),

                            TextInput::make('timeout')
                                ->label('Timeout Request (Detik)')
                                ->numeric()
                                ->default(30)
                                ->minValue(1)
                                ->maxValue(120)
                                ->helperText('Batas waktu maksimal menunggu respon dari server SIMRS.')
                                ->required(),
                        ]),

                        TextInput::make('api_token')
                            ->label('Token / API Key / Secret')
                            ->password()
                            ->revealable()
                            ->placeholder('Masukkan token atau kunci rahasia API...')
                            ->helperText('Dikosongkan jika token tidak berubah. Token disimpan terenkripsi.'),
                    ]),

                Section::make('Otomatisasi & Sinkronisasi Berkala')
                    ->description('Sinkronisasi master ruangan berjalan melalui scheduler sesuai interval.')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('auto_sync')
                                ->label('Jalankan Auto-Sync Background')
                                ->helperText('Menyinkronkan master ruangan otomatis saat bridging aktif.')
                                ->default(false),

                            Select::make('auto_sync_interval')
                                ->label('Interval Sinkronisasi')
                                ->options([
                                    5 => 'Setiap 5 Menit',
                                    10 => 'Setiap 10 Menit',
                                    15 => 'Setiap 15 Menit (Direkomendasikan)',
                                    30 => 'Setiap 30 Menit',
                                    60 => 'Setiap 1 Jam',
                                ])
                                ->default(15),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $formData = $this->validatedFormData();
        $this->settingRecord->update($formData);
        $this->data['api_token'] = null;

        Notification::make()
            ->success()
            ->title('Pengaturan Tersimpan')
            ->body('Konfigurasi Bridging SIMRS berhasil diperbarui.')
            ->send();
    }

    public function testConnection(): void
    {
        $formData = $this->validatedFormData();

        // Simpan dulu setting sementara
        $this->settingRecord->update($formData);
        $this->data['api_token'] = null;

        $service = new SimrsBridgeService($this->settingRecord);
        $result = $service->testConnection();

        if ($result['success']) {
            Notification::make()
                ->success()
                ->title('Koneksi Berhasil!')
                ->body("Berhasil terhubung ke SIMRS ({$result['latency_ms']} ms) dengan status HTTP {$result['status_code']}.")
                ->duration(8000)
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('Koneksi Gagal')
                ->body($result['message'])
                ->duration(10000)
                ->send();
        }
    }

    protected function validatedFormData(): array
    {
        $formData = $this->form->getState();
        $formData['base_url'] = rtrim($formData['base_url'], '/');

        if (blank($formData['api_token'] ?? null)) {
            unset($formData['api_token']);
        }

        return $formData;
    }

    public function syncRuanganNow(): void
    {
        try {
            $service = new SimrsBridgeService($this->settingRecord);
            $result = $service->syncRuangan();

            Notification::make()
                ->success()
                ->title('Sinkronisasi Berhasil')
                ->body("{$result['count']} ruangan terpetakan diproses; {$result['unmapped']} belum dipetakan.")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Gagal Sinkronisasi Ruangan')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function useMockSimrs(): void
    {
        $mockUrl = rtrim(config('app.url') ?: url('/'), '/').'/api/mock-simrs';

        $this->settingRecord->update([
            'is_active' => true,
            'simrs_name' => 'Mock SIMRS Simulator (Internal)',
            'base_url' => $mockUrl,
            'auth_type' => 'bearer',
            'api_token' => 'mock-token-simrs-secret',
            'timeout' => 30,
        ]);

        $this->mount();

        Notification::make()
            ->success()
            ->title('Mock SIMRS Simulator Aktif!')
            ->body('Konfigurasi otomatis diarahkan ke simulator internal ('.$mockUrl.'). Silakan klik "Test Koneksi API" untuk menguji.')
            ->duration(10000)
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('use_mock_simrs')
                ->label('Gunakan Mock Simulator')
                ->icon('heroicon-o-beaker')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Gunakan Mock SIMRS Simulator')
                ->modalDescription('Konfigurasi bridging akan otomatis diisi dengan simulator API internal SI-HAIs untuk keperluan testing data ruangan & pasien.')
                ->modalSubmitActionLabel('Ya, Aktifkan Simulator')
                ->action('useMockSimrs'),

            Action::make('mapping_data')
                ->label('Mapping Data')
                ->icon('heroicon-o-arrows-right-left')
                ->color('gray')
                ->url(fn () => BridgingMappingResource::getUrl()),

            Action::make('buka_katalog')
                ->label('Lihat Katalog API')
                ->icon('heroicon-o-book-open')
                ->color('info')
                ->url(fn () => BridgingKatalogPage::getUrl()),

            Action::make('test_koneksi')
                ->label('Test Koneksi API')
                ->icon('heroicon-o-signal')
                ->color('warning')
                ->action('testConnection'),
        ];
    }
}
