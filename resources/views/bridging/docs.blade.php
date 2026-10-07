<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi REST API Bridging - SiHAI's PPI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"Fira Code"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-brand-500 flex items-center justify-center text-white font-black text-lg shadow-sm">
                    H
                </div>
                <div>
                    <div class="font-bold text-slate-900 leading-tight">SiHAI's - Sistem PPI</div>
                    <div class="text-xs text-slate-500">Katalog Spesifikasi REST API Bridging SIMRS</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Versi 1.0 (REST)
                </span>
                <a href="/admin/login" class="text-xs font-medium text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                    Login Admin
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Sidebar Navigation -->
            <aside class="hidden lg:block lg:col-span-1">
                <div class="sticky top-24 space-y-4">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider px-3">Daftar Isi</div>
                    <nav class="space-y-1 text-sm font-medium">
                        <a href="#pengantar" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">1. Latar Belakang &amp; Tujuan</a>
                        <a href="#autentikasi" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">2. Autentikasi &amp; Header</a>
                        <a href="#kontrak" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">3. Kontrak Respon &amp; Error</a>
                        <a href="#ep-ruangan" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">4. GET Master Ruangan</a>
                        <a href="#ep-ranap" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">5. GET Pasien Ranap Aktif</a>
                        <a href="#ep-search" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">6. GET Pencarian Pasien</a>
                        <a href="#ep-pulang" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">7. GET Pasien Pulang</a>
                        <a href="#ep-alat" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">8. GET Alat Invasif (Opsional)</a>
                        <a href="#alur-mapping" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">9. Alur Integrasi &amp; Mapping</a>
                    </nav>

                    <div class="p-4 bg-brand-50 border border-brand-100 rounded-xl mt-6">
                        <div class="text-xs font-bold text-brand-800">Catatan untuk Pengembang:</div>
                        <p class="text-xs text-brand-700 mt-1 leading-relaxed">
                            Endpoint-endpoint ini dibuat di server SIMRS Anda. SiHAI's bertindak sebagai klien (consumer) yang akan memanggil endpoint ini.
                        </p>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="lg:col-span-3 space-y-10">

                <!-- Section: Pengantar -->
                <section id="pengantar" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-md uppercase">Gambaran Umum</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Integrasi SiHAI's PPI &amp; SIMRS
                    </h1>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        Dokumen ini adalah panduan spesifikasi REST API bagi <strong>Tim Pengembang SIMRS</strong>. SiHAI's membutuhkan data admisi dan rawat inap pasien dari SIMRS untuk melakukan pemantauan infeksi rumah sakit (HAIs: VAP, IAD, PLEB, ISK, ILO, HAP) dan audit kepatuhan bundle PPI.
                    </p>

                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-2">
                        <div class="text-xs font-bold text-amber-800 uppercase">Status Implementasi di SiHAI's</div>
                        <p class="text-xs text-amber-700 leading-relaxed">
                            Setiap endpoint di bawah ini diberi label status agar jelas mana yang sudah benar-benar dikonsumsi SiHAI's dan mana yang masih berupa spesifikasi. Saat ini <strong>data pasien rawat inap aktif &amp; pasien pulang</strong> sudah ditarik otomatis dari SIMRS sehingga petugas tidak perlu input pasien manual &mdash; tinggal mengisi data HAIs.
                        </p>
                        <ul class="text-xs text-amber-700 space-y-1.5 leading-relaxed">
                            <li><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Berjalan</span> &mdash; Sudah dipanggil oleh SiHAI's (siap dipakai).</li>
                            <li><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold"><span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>Belum Dikonsumsi</span> &mdash; Kontrak sudah ditentukan, namun SiHAI's belum memanggilnya (persiapan/roadmap).</li>
                        </ul>
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                        <div class="text-xs font-bold text-slate-700 uppercase">Base URL &amp; Contoh Pemanggilan</div>
                        <p class="text-sm text-slate-600">
                            Tim SIMRS menentukan sendiri Base URL API-nya (contoh: <code class="bg-slate-200 px-1 py-0.5 rounded">https://simrs.namafaskes.co.id/api/v1</code>). Base URL ini kemudian didaftarkan pada menu <strong>Pengaturan Bridging</strong> di SiHAI's. Semua path di bawah relatif terhadap Base URL tersebut.
                        </p>
                        <div class="text-xs font-bold text-slate-700 uppercase pt-1">Contoh cURL (Bearer Token):</div>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed">curl -X GET "https://simrs.namafaskes.co.id/api/v1/pasien-ranap/aktif?kode_ruangan=B01" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer &lt;TOKEN_ANDA&gt;"</pre>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                            <div class="p-3 bg-white rounded-lg border border-slate-200">
                                <div class="text-[11px] font-bold text-slate-500 uppercase">Timeout</div>
                                <div class="text-sm font-semibold text-slate-900 mt-0.5">Default 30 detik</div>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-slate-200">
                                <div class="text-[11px] font-bold text-slate-500 uppercase">Format Tanggal</div>
                                <div class="text-sm font-semibold text-slate-900 mt-0.5">YYYY-MM-DD HH:mm:ss</div>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-slate-200">
                                <div class="text-[11px] font-bold text-slate-500 uppercase">Encoding</div>
                                <div class="text-sm font-semibold text-slate-900 mt-0.5">UTF-8 / JSON</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section: Autentikasi -->
                <section id="autentikasi" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm">2</span>
                        <h2 class="text-xl font-bold text-slate-900">Standar Autentikasi &amp; Header</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-xs font-bold text-slate-500 uppercase">Protokol &amp; Format</div>
                            <div class="text-sm font-semibold text-slate-900 mt-1">HTTPS / JSON REST API</div>
                            <p class="text-xs text-slate-500 mt-1">Header wajib: <code class="bg-slate-200 px-1 py-0.5 rounded">Accept: application/json</code> &amp; <code class="bg-slate-200 px-1 py-0.5 rounded">Content-Type: application/json</code></p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-xs font-bold text-slate-500 uppercase">Metode Autentikasi</div>
                            <div class="text-sm font-semibold text-slate-900 mt-1">Bearer / API Key / Basic</div>
                            <p class="text-xs text-slate-500 mt-1">Pilih salah satu sesuai kesepakatan. Contoh: <code class="bg-slate-200 px-1 py-0.5 rounded">Authorization: Bearer &lt;token&gt;</code></p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border border-slate-200 rounded-lg">
                            <thead class="bg-slate-50 text-slate-700 font-semibold">
                                <tr>
                                    <th class="p-2.5 border-b">Tipe Autentikasi</th>
                                    <th class="p-2.5 border-b">Header yang Dikirim SiHAI's</th>
                                    <th class="p-2.5 border-b">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="p-2.5 font-semibold text-slate-800">Bearer Token</td>
                                    <td class="p-2.5 font-mono text-brand-600">Authorization: Bearer &lt;token&gt;</td>
                                    <td class="p-2.5 text-slate-600">Pilihan yang direkomendasikan.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-semibold text-slate-800">Custom API Key</td>
                                    <td class="p-2.5 font-mono text-brand-600">&lt;Nama-Header&gt;: &lt;key&gt; (mis. X-API-KEY)</td>
                                    <td class="p-2.5 text-slate-600">Nama header dapat dikustomisasi di Pengaturan Bridging.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-semibold text-slate-800">Basic Auth</td>
                                    <td class="p-2.5 font-mono text-brand-600">Authorization: Basic &lt;base64(user:pass)&gt;</td>
                                    <td class="p-2.5 text-slate-600">Format token disimpan sebagai <code class="bg-slate-100 px-1 py-0.5 rounded">username:password</code>.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-semibold text-slate-800">None</td>
                                    <td class="p-2.5 font-mono text-slate-500">&mdash; (tanpa header autentikasi)</td>
                                    <td class="p-2.5 text-slate-600">Hanya untuk jaringan internal yang aman.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Section: Kontrak Respon & Error -->
                <section id="kontrak" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm">3</span>
                        <h2 class="text-xl font-bold text-slate-900">Kontrak Respon &amp; Penanganan Error</h2>
                    </div>

                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase">Struktur Respon Sukses (Yang Diharapkan):</div>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed">{
  "status": "success",     // "success" atau "error"
  "message": "Operasi berhasil",
  "data": [ ... ]          // Data payload (WAJIB berupa array)
}</pre>
                    </div>

                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-2">
                        <div class="text-xs font-bold text-amber-800 uppercase">Aturan Penting Kontrak Respon</div>
                        <ul class="text-xs text-amber-700 space-y-1.5 list-disc list-inside leading-relaxed">
                            <li>Field <code class="bg-amber-100 px-1 py-0.5 rounded">data</code> <strong>harus berupa array</strong>. Jika bukan array, SiHAI's akan menolak dan menampilkan error.</li>
                            <li>Jika field <code class="bg-amber-100 px-1 py-0.5 rounded">data</code> tidak disertakan, SiHAI's akan memperlakukan <em>root object</em> JSON sebagai data.</li>
                            <li>Field <code class="bg-amber-100 px-1 py-0.5 rounded">status</code> dan <code class="bg-amber-100 px-1 py-0.5 rounded">message</code> bersifat informatif (tidak wajib dibaca), namun sangat dianjurkan.</li>
                            <li>Untuk respon gagal, gunakan HTTP status code yang tepat <strong>dan</strong> tetap kembalikan JSON (bukan halaman HTML error), agar pesan error terbaca jelas.</li>
                        </ul>
                    </div>

                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase">Daftar HTTP Status Code:</div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border border-slate-200 rounded-lg">
                                <thead class="bg-slate-50 text-slate-700 font-semibold">
                                    <tr>
                                        <th class="p-2.5 border-b">Status</th>
                                        <th class="p-2.5 border-b">Arti</th>
                                        <th class="p-2.5 border-b">Kapan Terjadi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <td class="p-2.5 font-mono font-bold text-emerald-600">200 OK</td>
                                        <td class="p-2.5 text-slate-700">Berhasil</td>
                                        <td class="p-2.5 text-slate-600">Permintaan diproses dan data dikembalikan (boleh array kosong).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5 font-mono font-bold text-amber-600">400 / 422</td>
                                        <td class="p-2.5 text-slate-700">Permintaan Tidak Valid</td>
                                        <td class="p-2.5 text-slate-600">Parameter wajib tidak dikirim atau format salah.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5 font-mono font-bold text-red-600">401 / 403</td>
                                        <td class="p-2.5 text-slate-700">Tidak Terautentikasi / Terlarang</td>
                                        <td class="p-2.5 text-slate-600">Token salah, kadaluarsa, atau tidak punya hak akses.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5 font-mono font-bold text-slate-600">404</td>
                                        <td class="p-2.5 text-slate-700">Tidak Ditemukan</td>
                                        <td class="p-2.5 text-slate-600">Path endpoint salah atau data (mis. no_rawat) tidak ada.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5 font-mono font-bold text-red-600">500</td>
                                        <td class="p-2.5 text-slate-700">Kesalahan Server</td>
                                        <td class="p-2.5 text-slate-600">Terjadi error internal pada server SIMRS.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase">Contoh Respon Error:</div>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed">{
  "status": "error",
  "message": "Parameter 'keyword' wajib diisi.",
  "data": []
}</pre>
                    </div>
                </section>

                <!-- Section: Endpoint 1 - Master Ruangan -->
                <section id="ep-ruangan" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-black rounded-md">GET</span>
                            <code class="text-base font-bold text-slate-900">/api/v1/ruangan</code>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Berjalan</span>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded">Prioritas: Wajib</span>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600">
                        Menyediakan daftar seluruh bangsal / ruangan rawat inap untuk disinkronkan ke master bangsal SiHAI's.
                    </p>

                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl space-y-2">
                        <div class="text-xs font-bold text-blue-800 uppercase">Nama Field yang Diterima (Fleksibel)</div>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            SiHAI's menerima beberapa variasi nama field agar mudah diadaptasi dari SIMRS yang berbeda:
                        </p>
                        <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside leading-relaxed">
                            <li>Kode ruangan: <code class="bg-blue-100 px-1 py-0.5 rounded">kode_ruangan</code>, <code class="bg-blue-100 px-1 py-0.5 rounded">kd_bangsal</code>, atau <code class="bg-blue-100 px-1 py-0.5 rounded">id</code></li>
                            <li>Nama ruangan: <code class="bg-blue-100 px-1 py-0.5 rounded">nama_ruangan</code>, <code class="bg-blue-100 px-1 py-0.5 rounded">nm_bangsal</code>, atau <code class="bg-blue-100 px-1 py-0.5 rounded">nama</code></li>
                        </ul>
                        <p class="text-xs text-blue-600">Gunakan salah satu yang paling sesuai dengan struktur database SIMRS Anda.</p>
                    </div>

                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase">Contoh Respon (200 OK):</div>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed">{
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
      "nama_ruangan": "Intensive Care Unit",
      "kelas": "ICU",
      "status_aktif": true
    }
  ]
}</pre>
                    </div>
                </section>

                <!-- Section: Endpoint 2 - Pasien Ranap Aktif -->
                <section id="ep-ranap" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-black rounded-md">GET</span>
                            <code class="text-base font-bold text-slate-900">/api/v1/pasien-ranap/aktif</code>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Berjalan</span>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded">Prioritas: Sangat Penting</span>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600">
                        Menampilkan seluruh pasien yang <strong>saat ini sedang aktif menginap</strong> di rumah sakit. Digunakan untuk sensus surveilans harian.
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border border-slate-200 rounded-lg">
                            <thead class="bg-slate-50 text-slate-700 font-semibold">
                                <tr>
                                    <th class="p-2.5 border-b">Parameter Query</th>
                                    <th class="p-2.5 border-b">Tipe</th>
                                    <th class="p-2.5 border-b">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="p-2.5 font-mono text-brand-600">kode_ruangan</td>
                                    <td class="p-2.5 text-slate-500">String (Opsional)</td>
                                    <td class="p-2.5 text-slate-600">Filter berdasarkan kode ruangan (misal: <code class="bg-slate-100 px-1 py-0.5 rounded">?kode_ruangan=B01</code>).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase">Contoh Respon (200 OK):</div>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed">{
  "status": "success",
  "total": 1,
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
                </section>

                <!-- Section: Endpoint 3 - Pencarian Pasien Realtime -->
                <section id="ep-search" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-black rounded-md">GET</span>
                            <code class="text-base font-bold text-slate-900">/api/v1/pasien/search</code>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-slate-200 text-slate-700 text-xs font-bold"><span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>Belum Dikonsumsi</span>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded">Prioritas: Wajib</span>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600">
                        Pencarian data pasien secara langsung (<em>on-the-fly</em>) saat petugas mengetik No. RM atau No. Rawat di form surveilans SiHAI's.
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border border-slate-200 rounded-lg">
                            <thead class="bg-slate-50 text-slate-700 font-semibold">
                                <tr>
                                    <th class="p-2.5 border-b">Parameter Query</th>
                                    <th class="p-2.5 border-b">Tipe</th>
                                    <th class="p-2.5 border-b">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="p-2.5 font-mono text-brand-600">keyword</td>
                                    <td class="p-2.5 text-slate-500">String (Wajib)</td>
                                    <td class="p-2.5 text-slate-600">Kata kunci: No Rekam Medis (No RM), No Rawat, atau NIK pasien.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase">Contoh Respon (200 OK):</div>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed">{
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
                </section>

                <!-- Section: Endpoint 4 - Pasien Pulang -->
                <section id="ep-pulang" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-black rounded-md">GET</span>
                            <code class="text-base font-bold text-slate-900">/api/v1/pasien-ranap/pulang</code>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Berjalan</span>
                            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded">Prioritas: Penting</span>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600">
                        Digunakan untuk meng-update tanggal keluar dan menghitung total hari rawat (<em>denominator</em> hari rawat pasien).
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border border-slate-200 rounded-lg">
                            <thead class="bg-slate-50 text-slate-700 font-semibold">
                                <tr>
                                    <th class="p-2.5 border-b">Parameter Query</th>
                                    <th class="p-2.5 border-b">Tipe</th>
                                    <th class="p-2.5 border-b">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="p-2.5 font-mono text-brand-600">tanggal_awal</td>
                                    <td class="p-2.5 text-slate-500">Date (YYYY-MM-DD)</td>
                                    <td class="p-2.5 text-slate-600">Batas awal tanggal kepulangan pasien.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-mono text-brand-600">tanggal_akhir</td>
                                    <td class="p-2.5 text-slate-500">Date (YYYY-MM-DD)</td>
                                    <td class="p-2.5 text-slate-600">Batas akhir tanggal kepulangan pasien.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase">Contoh Respon (200 OK):</div>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed">{
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
                </section>

                <!-- Section: Endpoint 5 - Alat Invasif -->
                <section id="ep-alat" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-black rounded-md">GET</span>
                            <code class="text-base font-bold text-slate-900">/api/v1/pasien/{no_rawat}/alat-invasif</code>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-slate-200 text-slate-700 text-xs font-bold"><span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>Belum Dikonsumsi</span>
                            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded">Prioritas: Opsional</span>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600">
                        Jika di SIMRS perawat sudah mencatat tanggal pasang/lepas infus, kateter, ventilator, data ini dapat ditarik langsung agar IPCN/perawat PPI tidak perlu input ulang denominator alat.
                    </p>

                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase">Contoh Respon (200 OK):</div>
                        <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto leading-relaxed">{
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
                </section>

                <!-- Section: Alur Integrasi & Mapping -->
                <section id="alur-mapping" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm">9</span>
                        <h2 class="text-xl font-bold text-slate-900">Alur Integrasi &amp; Mapping Data</h2>
                    </div>

                    <p class="text-sm text-slate-600 leading-relaxed">
                        Setelah endpoint tersedia di sisi SIMRS, langkah berikutnya adalah menghubungkan dan memetakan data melalui menu <strong>Bridging SIMRS</strong> di SiHAI's.
                    </p>

                    <ol class="space-y-3">
                        <li class="flex gap-3">
                            <span class="w-6 h-6 shrink-0 rounded-full bg-brand-500 text-white text-xs font-bold flex items-center justify-center">1</span>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Isi Pengaturan Bridging</div>
                                <p class="text-xs text-slate-500 leading-relaxed">Masukkan Base URL, tipe autentikasi, dan token SIMRS. Gunakan tombol <em>Test Koneksi</em> untuk memverifikasi kontrak endpoint <code class="bg-slate-100 px-1 py-0.5 rounded">/ruangan</code>.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="w-6 h-6 shrink-0 rounded-full bg-brand-500 text-white text-xs font-bold flex items-center justify-center">2</span>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Tarik &amp; Petakan Ruangan</div>
                                <p class="text-xs text-slate-500 leading-relaxed">Gunakan menu <strong>Mapping Data</strong> untuk menarik daftar ruangan dari SIMRS, lalu pasangkan kode ruangan SIMRS dengan master bangsal SiHAI's.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="w-6 h-6 shrink-0 rounded-full bg-brand-500 text-white text-xs font-bold flex items-center justify-center">3</span>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Aktifkan Sinkronisasi Ruangan</div>
                                <p class="text-xs text-slate-500 leading-relaxed">Aktifkan bridging dan (opsional) auto-sync berkala agar master ruangan selalu terbarui.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="w-6 h-6 shrink-0 rounded-full bg-brand-500 text-white text-xs font-bold flex items-center justify-center">4</span>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Tarik Data Pasien Otomatis</div>
                                <p class="text-xs text-slate-500 leading-relaxed">Data pasien rawat inap aktif dan pasien pulang ditarik dari SIMRS (via scheduler berkala atau tombol <strong>Tarik Pasien dari SIMRS</strong> di halaman Rawat Inap). Petugas tidak perlu input pasien manual.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="w-6 h-6 shrink-0 rounded-full bg-brand-500 text-white text-xs font-bold flex items-center justify-center">5</span>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Petugas Input Data HAIs</div>
                                <p class="text-xs text-slate-500 leading-relaxed">Setelah daftar pasien terisi otomatis, petugas PPI/IPCN cukup mengisi data surveilans HAIs dan audit bundle pada tiap pasien.</p>
                            </div>
                        </li>
                    </ol>

                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <div class="text-xs font-bold text-emerald-800">Arsitektur Peran:</div>
                        <p class="text-xs text-emerald-700 mt-1 leading-relaxed">
                            <strong>SIMRS</strong> = Penyedia endpoint (server). <strong>SiHAI's</strong> = Konsumen yang memanggil endpoint tersebut secara periodik dan on-demand.
                        </p>
                    </div>
                </section>

                <!-- Footer Info -->
                <div class="text-center text-xs text-slate-400 py-6 border-t border-slate-200">
                    &copy; 2026 SiHAI's - Sistem Surveilans Pencegahan &amp; Pengendalian Infeksi (PPI).
                </div>

            </main>
        </div>
    </div>

</body>
</html>
