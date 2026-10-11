<?php

namespace App\Filament\Pages;

use App\Models\BridgingSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use Throwable;

class BridgingTesterPage extends Page
{
    protected static ?string $slug = 'bridging-tester';

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationGroup = 'Bridging SIMRS';

    protected static ?string $navigationLabel = 'Tester API (Playground)';

    protected static ?string $title = 'Tester & Simulasi API Bridging SIMRS';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.bridging-tester';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    // Server Target Properties
    public string $preset = 'active'; // 'active', 'mock', 'custom'
    public string $baseUrl = '';
    public string $authType = 'bearer'; // 'bearer', 'api_key', 'basic', 'none'
    public string $apiToken = '';
    public string $apiKeyHeader = 'X-API-KEY';
    public int $timeout = 15;

    // Selected Endpoint
    public string $selectedEndpoint = 'ruangan'; // 'ruangan', 'pasien_aktif', 'pasien_pulang', 'search', 'alat_invasif', 'custom'

    // Parameters
    public string $paramKodeRuangan = '';
    public string $paramTglAwal = '';
    public string $paramTglAkhir = '';
    public string $paramKeyword = '001201';
    public string $paramNoRawat = '';
    public string $customPath = '';

    // Results
    public ?array $lastResult = null;
    public ?array $batchResults = null;
    public bool $isTestingBatch = false;

    public function mount(): void
    {
        $this->applyPreset('active');
        $this->paramTglAwal = now()->subDays(7)->toDateString();
        $this->paramTglAkhir = now()->toDateString();
        $this->paramNoRawat = now()->format('Y/m/d').'/000001';
    }

    public function applyPreset(string $preset): void
    {
        $this->preset = $preset;

        if ($preset === 'mock') {
            $this->baseUrl = rtrim(config('app.url') ?: url('/'), '/').'/api/mock-simrs';
            $this->authType = 'bearer';
            $this->apiToken = 'mock-token-simrs-secret';
            $this->apiKeyHeader = 'X-API-KEY';
            $this->timeout = 15;
            Notification::make()
                ->info()
                ->title('Preset Mock Simulator Dipilih')
                ->body('Target dialihkan ke Mock SIMRS Simulator internal.')
                ->send();
            return;
        }

        if ($preset === 'active') {
            $setting = BridgingSetting::current();
            $this->baseUrl = $setting->base_url ?: rtrim(config('app.url') ?: url('/'), '/').'/api/mock-simrs';
            $this->authType = $setting->auth_type ?: 'bearer';
            $this->apiToken = $setting->api_token ?: '';
            $this->apiKeyHeader = $setting->api_key_header ?: 'X-API-KEY';
            $this->timeout = $setting->timeout ?: 15;
            Notification::make()
                ->info()
                ->title('Preset Pengaturan Aktif Dipilih')
                ->body('Target membaca konfigurasi dari menu Pengaturan Bridging.')
                ->send();
            return;
        }

        // custom: biarkan nilai saat ini tetap
        Notification::make()
            ->info()
            ->title('Mode Kustom')
            ->body('Anda dapat mengubah Base URL dan token secara bebas.')
            ->send();
    }

    public function selectEndpoint(string $endpoint): void
    {
        $this->selectedEndpoint = $endpoint;
        $this->lastResult = null;
    }

    /**
     * Hitung URL lengkap dan parameter query untuk endpoint terpilih.
     */
    public function getEndpointDetails(string $endpoint): array
    {
        $path = '';
        $params = [];

        switch ($endpoint) {
            case 'ruangan':
                $path = 'ruangan';
                break;
            case 'pasien_aktif':
                $path = 'pasien-ranap/aktif';
                if (! empty(trim($this->paramKodeRuangan))) {
                    $params['kode_ruangan'] = trim($this->paramKodeRuangan);
                }
                break;
            case 'pasien_pulang':
                $path = 'pasien-ranap/pulang';
                if (! empty($this->paramTglAwal)) {
                    $params['tanggal_awal'] = $this->paramTglAwal;
                }
                if (! empty($this->paramTglAkhir)) {
                    $params['tanggal_akhir'] = $this->paramTglAkhir;
                }
                break;
            case 'search':
                $path = 'pasien/search';
                $params['keyword'] = trim($this->paramKeyword) ?: '001201';
                break;
            case 'alat_invasif':
                $noRawat = trim($this->paramNoRawat) ?: (now()->format('Y/m/d').'/000001');
                $path = 'pasien/'.rawurlencode($noRawat).'/alat-invasif';
                break;
            case 'custom':
                $path = ltrim(trim($this->customPath), '/');
                break;
        }

        $cleanBase = rtrim($this->baseUrl, '/');
        $fullUrl = $cleanBase.'/'.$path;
        if (! empty($params)) {
            $fullUrl .= '?'.http_build_query($params);
        }

        return [
            'path' => $path,
            'params' => $params,
            'full_url' => $fullUrl,
        ];
    }

