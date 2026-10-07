<x-filament-panels::page>
    <style>
        .bridging-json-preview {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border: 1px solid #334155;
            color-scheme: dark;
            white-space: pre;
        }

        .bridging-catalog-hero {
            background: linear-gradient(135deg, #0f172a 0%, #172033 65%, #292014 100%) !important;
            color: #f8fafc !important;
        }

        .bridging-catalog-hero h2,
        .bridging-catalog-hero p,
        .bridging-catalog-hero label,
        .bridging-catalog-hero a,
        .bridging-catalog-hero span {
            color: inherit !important;
        }

        .bridging-catalog-hero input {
            background-color: rgba(15, 23, 42, 0.72) !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        .bridging-copy-button,
        .bridging-copy-button span {
            background-color: #fbbf24 !important;
            color: #0f172a !important;
        }

        .bridging-copy-button:hover {
            background-color: #fcd34d !important;
        }
    </style>
    <div class="bridging-catalog-hero relative overflow-hidden rounded-2xl p-6 shadow-sm sm:p-8">
        <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-amber-400/20 blur-3xl"></div>
        <div class="relative grid gap-6 lg:grid-cols-[1fr_22rem] lg:items-end">
            <div class="max-w-2xl">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-amber-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    REST API · Versi 1.0
                </div>
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Katalog Bridging SIMRS</h2>
                <p class="mt-3 text-sm leading-6 text-slate-300 sm:text-base">
                    Referensi teknis untuk menghubungkan data SIMRS ke SiHAI's. Bagikan dokumentasi publik ini kepada tim pengembang vendor.
                </p>
                <div class="mt-5 flex flex-wrap gap-2 text-xs font-medium text-slate-300">
                    <span class="rounded-lg bg-white/5 px-3 py-2">5 endpoint</span>
                    <span class="rounded-lg bg-white/5 px-3 py-2">JSON response</span>
                    <span class="rounded-lg bg-white/5 px-3 py-2">Bearer / API key</span>
                </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                <label for="publicDocsUrlInput" class="text-xs font-semibold text-slate-300">Dokumentasi publik</label>
                <div class="mt-2 flex min-w-0 gap-2">
                    <input type="text" readonly value="{{ $publicDocsUrl }}" id="publicDocsUrlInput"
                        class="min-w-0 flex-1 rounded-lg border border-white/10 bg-black/20 px-3 py-2.5 text-xs text-slate-200 focus:border-amber-400 focus:ring-amber-400" />
                    <button type="button" onclick="copyPublicDocsUrl()"
                        class="bridging-copy-button inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-bold transition focus:outline-none focus:ring-2 focus:ring-amber-300">
                        <x-heroicon-m-clipboard class="h-4 w-4" />
                        <span id="copyBtnText">Salin</span>
                    </button>
                </div>
                <a href="{{ $publicDocsUrl }}" target="_blank" rel="noopener noreferrer"
                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-amber-300 transition hover:text-amber-200">
                    Buka dokumentasi publik
                    <x-heroicon-m-arrow-top-right-on-square class="h-4 w-4" />
                </a>
            </div>
        </div>
    </div>

    <nav aria-label="Navigasi endpoint" class="sticky top-4 z-10 -mx-1 overflow-x-auto px-1 pb-1">
        <div class="flex min-w-max gap-2 rounded-xl border border-gray-200 bg-white/95 p-2 shadow-sm backdrop-blur">
            <a href="#ruangan" class="rounded-lg bg-primary-50 px-3 py-2 text-xs font-semibold text-primary-700 hover:bg-primary-100">Ruangan</a>
            <a href="#ranap" class="rounded-lg px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 hover:text-gray-900">Ranap aktif</a>
            <a href="#pencarian" class="rounded-lg px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 hover:text-gray-900">Pencarian pasien</a>
            <a href="#pulang" class="rounded-lg px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 hover:text-gray-900">Pasien pulang</a>
            <a href="#alat-invasif" class="rounded-lg px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 hover:text-gray-900">Alat invasif</a>
        </div>
    </nav>

    {{-- Pengantar & Autentikasi Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400">
                <x-heroicon-o-shield-check class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Ketentuan Umum & Autentikasi</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Standar komunikasi data antara SiHAI's (Sistem Surveilans PPI) dan SIMRS</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 pt-2 md:grid-cols-3">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800">
                <div class="text-xs font-semibold text-gray-500 uppercase">Format Data</div>
                <div class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1">JSON (RESTful API)</div>
                <div class="text-xs text-gray-500 mt-1">Content-Type: application/json</div>
            </div>
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800">
                <div class="text-xs font-semibold text-gray-500 uppercase">Autentikasi Header</div>
                <div class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1">Bearer Token / API Key</div>
                <div class="text-xs text-gray-500 mt-1">Authorization: Bearer &lt;token&gt;</div>
            </div>
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800">
                <div class="text-xs font-semibold text-gray-500 uppercase">Format Tanggal</div>
                <div class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1">ISO / YYYY-MM-DD</div>
                <div class="text-xs text-gray-500 mt-1">Contoh: 2026-10-07 14:30:00</div>
            </div>
        </div>
    </div>

    {{-- Daftar Endpoint Interaktif --}}
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <x-heroicon-o-list-bullet class="w-5 h-5 text-primary-500" />
                Daftar Endpoint yang Dibutuhkan oleh SiHAI's
            </h3>
            <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">4 wajib · 1 opsional · 3 berjalan</span>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-300">
            <strong>Status implementasi:</strong> endpoint <em>Ruangan</em>, <em>Ranap aktif</em>, dan <em>Pasien pulang</em> sudah dikonsumsi SiHAI's (berjalan). Endpoint <em>Pencarian pasien</em> dan <em>Alat invasif</em> masih berupa spesifikasi/roadmap.
        </div>

        {{-- 1. Master Ruangan --}}
        <div id="ruangan" class="scroll-mt-24 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden transition hover:border-primary-200 hover:shadow-md">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <span class="px-2.5 py-1 rounded-md text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">GET</span>
                    <code class="break-all text-sm font-semibold text-gray-900 dark:text-white">/api/v1/ruangan</code>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 px-2 py-0.5 rounded">Berjalan</span>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 px-2 py-0.5 rounded">Wajib</span>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Mengambil seluruh daftar bangsal/ruangan rawat inap untuk pemetaan unit surveilans infeksi di SiHAI's.
                </p>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Contoh Response JSON:</span>
                        <button type="button" onclick="copySnippet('json_ruangan')" class="text-xs text-primary-600 hover:underline flex items-center gap-1">
                            <x-heroicon-m-clipboard class="w-3.5 h-3.5" /> Salin JSON
                        </button>
                    </div>
                    <pre id="json_ruangan" class="bridging-json-preview p-4 rounded-xl text-xs overflow-x-auto font-mono leading-relaxed">{
  "status": "success",
  "data": [
    {
      "kode_ruangan": "B01",
      "nama_ruangan": "Ruang Melati",
      "kelas": "Kelas 1",
      "status_aktif": true
    },
    {
      "kode_ruangan": "ICU01",
      "nama_ruangan": "Intensive Care Unit (ICU)",
      "kelas": "ICU",
      "status_aktif": true
    }
  ]
}</pre>
                </div>
            </div>
        </div>

        {{-- 2. Pasien Rawat Inap Aktif --}}
        <div id="ranap" class="scroll-mt-24 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden transition hover:border-primary-200 hover:shadow-md">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <span class="px-2.5 py-1 rounded-md text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">GET</span>
                    <code class="break-all text-sm font-semibold text-gray-900 dark:text-white">/api/v1/pasien-ranap/aktif</code>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 px-2 py-0.5 rounded">Berjalan</span>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 px-2 py-0.5 rounded">Wajib Utama</span>
                </div>
                <div class="text-xs text-gray-500">Kebutuhan: Sensus Harian & Surveilans Pasien</div>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Menarik seluruh pasien yang sedang aktif dirawat inap saat ini beserta lokasi ruangan dan nomor rawatnya.
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="p-2.5 border-b">Parameter</th>
                                <th class="p-2.5 border-b">Tipe</th>
                                <th class="p-2.5 border-b">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr>
                                <td class="p-2.5 font-mono text-primary-600">kode_ruangan</td>
                                <td class="p-2.5 text-gray-500">String (Opsional)</td>
                                <td class="p-2.5 text-gray-600 dark:text-gray-300">Filter berdasarkan kode ruangan tertentu.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Contoh Response JSON:</span>
                        <button type="button" onclick="copySnippet('json_ranap')" class="text-xs text-primary-600 hover:underline flex items-center gap-1">
                            <x-heroicon-m-clipboard class="w-3.5 h-3.5" /> Salin JSON
                        </button>
                    </div>
                    <pre id="json_ranap" class="bridging-json-preview p-4 rounded-xl text-xs overflow-x-auto font-mono leading-relaxed">{
  "status": "success",
  "total": 2,
  "data": [
    {
      "no_rawat": "2026/10/07/000123",
      "no_rkm_medis": "00123456",
      "nama_pasien": "Budi Santoso",
      "jenis_kelamin": "L",
      "tanggal_lahir": "1985-04-12",
      "umur": "41 Th",
      "nik": "3501xxxxxxxxxxxx",
      "tanggal_masuk": "2026-10-05 14:30:00",
      "kode_ruangan": "B01",
      "nama_ruangan": "Ruang Melati",
      "kode_kamar": "MEL-01A",
      "dokter_penanggung_jawab": "dr. Ahmad, Sp.PD"
    }
  ]
}</pre>
                </div>
            </div>
        </div>

        {{-- 3. Pencarian Pasien Realtime --}}
        <div id="pencarian" class="scroll-mt-24 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden transition hover:border-primary-200 hover:shadow-md">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <span class="px-2.5 py-1 rounded-md text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">GET</span>
                    <code class="break-all text-sm font-semibold text-gray-900 dark:text-white">/api/v1/pasien/search</code>
                    <span class="text-xs font-medium text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">Belum Dikonsumsi</span>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 px-2 py-0.5 rounded">Wajib</span>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Digunakan saat petugas mengetik No. RM atau No. Rawat di form input SiHAI's agar data pasien otomatis terisi.
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="p-2.5 border-b">Parameter</th>
                                <th class="p-2.5 border-b">Tipe</th>
                                <th class="p-2.5 border-b">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr>
                                <td class="p-2.5 font-mono text-primary-600">keyword</td>
                                <td class="p-2.5 text-gray-500">String (Wajib)</td>
                                <td class="p-2.5 text-gray-600 dark:text-gray-300">Bisa berupa No. Rekam Medis (No RM), No. Rawat, atau NIK.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Contoh Response JSON:</span>
                        <button type="button" onclick="copySnippet('json_search')" class="text-xs text-primary-600 hover:underline flex items-center gap-1">
                            <x-heroicon-m-clipboard class="w-3.5 h-3.5" /> Salin JSON
                        </button>
                    </div>
                    <pre id="json_search" class="bridging-json-preview p-4 rounded-xl text-xs overflow-x-auto font-mono leading-relaxed">{
  "status": "success",
  "data": [
    {
      "no_rawat": "2026/10/07/000123",
      "no_rkm_medis": "00123456",
      "nama_pasien": "Budi Santoso",
      "jenis_kelamin": "L",
      "tanggal_lahir": "1985-04-12",
      "alamat": "Jl. Mawar No. 10",
      "tanggal_masuk": "2026-10-05 14:30:00",
      "tanggal_keluar": null,
      "status_rawat": "Masih Dirawat",
      "kode_ruangan": "B01",
      "nama_ruangan": "Ruang Melati",
      "kode_kamar": "MEL-01A"
    }
  ]
}</pre>
                </div>
            </div>
        </div>

        {{-- 4. Pasien Pulang --}}
        <div id="pulang" class="scroll-mt-24 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden transition hover:border-primary-200 hover:shadow-md">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <span class="px-2.5 py-1 rounded-md text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">GET</span>
                    <code class="break-all text-sm font-semibold text-gray-900 dark:text-white">/api/v1/pasien-ranap/pulang</code>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 px-2 py-0.5 rounded">Berjalan</span>
                    <span class="text-xs font-medium text-amber-600 bg-amber-50 dark:bg-amber-950/50 dark:text-amber-400 px-2 py-0.5 rounded">Penting</span>
                </div>
                <div class="text-xs text-gray-500">Kebutuhan: Perhitungan Denominator Hari Rawat</div>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Mendapatkan daftar pasien yang selesai dirawat / pulang pada rentang tanggal tertentu untuk memperbarui status keluar.
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="p-2.5 border-b">Parameter</th>
                                <th class="p-2.5 border-b">Tipe</th>
                                <th class="p-2.5 border-b">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr>
                                <td class="p-2.5 font-mono text-primary-600">tanggal_awal</td>
                                <td class="p-2.5 text-gray-500">Date (YYYY-MM-DD)</td>
                                <td class="p-2.5 text-gray-600 dark:text-gray-300">Batas awal tanggal kepulangan pasien.</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-mono text-primary-600">tanggal_akhir</td>
                                <td class="p-2.5 text-gray-500">Date (YYYY-MM-DD)</td>
                                <td class="p-2.5 text-gray-600 dark:text-gray-300">Batas akhir tanggal kepulangan pasien.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Contoh Response JSON:</span>
                        <button type="button" onclick="copySnippet('json_pulang')" class="text-xs text-primary-600 hover:underline flex items-center gap-1">
                            <x-heroicon-m-clipboard class="w-3.5 h-3.5" /> Salin JSON
                        </button>
                    </div>
                    <pre id="json_pulang" class="bridging-json-preview p-4 rounded-xl text-xs overflow-x-auto font-mono leading-relaxed">{
  "status": "success",
  "data": [
    {
      "no_rawat": "2026/10/01/000045",
      "no_rkm_medis": "00098765",
      "nama_pasien": "Siti Aminah",
      "tanggal_masuk": "2026-10-01 09:00:00",
      "tanggal_keluar": "2026-10-06 11:30:00",
      "keadaan_pulang": "Sembuh",
      "kode_ruangan_terakhir": "B01"
    }
  ]
}</pre>
                </div>
            </div>
        </div>

        {{-- 5. Alat Invasif --}}
        <div id="alat-invasif" class="scroll-mt-24 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden transition hover:border-primary-200 hover:shadow-md">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <span class="px-2.5 py-1 rounded-md text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">GET</span>
                    <code class="break-all text-sm font-semibold text-gray-900 dark:text-white">/api/v1/pasien/{no_rawat}/alat-invasif</code>
                    <span class="text-xs font-medium text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">Belum Dikonsumsi</span>
                    <span class="text-xs font-medium text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">Opsional / Nilai Tambah</span>
                </div>
                <div class="text-xs text-gray-500">Kebutuhan: Otomatisasi Denominator Alat (VAP, IAD, PLEB, ISK)</div>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Jika di SIMRS perawat sudah mencatat tanggal pasang/lepas infus, kateter, ventilator, data ini dapat ditarik langsung sehingga petugas PPI tidak perlu input manual.
                </p>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Contoh Response JSON:</span>
                        <button type="button" onclick="copySnippet('json_alat')" class="text-xs text-primary-600 hover:underline flex items-center gap-1">
                            <x-heroicon-m-clipboard class="w-3.5 h-3.5" /> Salin JSON
                        </button>
                    </div>
                    <pre id="json_alat" class="bridging-json-preview p-4 rounded-xl text-xs overflow-x-auto font-mono leading-relaxed">{
  "status": "success",
  "no_rawat": "2026/10/07/000123",
  "data": [
    {
      "jenis_alat": "IVL",
      "nama_alat": "Infus Perifer",
      "tanggal_pasang": "2026-10-05 15:00:00",
      "tanggal_lepas": null,
      "lokasi": "Tangan Kanan"
    },
    {
      "jenis_alat": "UC",
      "nama_alat": "Kateter Urin",
      "tanggal_pasang": "2026-10-05 16:30:00",
      "tanggal_lepas": "2026-10-07 10:00:00",
      "lokasi": "-"
    }
  ]
}</pre>
                </div>
            </div>
        </div>
    </div>

    <div id="copyToast" role="status" aria-live="polite"
        class="pointer-events-none fixed bottom-6 right-6 z-50 hidden items-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-xl">
        <x-heroicon-m-check-circle class="h-5 w-5 text-emerald-400" />
        <span id="copyToastText">Berhasil disalin</span>
    </div>

    <script>
        async function copyText(text, message) {
            try {
                await navigator.clipboard.writeText(text);
            } catch (error) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                textarea.remove();
            }

            const toast = document.getElementById('copyToast');
            document.getElementById('copyToastText').innerText = message;
            toast.classList.remove('hidden');
            toast.classList.add('flex');
            window.clearTimeout(window.bridgingCopyToastTimer);
            window.bridgingCopyToastTimer = window.setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('flex');
            }, 2200);
        }

        async function copyPublicDocsUrl() {
            const input = document.getElementById('publicDocsUrlInput');
            const buttonText = document.getElementById('copyBtnText');
            await copyText(input.value, 'Link dokumentasi berhasil disalin');
            buttonText.innerText = 'Tersalin';
            window.setTimeout(() => buttonText.innerText = 'Salin', 2200);
        }

        async function copySnippet(elementId) {
            const text = document.getElementById(elementId).innerText;
            await copyText(text, 'Contoh JSON berhasil disalin');
        }
    </script>
</x-filament-panels::page>
