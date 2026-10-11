<x-filament-panels::page>
    <style>
        .tester-code-view {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            border: 1px solid #334155;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .tester-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #1e293b 100%) !important;
            color: #f8fafc !important;
        }
    </style>

    <!-- Top Hero / Preset Bar -->
    <div class="tester-hero relative overflow-hidden rounded-2xl p-6 shadow-sm border border-slate-700/50">
        <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-400/20 bg-indigo-500/10 px-3 py-1 text-xs font-semibold text-indigo-300 mb-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    API Playground &amp; Postman Simulator
                </div>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">Tester API Bridging SIMRS</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Uji dan verifikasi respon API SIMRS secara langsung tanpa perlu aplikasi Postman. Pilih endpoint di bawah, sesuaikan parameter, lalu klik <strong>Kirim Permintaan</strong>.
                </p>
            </div>

            <!-- Preset Buttons -->
            <div class="flex flex-wrap items-center gap-2 bg-slate-900/60 p-2 rounded-xl border border-slate-700/60 shrink-0">
                <span class="text-xs text-slate-400 font-semibold px-2">Target:</span>
                <button
                    type="button"
                    wire:click="applyPreset('active')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $preset === 'active' ? 'bg-amber-500 text-slate-950 font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                    Pengaturan Aktif
                </button>
                <button
                    type="button"
                    wire:click="applyPreset('mock')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $preset === 'mock' ? 'bg-emerald-500 text-slate-950 font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                    Mock Simulator
                </button>
                <button
                    type="button"
                    wire:click="applyPreset('custom')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $preset === 'custom' ? 'bg-blue-500 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                    Kustom (Manual)
                </button>
            </div>
        </div>
    </div>

    <!-- Target Server Config Summary (Collapsible/Editable) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-md bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">1</span>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Konfigurasi Target Server</h3>
            </div>
            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $preset === 'mock' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                Mode: {{ strtoupper($preset) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Base URL API SIMRS</label>
                <input
                    type="text"
                    wire:model.defer="baseUrl"
                    placeholder="https://simrs.namafaskes.co.id/api/v1"
                    class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Tipe Autentikasi</label>
                <select
                    wire:model="authType"
                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="bearer">Bearer Token</option>
                    <option value="api_key">API Key Header</option>
                    <option value="basic">Basic Auth</option>
                    <option value="none">Tanpa Auth (None)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Token / Key / Password</label>
                <input
                    type="password"
                    wire:model.defer="apiToken"
                    placeholder="Token atau API key..."
                    class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    <!-- Request Bar & Controls -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-md bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">2</span>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Pilih Endpoint &amp; Kirim Permintaan</h3>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="runAllTests"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Test Semua Endpoint Sekaligus
                </button>
            </div>
        </div>

        <!-- Endpoint Selection Buttons -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
            <button
                type="button"
                wire:click="selectEndpoint('ruangan')"
                class="p-2.5 rounded-xl border text-left transition flex flex-col justify-between {{ $selectedEndpoint === 'ruangan' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 font-bold shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 text-xs' }}">
                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase">GET</span>
                <span class="font-semibold text-xs mt-1">1. Ruangan</span>
                <span class="text-[10px] text-slate-400">Master Bangsal</span>
            </button>

            <button
                type="button"
                wire:click="selectEndpoint('pasien_aktif')"
                class="p-2.5 rounded-xl border text-left transition flex flex-col justify-between {{ $selectedEndpoint === 'pasien_aktif' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 font-bold shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 text-xs' }}">
                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase">GET</span>
                <span class="font-semibold text-xs mt-1">2. Pasien Aktif</span>
                <span class="text-[10px] text-slate-400">Ranap Inap</span>
            </button>

            <button
                type="button"
                wire:click="selectEndpoint('pasien_pulang')"
                class="p-2.5 rounded-xl border text-left transition flex flex-col justify-between {{ $selectedEndpoint === 'pasien_pulang' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 font-bold shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 text-xs' }}">
                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase">GET</span>
                <span class="font-semibold text-xs mt-1">3. Pasien Pulang</span>
                <span class="text-[10px] text-slate-400">KRS / Sembuh</span>
            </button>

            <button
                type="button"
                wire:click="selectEndpoint('search')"
                class="p-2.5 rounded-xl border text-left transition flex flex-col justify-between {{ $selectedEndpoint === 'search' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 font-bold shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 text-xs' }}">
                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase">GET</span>
                <span class="font-semibold text-xs mt-1">4. Cari Pasien</span>
                <span class="text-[10px] text-slate-400">No RM / Nama</span>
            </button>

            <button
                type="button"
                wire:click="selectEndpoint('alat_invasif')"
                class="p-2.5 rounded-xl border text-left transition flex flex-col justify-between {{ $selectedEndpoint === 'alat_invasif' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 font-bold shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 text-xs' }}">
                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase">GET</span>
                <span class="font-semibold text-xs mt-1">5. Alat Invasif</span>
                <span class="text-[10px] text-slate-400">IVL / Kateter</span>
            </button>

            <button
                type="button"
                wire:click="selectEndpoint('custom')"
                class="p-2.5 rounded-xl border text-left transition flex flex-col justify-between {{ $selectedEndpoint === 'custom' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 font-bold shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 text-xs' }}">
                <span class="text-[11px] font-bold text-slate-500 uppercase">CUSTOM</span>
                <span class="font-semibold text-xs mt-1">Kustom Path</span>
                <span class="text-[10px] text-slate-400">Manual Path</span>
            </button>
        </div>

        <!-- Dynamic Parameter Inputs based on selected endpoint -->
        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60">
            <div class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">Parameter Request (Query Params / Path)</div>

            @if($selectedEndpoint === 'ruangan')
                <p class="text-xs text-slate-500 dark:text-slate-400 italic">Endpoint <code class="font-mono bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">/ruangan</code> tidak membutuhkan parameter query wajib.</p>
            @elseif($selectedEndpoint === 'pasien_aktif')
                <div class="max-w-md">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Filter Kode Ruangan (Opsional)</label>
                    <input
                        type="text"
                        wire:model.defer="paramKodeRuangan"
                        placeholder="Contoh: B01 atau kosongkan untuk semua"
                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-slate-100">
                </div>
            @elseif($selectedEndpoint === 'pasien_pulang')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-lg">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Tanggal Awal</label>
                        <input
                            type="date"
                            wire:model.defer="paramTglAwal"
                            class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Tanggal Akhir</label>
                        <input
                            type="date"
                            wire:model.defer="paramTglAkhir"
                            class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-slate-100">
                    </div>
                </div>
            @elseif($selectedEndpoint === 'search')
                <div class="max-w-md">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Keyword Pencarian (No RM / No Rawat / NIK / Nama)</label>
                    <input
                        type="text"
                        wire:model.defer="paramKeyword"
                        placeholder="Contoh: 001201 atau Budi"
                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-slate-100">
                </div>
            @elseif($selectedEndpoint === 'alat_invasif')
                <div class="max-w-md">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nomor Rawat Pasien (Path Parameter)</label>
                    <input
                        type="text"
                        wire:model.defer="paramNoRawat"
                        placeholder="Contoh: 2026/10/11/000001"
                        class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-slate-100">
                </div>
            @elseif($selectedEndpoint === 'custom')
                <div class="max-w-md">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Custom Path (Relatif dari Base URL)</label>
                    <input
                        type="text"
                        wire:model.defer="customPath"
                        placeholder="Contoh: ping atau dokter atau users"
                        class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-slate-100">
                </div>
            @endif
        </div>

        <!-- URL Preview & Send Action Button -->
        @php
            $currentDetails = $this->getEndpointDetails($selectedEndpoint);
        @endphp
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-2">
            <div class="flex-1 flex items-center gap-2 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono overflow-x-auto text-slate-800 dark:text-slate-200">
                <span class="px-2 py-0.5 rounded bg-blue-600 text-white font-black text-[11px]">GET</span>
                <span class="truncate">{{ $currentDetails['full_url'] }}</span>
            </div>

            <button
                type="button"
                wire:click="sendRequest"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md transition disabled:opacity-50">
                <span wire:loading.remove wire:target="sendRequest">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Kirim Permintaan
                </span>
                <span wire:loading wire:target="sendRequest" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    Menghubungi Server...
                </span>
            </button>
        </div>
    </div>

    <!-- Batch Results Table (Jika user klik Test Semua Endpoint) -->
    @if(!empty($batchResults))
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">✓</span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Hasil Pengujian Semua Endpoint (Health Check)</h3>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                        <tr>
                            <th class="p-3">Endpoint</th>
                            <th class="p-3">Status Code</th>
                            <th class="p-3">Waktu Respon</th>
                            <th class="p-3">Jumlah Data</th>
                            <th class="p-3">Validasi Kontrak</th>
                            <th class="p-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($batchResults as $bKey => $bResult)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $bResult['title'] ?? $bKey }}
                                </td>
                                <td class="p-3">
                                    @if($bResult['status_code'] === 200)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                            200 OK
                                        </span>
                                    @elseif($bResult['status_code'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                            {{ $bResult['status_code'] }} {{ $bResult['status_text'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 font-mono text-slate-600 dark:text-slate-400">
                                    {{ $bResult['latency_ms'] }} ms
                                </td>
                                <td class="p-3 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $bResult['data_count'] }} record
                                </td>
                                <td class="p-3">
                                    @if($bResult['success'])
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Lulus Validasi
                                        </span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400 font-bold flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            Tidak Sesuai
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-slate-500 dark:text-slate-400">
                                    {{ $bResult['message'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Single Request Result Viewer -->
    @if($lastResult)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-5">
            <!-- Result Header Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-xl {{ $lastResult['success'] ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800' }}">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-lg text-sm font-black {{ $lastResult['success'] ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white' }}">
                        {{ $lastResult['status_code'] ?: 'ERR' }}
                    </span>
                    <div>
                        <div class="text-sm font-bold {{ $lastResult['success'] ? 'text-emerald-900 dark:text-emerald-200' : 'text-red-900 dark:text-red-200' }}">
                            {{ $lastResult['status_text'] }} &mdash; {{ $lastResult['message'] }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                            {{ $lastResult['full_url'] }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-xs shrink-0 font-mono">
                    <span class="px-2.5 py-1 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                        ⚡ {{ $lastResult['latency_ms'] }} ms
                    </span>
                    <span class="px-2.5 py-1 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                        📦 {{ $lastResult['data_count'] }} record
                    </span>
                </div>
            </div>

            <!-- Contract Checklist -->
            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 space-y-2">
                <div class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Checklist Kontrak Integrasi SI-HAIs</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                    @foreach($lastResult['contract_checks'] as $check)
                        <div class="p-2.5 rounded-lg border {{ $check['passed'] ? 'border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/60 dark:bg-emerald-950/30' : 'border-red-200 dark:border-red-800/60 bg-red-50/60 dark:bg-red-950/30' }}">
                            <div class="flex items-center gap-1.5 text-xs font-bold {{ $check['passed'] ? 'text-emerald-800 dark:text-emerald-300' : 'text-red-800 dark:text-red-300' }}">
                                @if($check['passed'])
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                @endif
                                {{ $check['label'] }}
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-snug">
                                {{ $check['note'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Response Body Viewer (Pretty JSON) -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Isi Respon (Response Body JSON)</span>
                    <button
                        type="button"
                        onclick="navigator.clipboard.writeText(document.getElementById('rawJsonResponseText').innerText); alert('JSON berhasil disalin ke clipboard!');"
                        class="px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                        📋 Salin JSON
                    </button>
                </div>

                <div class="rounded-xl overflow-hidden border border-slate-700">
                    <pre id="rawJsonResponseText" class="tester-code-view p-4 text-xs max-h-96 overflow-y-auto leading-relaxed">{{ $lastResult['response_body'] }}</pre>
                </div>
            </div>

            <!-- Request Headers Sent Info -->
            <div class="text-xs text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-4 font-mono">
                <span>Header Terkirim:</span>
                @foreach($lastResult['headers_sent'] as $hKey => $hVal)
                    <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                        {{ $hKey }}: {{ $hVal }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif
</x-filament-panels::page>