    public function sendRequest(): void
    {
        $this->batchResults = null;
        $details = $this->getEndpointDetails($this->selectedEndpoint);

        $this->lastResult = $this->executeSingleTest(
            $this->selectedEndpoint,
            $details['path'],
            $details['params'],
            $details['full_url']
        );

        if ($this->lastResult['success']) {
            Notification::make()
                ->success()
                ->title('Request Sukses ('.$this->lastResult['status_code'].')')
                ->body('Respon diterima dalam '.$this->lastResult['latency_ms'].' ms.')
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('Request Gagal ('.($this->lastResult['status_code'] ?: 'Error').')')
                ->body($this->lastResult['message'])
                ->duration(8000)
                ->send();
        }
    }

    public function runAllTests(): void
    {
        $this->lastResult = null;
        $endpoints = [
            'ruangan' => 'Master Ruangan (/ruangan)',
            'pasien_aktif' => 'Pasien Ranap Aktif (/pasien-ranap/aktif)',
            'pasien_pulang' => 'Pasien Pulang (/pasien-ranap/pulang)',
            'search' => 'Pencarian Pasien (/pasien/search)',
            'alat_invasif' => 'Alat Invasif Pasien (/pasien/{no_rawat}/alat-invasif)',
        ];

        $results = [];
        $allSuccess = true;

        foreach ($endpoints as $key => $title) {
            $details = $this->getEndpointDetails($key);
            $res = $this->executeSingleTest($key, $details['path'], $details['params'], $details['full_url']);
            $res['title'] = $title;
            $results[$key] = $res;

            if (! $res['success']) {
                $allSuccess = false;
            }
        }

        $this->batchResults = $results;

        if ($allSuccess) {
            Notification::make()
                ->success()
                ->title('Semua Tes Berhasil! (5/5)')
                ->body('Seluruh endpoint merespons dengan status 200 OK dan kontrak data valid.')
                ->duration(8000)
                ->send();
        } else {
            Notification::make()
                ->warning()
                ->title('Beberapa Endpoint Memiliki Kendala')
                ->body('Periksa rincian status endpoint pada tabel hasil pengujian di bawah.')
                ->duration(8000)
                ->send();
        }
    }

    protected function executeSingleTest(string $endpointKey, string $path, array $params, string $fullUrl): array
    {
        $cleanBase = rtrim($this->baseUrl, '/');

        if (blank($cleanBase)) {
            return [
                'endpoint_key' => $endpointKey,
                'success' => false,
                'status_code' => null,
                'status_text' => 'Invalid URL',
                'latency_ms' => 0,
                'full_url' => $fullUrl,
                'headers_sent' => [],
                'response_body' => '',
                'response_headers' => [],
                'data_count' => 0,
                'contract_checks' => [
                    ['label' => 'URL Valid', 'passed' => false, 'note' => 'Base URL belum diisi.'],
                ],
                'message' => 'Base URL tidak boleh kosong.',
            ];
        }

        $headersSent = [
            'Accept' => 'application/json',
        ];

        $client = Http::baseUrl($cleanBase.'/')
            ->timeout($this->timeout ?: 15)
            ->acceptJson();

        switch ($this->authType) {
            case 'bearer':
                if (! empty($this->apiToken)) {
                    $client = $client->withToken($this->apiToken);
                    $headersSent['Authorization'] = 'Bearer '.substr($this->apiToken, 0, 8).'***';
                }
                break;
            case 'api_key':
                $headerName = $this->apiKeyHeader ?: 'X-API-KEY';
                if (! empty($this->apiToken)) {
                    $client = $client->withHeaders([$headerName => $this->apiToken]);
                    $headersSent[$headerName] = substr($this->apiToken, 0, 8).'***';
                }
                break;
            case 'basic':
                if (! empty($this->apiToken)) {
                    $parts = explode(':', $this->apiToken, 2);
                    $user = $parts[0] ?? '';
                    $pass = $parts[1] ?? '';
                    $client = $client->withBasicAuth($user, $pass);
                    $headersSent['Authorization'] = 'Basic <credentials>';
                }
                break;
            case 'none':
            default:
                break;
        }

        $startTime = microtime(true);

        try {
            $response = $client->get($path, $params);
            $latency = (int) round((microtime(true) - $startTime) * 1000);

            $statusCode = $response->status();
            $statusText = $response->reason();
            $rawBody = $response->body();
            $headers = $response->headers();

            $decoded = null;
            $isJson = false;
            try {
                $decoded = $response->json();
                $isJson = is_array($decoded);
            } catch (Throwable) {
                $isJson = false;
            }

            $prettyJson = $isJson ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $rawBody;

            // Validasi Kontrak
            $checks = [];

            // 1. HTTP 200 OK
            $checks[] = [
                'label' => 'HTTP Status 200 OK',
                'passed' => $statusCode === 200,
                'note' => "Status yang diterima: {$statusCode} {$statusText}",
            ];

            // 2. Format JSON
            $checks[] = [
                'label' => 'Format Respon JSON',
                'passed' => $isJson,
                'note' => $isJson ? 'Respon berupa JSON yang valid' : 'Respon bukan format JSON valid',
            ];

            // 3. Field data berupa Array
            $payloadData = is_array($decoded) ? ($decoded['data'] ?? $decoded) : null;
            $hasArrayData = is_array($payloadData);
            $dataCount = $hasArrayData ? count($payloadData) : 0;

            $checks[] = [
                'label' => 'Field data berupa Array',
                'passed' => $hasArrayData,
                'note' => $hasArrayData ? "Ditemukan {$dataCount} record data" : 'Field data tidak berupa array',
            ];

            // 4. Verifikasi field spesifik sesuai endpoint
            $fieldCheck = $this->verifyContractFields($endpointKey, $payloadData);
            $checks[] = $fieldCheck;

            $success = ($statusCode === 200) && $isJson && $hasArrayData && $fieldCheck['passed'];

            return [
                'endpoint_key' => $endpointKey,
                'success' => $success,
                'status_code' => $statusCode,
                'status_text' => $statusText,
                'latency_ms' => $latency,
                'full_url' => $fullUrl,
                'headers_sent' => $headersSent,
                'response_body' => $prettyJson,
                'response_headers' => $headers,
                'data_count' => $dataCount,
                'contract_checks' => $checks,
                'message' => $success
                    ? "Berhasil terhubung ({$latency} ms, {$dataCount} record)"
                    : "Respon tidak sesuai standar (HTTP {$statusCode})",
            ];
        } catch (Throwable $e) {
            $latency = (int) round((microtime(true) - $startTime) * 1000);

            return [
                'endpoint_key' => $endpointKey,
                'success' => false,
                'status_code' => null,
                'status_text' => 'Connection Failed',
                'latency_ms' => $latency,
                'full_url' => $fullUrl,
                'headers_sent' => $headersSent,
                'response_body' => $e->getMessage(),
                'response_headers' => [],
                'data_count' => 0,
                'contract_checks' => [
                    ['label' => 'Konektivitas Jaringan', 'passed' => false, 'note' => 'Gagal menghubungi server: '.$e->getMessage()],
                ],
                'message' => 'Gagal terhubung: '.$e->getMessage(),
            ];
        }
    }

