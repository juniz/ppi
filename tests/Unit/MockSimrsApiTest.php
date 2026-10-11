<?php

namespace Tests\Unit;

use Tests\TestCase;

class MockSimrsApiTest extends TestCase
{
    public function test_mock_simrs_info_returns_status_and_catalog(): void
    {
        $response = $this->getJson('/api/mock-simrs');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'name' => 'SI-HAIs Mock SIMRS Simulator API',
            ])
            ->assertJsonStructure([
                'status',
                'name',
                'version',
                'base_url',
                'endpoints' => [
                    'ruangan',
                    'pasien_ranap_aktif',
                    'pasien_ranap_pulang',
                    'search_pasien',
                    'alat_invasif',
                ],
            ]);
    }

    public function test_mock_simrs_ruangan_returns_valid_rooms_contract(): void
    {
        $response = $this->getJson('/api/mock-simrs/ruangan');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'kode_ruangan',
                        'nama_ruangan',
                        'kelas',
                        'status_aktif',
                    ],
                ],
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertSame('B01', $data[0]['kode_ruangan']);
    }

    public function test_mock_simrs_pasien_ranap_aktif_returns_patients_contract(): void
    {
        $response = $this->getJson('/api/mock-simrs/pasien-ranap/aktif');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'status',
                'total',
                'data' => [
                    '*' => [
                        'no_rawat',
                        'no_rkm_medis',
                        'nama_pasien',
                        'jenis_kelamin',
                        'tanggal_masuk',
                        'kode_ruangan',
                        'kode_kamar',
                    ],
                ],
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertSame('001201', $data[0]['no_rkm_medis']);
    }

    public function test_mock_simrs_pasien_ranap_aktif_filter_ruangan(): void
    {
        $response = $this->getJson('/api/mock-simrs/pasien-ranap/aktif?kode_ruangan=B02');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        foreach ($data as $item) {
            $this->assertSame('B02', $item['kode_ruangan']);
        }
    }

    public function test_mock_simrs_search_pasien_by_keyword(): void
    {
        $response = $this->getJson('/api/mock-simrs/pasien/search?keyword=Budi');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Budi', $data[0]['nama_pasien']);
    }

    public function test_mock_simrs_alat_invasif_returns_data(): void
    {
        $response = $this->getJson('/api/mock-simrs/pasien/2026-10-11-000001/alat-invasif');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'jenis_alat',
                        'nama_alat',
                        'tanggal_pasang',
                    ],
                ],
            ]);
    }
}
