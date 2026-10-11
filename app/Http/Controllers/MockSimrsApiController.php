<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MockSimrsApiController extends Controller
{
    /**
     * Data Master Ruangan Mock.
     */
    protected array $mockRuangan = [
        [
            'kode_ruangan' => 'B01',
            'nama_ruangan' => 'Ruang Melati (Penyakit Dalam)',
            'kelas' => 'Kelas 1',
            'kapasitas' => 20,
            'status_aktif' => true,
        ],
        [
            'kode_ruangan' => 'B02',
            'nama_ruangan' => 'Ruang Mawar (Bedah)',
            'kelas' => 'Kelas 2',
            'kapasitas' => 25,
            'status_aktif' => true,
        ],
        [
            'kode_ruangan' => 'ICU01',
            'nama_ruangan' => 'Intensive Care Unit (ICU)',
            'kelas' => 'ICU',
            'kapasitas' => 10,
            'status_aktif' => true,
        ],
        [
            'kode_ruangan' => 'NICU01',
            'nama_ruangan' => 'Neonatal ICU (NICU)',
            'kelas' => 'NICU',
            'kapasitas' => 8,
            'status_aktif' => true,
        ],
        [
            'kode_ruangan' => 'B03',
            'nama_ruangan' => 'Ruang Anggrek (VIP)',
            'kelas' => 'VIP',
            'kapasitas' => 12,
            'status_aktif' => true,
        ],
    ];

    /**
     * Info & Katalog Endpoint Mock API Simulator.
     */
    public function info(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'name' => 'SI-HAIs Mock SIMRS Simulator API',
            'version' => '1.0.0',
            'description' => 'Simulator API SIMRS untuk pengujian integrasi & bridging SiHAI\'s PPI tanpa bergantung server eksternal.',
            'base_url' => url('/api/mock-simrs'),
            'endpoints' => [
                'ruangan' => [
                    'method' => 'GET',
                    'path' => '/api/mock-simrs/ruangan',
                    'description' => 'Daftar master ruangan rawat inap',
                ],
                'pasien_ranap_aktif' => [
                    'method' => 'GET',
                    'path' => '/api/mock-simrs/pasien-ranap/aktif?kode_ruangan=B01',
                    'description' => 'Daftar pasien yang sedang aktif dirawat inap',
                ],
                'pasien_ranap_pulang' => [
                    'method' => 'GET',
                    'path' => '/api/mock-simrs/pasien-ranap/pulang?tanggal_awal=2026-10-01&tanggal_akhir=2026-10-11',
                    'description' => 'Daftar pasien rawat inap yang sudah pulang',
                ],
                'search_pasien' => [
                    'method' => 'GET',
                    'path' => '/api/mock-simrs/pasien/search?keyword=001201',
                    'description' => 'Pencarian pasien realtime berdasarkan No RM, No Rawat, atau NIK',
                ],
                'alat_invasif' => [
                    'method' => 'GET',
                    'path' => '/api/mock-simrs/pasien/{no_rawat}/alat-invasif',
                    'description' => 'Riwayat pemasangan alat invasif (IVL, UC, ETT, dll)',
                ],
            ],
        ]);
    }

    /**
     * Endpoint: GET /api/mock-simrs/ruangan
     * Mengembalikan daftar master ruangan SIMRS.
     */
    public function ruangan(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Data master ruangan berhasil diambil.',
            'total' => count($this->mockRuangan),
            'data' => $this->mockRuangan,
        ]);
    }

    /**
     * Endpoint: GET /api/mock-simrs/pasien-ranap/aktif
     * Mengembalikan daftar pasien yang sedang dirawat aktif.
     */
    public function pasienRanapAktif(Request $request): JsonResponse
    {
        $today = now()->format('Y-m-d');
        $threeDaysAgo = now()->subDays(3)->format('Y-m-d H:i:s');
        $twoDaysAgo = now()->subDays(2)->format('Y-m-d H:i:s');
        $yesterday = now()->subDay()->format('Y-m-d H:i:s');

        $pasienList = [
            [
                'no_rawat' => now()->format('Y/m/d').'/000001',
                'no_rkm_medis' => '001201',
                'nama_pasien' => 'Tn. Budi Santoso',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1982-05-14',
                'umur' => '44 Th',
                'nik' => '3501011405820001',
                'alamat' => 'Jl. Mawar No. 12, Surabaya',
                'tanggal_masuk' => $threeDaysAgo,
                'kode_ruangan' => 'B01',
                'nama_ruangan' => 'Ruang Melati (Penyakit Dalam)',
                'kode_kamar' => 'MEL-01A',
                'dokter_penanggung_jawab' => 'dr. Ahmad Pratama, Sp.PD',
            ],
            [
                'no_rawat' => now()->format('Y/m/d').'/000002',
                'no_rkm_medis' => '001202',
                'nama_pasien' => 'Ny. Siti Aminah',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1990-11-23',
                'umur' => '35 Th',
                'nik' => '3501012311900002',
                'alamat' => 'Jl. Kenanga No. 45, Sidoarjo',
                'tanggal_masuk' => $twoDaysAgo,
                'kode_ruangan' => 'B02',
                'nama_ruangan' => 'Ruang Mawar (Bedah)',
                'kode_kamar' => 'MAW-02B',
                'dokter_penanggung_jawab' => 'dr. Siti Rahayu, Sp.B',
            ],
            [
                'no_rawat' => now()->format('Y/m/d').'/000003',
                'no_rkm_medis' => '001203',
                'nama_pasien' => 'Tn. Hartono Wijaya',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1968-02-10',
                'umur' => '58 Th',
                'nik' => '3501011002680003',
                'alamat' => 'Jl. Pahlawan No. 8, Gresik',
                'tanggal_masuk' => $yesterday,
                'kode_ruangan' => 'ICU01',
                'nama_ruangan' => 'Intensive Care Unit (ICU)',
                'kode_kamar' => 'ICU-BED-01',
                'dokter_penanggung_jawab' => 'dr. Bambang Santoso, Sp.An',
            ],
            [
                'no_rawat' => now()->format('Y/m/d').'/000004',
                'no_rkm_medis' => '001204',
                'nama_pasien' => 'An. Rizky Pratama',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => now()->subYears(4)->format('Y-m-d'),
                'umur' => '4 Th',
                'nik' => '3501010101220004',
                'alamat' => 'Jl. Merpati No. 3, Surabaya',
                'tanggal_masuk' => $yesterday,
                'kode_ruangan' => 'B01',
                'nama_ruangan' => 'Ruang Melati (Penyakit Dalam)',
                'kode_kamar' => 'MEL-02A',
                'dokter_penanggung_jawab' => 'dr. Anita, Sp.A',
            ],
            [
                'no_rawat' => now()->format('Y/m/d').'/000005',
                'no_rkm_medis' => '001205',
                'nama_pasien' => 'Ny. Ratna Sari',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1988-08-17',
                'umur' => '38 Th',
                'nik' => '3501011708880005',
                'alamat' => 'Jl. Flamboyan No. 20, Mojokerto',
                'tanggal_masuk' => $twoDaysAgo,
                'kode_ruangan' => 'B03',
                'nama_ruangan' => 'Ruang Anggrek (VIP)',
                'kode_kamar' => 'ANG-VIP-01',
                'dokter_penanggung_jawab' => 'dr. Hendra, Sp.PD',
            ],
        ];

        // Filter berdasarkan kode ruangan bila diminta
        if ($kodeRuangan = $request->query('kode_ruangan')) {
            $pasienList = array_values(array_filter(
                $pasienList,
                fn (array $item) => $item['kode_ruangan'] === $kodeRuangan
            ));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data pasien rawat inap aktif berhasil diambil.',
            'total' => count($pasienList),
            'data' => $pasienList,
        ]);
    }

    /**
     * Endpoint: GET /api/mock-simrs/pasien-ranap/pulang
     * Mengembalikan daftar pasien yang sudah pulang.
     */
    public function pasienRanapPulang(Request $request): JsonResponse
    {
        $pulangList = [
            [
                'no_rawat' => now()->subDays(5)->format('Y/m/d').'/000088',
                'no_rkm_medis' => '001188',
                'nama_pasien' => 'Ny. Dewi Sartika',
                'tanggal_masuk' => now()->subDays(7)->format('Y-m-d 08:00:00'),
                'tanggal_keluar' => now()->subDays(1)->format('Y-m-d 11:30:00'),
                'keadaan_pulang' => 'Sembuh',
                'kode_ruangan_terakhir' => 'B01',
            ],
            [
                'no_rawat' => now()->subDays(6)->format('Y/m/d').'/000089',
                'no_rkm_medis' => '001189',
                'nama_pasien' => 'Tn. Hendro Susilo',
                'tanggal_masuk' => now()->subDays(8)->format('Y-m-d 10:00:00'),
                'tanggal_keluar' => now()->subDays(2)->format('Y-m-d 14:00:00'),
                'keadaan_pulang' => 'Membaik',
                'kode_ruangan_terakhir' => 'B02',
            ],
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Data pasien pulang berhasil diambil.',
            'total' => count($pulangList),
            'data' => $pulangList,
        ]);
    }

    /**
     * Endpoint: GET /api/mock-simrs/pasien/search
     * Pencarian pasien realtime berdasarkan keyword.
     */
    public function searchPasien(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->query('keyword', ''));

        if (blank($keyword)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter keyword wajib diisi.',
                'data' => [],
            ], 422);
        }

        $allPasien = [
            [
                'no_rawat' => now()->format('Y/m/d').'/000001',
                'no_rkm_medis' => '001201',
                'nama_pasien' => 'Tn. Budi Santoso',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1982-05-14',
                'alamat' => 'Jl. Mawar No. 12, Surabaya',
                'tanggal_masuk' => now()->subDays(3)->format('Y-m-d H:i:s'),
                'tanggal_keluar' => null,
                'status_rawat' => 'Masih Dirawat',
                'kode_ruangan' => 'B01',
                'nama_ruangan' => 'Ruang Melati (Penyakit Dalam)',
                'kode_kamar' => 'MEL-01A',
            ],
            [
                'no_rawat' => now()->format('Y/m/d').'/000002',
                'no_rkm_medis' => '001202',
                'nama_pasien' => 'Ny. Siti Aminah',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1990-11-23',
                'alamat' => 'Jl. Kenanga No. 45, Sidoarjo',
                'tanggal_masuk' => now()->subDays(2)->format('Y-m-d H:i:s'),
                'tanggal_keluar' => null,
                'status_rawat' => 'Masih Dirawat',
                'kode_ruangan' => 'B02',
                'nama_ruangan' => 'Ruang Mawar (Bedah)',
                'kode_kamar' => 'MAW-02B',
            ],
            [
                'no_rawat' => now()->format('Y/m/d').'/000003',
                'no_rkm_medis' => '001203',
                'nama_pasien' => 'Tn. Hartono Wijaya',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1968-02-10',
                'alamat' => 'Jl. Pahlawan No. 8, Gresik',
                'tanggal_masuk' => now()->subDay()->format('Y-m-d H:i:s'),
                'tanggal_keluar' => null,
                'status_rawat' => 'Masih Dirawat',
                'kode_ruangan' => 'ICU01',
                'nama_ruangan' => 'Intensive Care Unit (ICU)',
                'kode_kamar' => 'ICU-BED-01',
            ],
        ];

        $keywordLower = strtolower($keyword);
        $filtered = array_values(array_filter($allPasien, function (array $item) use ($keywordLower) {
            return str_contains(strtolower($item['no_rkm_medis']), $keywordLower)
                || str_contains(strtolower($item['nama_pasien']), $keywordLower)
                || str_contains(strtolower($item['no_rawat']), $keywordLower);
        }));

        return response()->json([
            'status' => 'success',
            'message' => 'Pencarian pasien selesai.',
            'total' => count($filtered),
            'data' => $filtered,
        ]);
    }

    /**
     * Endpoint: GET /api/mock-simrs/pasien/{no_rawat}/alat-invasif
     * Riwayat alat invasif pasien.
     */
    public function alatInvasif(Request $request, string $noRawat): JsonResponse
    {
        $alatList = [
            [
                'jenis_alat' => 'IVL',
                'nama_alat' => 'Infus Perifer (IVL)',
                'tanggal_pasang' => now()->subDays(2)->format('Y-m-d 10:00:00'),
                'tanggal_lepas' => null,
                'lokasi' => 'Ekstremitas Atas Kanan',
            ],
            [
                'jenis_alat' => 'UC',
                'nama_alat' => 'Urine Catheter (Dower Catheter)',
                'tanggal_pasang' => now()->subDays(2)->format('Y-m-d 11:30:00'),
                'tanggal_lepas' => null,
                'lokasi' => 'Uretra',
            ],
        ];

        return response()->json([
            'status' => 'success',
            'no_rawat' => $noRawat,
            'message' => 'Data alat invasif berhasil diambil.',
            'data' => $alatList,
        ]);
    }
}