    protected function verifyContractFields(string $endpointKey, mixed $data): array
    {
        if (! is_array($data) || empty($data)) {
            return [
                'label' => 'Struktur Field Kontrak',
                'passed' => true,
                'note' => 'Data kosong (0 record), format array valid.',
            ];
        }

        $sample = $data[0] ?? [];
        if (! is_array($sample)) {
            return [
                'label' => 'Struktur Field Kontrak',
                'passed' => false,
                'note' => 'Item data pertama bukan object/array.',
            ];
        }

        switch ($endpointKey) {
            case 'ruangan':
                $hasCode = isset($sample['kode_ruangan']) || isset($sample['kd_bangsal']) || isset($sample['id']);
                $hasName = isset($sample['nama_ruangan']) || isset($sample['nm_bangsal']) || isset($sample['nama']);
                return [
                    'label' => 'Field Wajib Ruangan',
                    'passed' => $hasCode && $hasName,
                    'note' => ($hasCode && $hasName) ? 'Field kode & nama ruangan ditemukan.' : 'Field kode_ruangan / nama_ruangan tidak ditemukan.',
                ];

            case 'pasien_aktif':
            case 'search':
                $hasRawat = isset($sample['no_rawat']);
                $hasRm = isset($sample['no_rkm_medis']);
                return [
                    'label' => 'Field Wajib Pasien',
                    'passed' => $hasRawat && $hasRm,
                    'note' => ($hasRawat && $hasRm) ? 'Field no_rawat & no_rkm_medis ditemukan.' : 'Field no_rawat / no_rkm_medis tidak ditemukan.',
                ];

            case 'pasien_pulang':
                $hasRawat = isset($sample['no_rawat']);
                $hasKeluar = isset($sample['tanggal_keluar']) || isset($sample['tgl_keluar']);
                return [
                    'label' => 'Field Wajib Pasien Pulang',
                    'passed' => $hasRawat && $hasKeluar,
                    'note' => ($hasRawat && $hasKeluar) ? 'Field no_rawat & tanggal_keluar ditemukan.' : 'Field no_rawat / tanggal_keluar tidak ditemukan.',
                ];

            case 'alat_invasif':
                $hasAlat = isset($sample['jenis_alat']) || isset($sample['nama_alat']);
                return [
                    'label' => 'Field Wajib Alat Invasif',
                    'passed' => $hasAlat,
                    'note' => $hasAlat ? 'Field jenis_alat ditemukan.' : 'Field jenis_alat tidak ditemukan.',
                ];

            default:
                return [
                    'label' => 'Struktur Field Kontrak',
                    'passed' => true,
                    'note' => 'Validasi kustom selesai.',
                ];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('run_all_tests')
                ->label('Jalankan Semua Tes Endpoint')
                ->icon('heroicon-o-play')
                ->color('success')
                ->action('runAllTests'),

            Action::make('ke_pengaturan')
                ->label('Pengaturan Bridging')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->url(fn () => BridgingSettingPage::getUrl()),

            Action::make('ke_katalog')
                ->label('Katalog API')
                ->icon('heroicon-o-book-open')
                ->color('info')
                ->url(fn () => BridgingKatalogPage::getUrl()),
        ];
    }
}
