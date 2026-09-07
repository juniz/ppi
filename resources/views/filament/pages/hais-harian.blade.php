<x-filament-panels::page>
    <style>
        /* Compact / Tipis Table Rows for HAIs Harian */
        .fi-ta-table .fi-ta-cell .fi-ta-text {
            padding-top: 0.35rem !important;
            padding-bottom: 0.35rem !important;
        }
        .fi-ta-table .fi-ta-cell {
            padding-top: 0.15rem !important;
            padding-bottom: 0.15rem !important;
        }
        .fi-ta-table .fi-ta-text.gap-y-1 {
            row-gap: 0.125rem !important;
        }
        .fi-ta-table .fi-ta-text span.fi-ta-text-item-label {
            line-height: 1.25 !important;
        }
        .fi-ta-table .fi-ta-text p {
            font-size: 0.725rem !important;
            line-height: 0.95rem !important;
            margin-top: 0.05rem !important;
        }
        .fi-ta-table .fi-ta-cell .fi-badge {
            padding-top: 0.1rem !important;
            padding-bottom: 0.1rem !important;
            min-height: 1.35rem !important;
            font-size: 0.75rem !important;
        }
        .fi-ta-table th.fi-ta-header-cell {
            padding-top: 0.45rem !important;
            padding-bottom: 0.45rem !important;
        }

        /* Angka di tabel: Hitam Pekat, Tebal, dan Kontras Tinggi */
        .fi-ta-table .fi-badge {
            color: #000000 !important;
            font-weight: 800 !important;
            font-size: 0.8125rem !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08) !important;
        }
        .fi-ta-table .fi-badge span {
            color: #000000 !important;
            font-weight: 800 !important;
        }
        /* Badge Pemasangan Alat: Latar lembut, border tegas, angka hitam tebal */
        .fi-ta-table .fi-badge.fi-color-info {
            background-color: #e0f2fe !important;
            border: 1.5px solid #0284c7 !important;
            color: #000000 !important;
        }
        .fi-ta-table .fi-badge.fi-color-info span {
            color: #000000 !important;
            font-weight: 800 !important;
        }
        /* Badge Infeksi HAIs: Latar lembut, border tegas, angka hitam tebal */
        .fi-ta-table .fi-badge.fi-color-danger {
            background-color: #ffe4e6 !important;
            border: 1.5px solid #e11d48 !important;
            color: #000000 !important;
        }
        .fi-ta-table .fi-badge.fi-color-danger span {
            color: #000000 !important;
            font-weight: 800 !important;
        }

        /* Nilai di Rangkuman (Summary Row): Hitam Pekat dan Tebal */
        .fi-ta-summary-row,
        .fi-ta-summary-row td,
        .fi-ta-summary-row span,
        .fi-ta-text-summary,
        .fi-ta-text-summary span,
        .fi-ta-summary-row-heading,
        .fi-ta-summary-header-cell,
        tfoot td,
        tfoot td span {
            color: #000000 !important;
            font-weight: 800 !important;
            font-size: 0.875rem !important;
        }
    </style>

    <div class="mb-4">
        <form id="applyFilters" wire:submit="applyFilters">
            {{ $this->form }}
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @livewire(\App\Filament\Resources\DataHaisResource\Widgets\HaisHarianInfeksiChart::class, ['filters' => $filters], key('hais-infeksi-chart-' . md5(json_encode($filters))))
        @livewire(\App\Filament\Resources\DataHaisResource\Widgets\HaisHarianAlatChart::class, ['filters' => $filters], key('hais-alat-chart-' . md5(json_encode($filters))))
    </div>

    {{ $this->table }}
</x-filament-panels::page>
