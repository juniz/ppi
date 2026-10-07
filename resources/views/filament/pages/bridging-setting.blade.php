<x-filament-panels::page>
    {{-- Status Banner Card --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Bridging</div>
            <div class="mt-2 flex items-center gap-2">
                @if($this->settingRecord?->is_active)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Aktif Terhubung
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        Nonaktif
                    </span>
                @endif
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $this->settingRecord?->simrs_name ?: 'SIMRS Utama' }}
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terakhir Sinkronisasi</div>
            <div class="mt-2 text-sm text-gray-900 dark:text-white font-medium">
                @if($this->settingRecord?->last_sync_at)
                    <div class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                        <x-heroicon-m-check-circle class="w-4 h-4" />
                        <span>{{ $this->settingRecord->last_sync_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" title="{{ $this->settingRecord->last_sync_message }}">
                        {{ $this->settingRecord->last_sync_message ?: 'Berhasil sinkron' }}
                    </div>
                @else
                    <span class="text-gray-400 italic">Belum pernah dijalankan</span>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col justify-between">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi Cepat</div>
            <div class="mt-2 flex items-center gap-2">
                <x-filament::button wire:click="syncRuanganNow" color="info" size="sm" icon="heroicon-m-arrow-path">
                    Sync Master Ruangan
                </x-filament::button>
                <x-filament::button wire:click="testConnection" color="warning" size="sm" icon="heroicon-m-signal">
                    Tes Koneksi
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- Form Setting --}}
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="mt-4 flex items-center justify-between">
            <a href="{{ \App\Filament\Pages\BridgingKatalogPage::getUrl() }}" class="text-sm font-medium text-primary-600 hover:text-primary-500 flex items-center gap-1">
                <x-heroicon-m-book-open class="w-4 h-4" />
                Buka Katalog & Dokumentasi API Lengkap &rarr;
            </a>

            <div class="flex items-center gap-3">
                <x-filament::button type="button" wire:click="testConnection" color="warning" icon="heroicon-m-signal">
                    Tes Koneksi API
                </x-filament::button>
                <x-filament::button type="submit" size="md" icon="heroicon-m-check">
                    Simpan Pengaturan
                </x-filament::button>
            </div>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
