<?php

namespace Tests\Unit;

use App\Filament\Pages\BridgingTesterPage;
use Tests\TestCase;

class BridgingTesterPageTest extends TestCase
{
    public function test_bridging_tester_page_computes_endpoint_details(): void
    {
        $page = new BridgingTesterPage();
        $page->baseUrl = 'https://simrs.example.test/api/v1';

        $ruangan = $page->getEndpointDetails('ruangan');
        $this->assertSame('ruangan', $ruangan['path']);
        $this->assertSame('https://simrs.example.test/api/v1/ruangan', $ruangan['full_url']);

        $page->paramKodeRuangan = 'B01';
        $aktif = $page->getEndpointDetails('pasien_aktif');
        $this->assertSame('https://simrs.example.test/api/v1/pasien-ranap/aktif?kode_ruangan=B01', $aktif['full_url']);
    }

    public function test_bridging_tester_page_runs_all_tests_against_mock(): void
    {
        $page = new BridgingTesterPage();
        $page->baseUrl = 'http://127.0.0.1:8000/api/mock-simrs';
        $page->runAllTests();

        $this->assertIsArray($page->batchResults);
        $this->assertCount(5, $page->batchResults);

        foreach ($page->batchResults as $endpoint => $result) {
            $this->assertTrue($result['success'], "Endpoint {$endpoint} failed validation.");
            $this->assertSame(200, $result['status_code']);
        }
    }
}
