<?php

namespace Tests\Unit;

use App\Models\BridgingSetting;
use App\Services\SimrsBridgeService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SimrsBridgeServiceTest extends TestCase
{
    public function test_connection_checks_required_endpoint_without_dropping_base_path(): void
    {
        Http::fake([
            'https://simrs.example.test/api/v1/ruangan' => Http::response(['data' => []]),
        ]);

        $setting = new BridgingSetting([
            'base_url' => 'https://simrs.example.test/api/v1',
            'auth_type' => 'bearer',
            'api_token' => 'secret-token',
            'timeout' => 10,
        ]);

        $result = (new SimrsBridgeService($setting))->testConnection();

        $this->assertTrue($result['success']);
        $this->assertSame('success', $setting->last_connection_status);
        Http::assertSent(fn (Request $request) => $request->url() === 'https://simrs.example.test/api/v1/ruangan'
            && $request->hasHeader('Authorization', 'Bearer secret-token'));
    }

    public function test_api_key_authentication_uses_configured_header(): void
    {
        Http::fake([
            'https://simrs.example.test/api/v1/ruangan' => Http::response(['status' => 'success', 'data' => [['kode_ruangan' => 'ICU']]]),
        ]);

        $setting = new BridgingSetting([
            'base_url' => 'https://simrs.example.test/api/v1/',
            'auth_type' => 'api_key',
            'api_key_header' => 'X-SIMRS-KEY',
            'api_token' => 'api-secret',
            'timeout' => 10,
        ]);

        $rooms = (new SimrsBridgeService($setting))->getRuangan();

        $this->assertSame('ICU', $rooms[0]['kode_ruangan']);
        Http::assertSent(fn (Request $request) => $request->hasHeader('X-SIMRS-KEY', 'api-secret'));
    }

    public function test_connection_rejects_non_json_contract(): void
    {
        Http::fake([
            'https://simrs.example.test/api/v1/ruangan' => Http::response('<html>OK</html>', 200),
        ]);

        $setting = new BridgingSetting([
            'base_url' => 'https://simrs.example.test/api/v1',
            'auth_type' => 'none',
            'timeout' => 10,
        ]);

        $result = (new SimrsBridgeService($setting))->testConnection();

        $this->assertFalse($result['success']);
        $this->assertSame('failed', $setting->last_connection_status);
    }
}
