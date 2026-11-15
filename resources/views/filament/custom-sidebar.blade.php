<style>
    /* Modern UI/UX for Filament Sidebar */
    aside.fi-sidebar {
        background-color: #ffffff !important;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05) !important;
        border-right: 1px solid #f1f5f9 !important;
    }
    
    /* Better text contrast for items */
    .fi-sidebar-item-label {
        color: #334155 !important;
        font-weight: 500 !important;
        font-size: 0.925rem !important;
    }
    
    /* Force smaller gaps between outer group containers in Filament v3 */
    .fi-sidebar-nav-groups {
        gap: 0.5rem !important;
    }
    .fi-sidebar-group {
        margin-top: 0 !important;
    }
    
    /* Style the group button to act as the colored pill enclosing the text and arrow */
    .fi-sidebar-group-button {
        display: flex !important;
        width: 100% !important;
        justify-content: space-between !important;
        align-items: center;
        background-color: rgba(var(--primary-500), 0.08) !important;
        padding: 0.35rem 0.5rem !important;
        border-radius: 0.5rem !important;
        border-left: 3px solid rgba(var(--primary-500), 0.6) !important;
        margin-bottom: 0.15rem !important;
    }
    
    /* Make sure the chevron arrow inside the button matches the color */
    .fi-sidebar-group-button svg {
        color: rgba(var(--primary-600), 1) !important;
    }

    /* Group headings text - remove background since it's now on the button */
    .fi-sidebar-group-label {
        color: rgba(var(--primary-600), 1) !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
    }
    
    /* Icon contrast */
    .fi-sidebar-item-icon {
        color: #64748b !important;
    }
    
    /* Hover effects */
    .fi-sidebar-item-button:hover {
        background-color: #f8fafc !important;
        transform: translateX(2px);
        transition: all 0.2s ease-in-out;
    }
    
    /* Active Item Highlight */
    .fi-sidebar-item-active {
        background-color: rgba(var(--primary-500), 0.05) !important;
        border-right: 3px solid rgba(var(--primary-600), 1) !important;
        border-radius: 0 !important;
    }
    
    .fi-sidebar-item-active .fi-sidebar-item-label,
    .fi-sidebar-item-active .fi-sidebar-item-icon {
        color: rgba(var(--primary-600), 1) !important;
        font-weight: 700 !important;
    }
    
    /* Logo area spacing */
    .fi-sidebar-header {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    /* Compact / Dense Tables for clinical data */
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
    .fi-ta-table .fi-badge.fi-color-info {
        background-color: #e0f2fe !important;
        border: 1.5px solid #0284c7 !important;
        color: #000000 !important;
    }
    .fi-ta-table .fi-badge.fi-color-info span {
        color: #000000 !important;
        font-weight: 800 !important;
    }
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

    /* ==========================================================================
       Audit 5 Momen Cuci Tangan Cards (Modern Clinical Audit UI)
       ========================================================================== */
    .fi-fo-field-wrp:has(.audit-momen-card) {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.625rem 0.875rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        margin-bottom: 0.35rem;
    }
    .dark .fi-fo-field-wrp:has(.audit-momen-card) {
        background: rgba(30, 41, 59, 0.45);
        border-color: rgba(51, 65, 85, 0.75);
    }
    .fi-fo-field-wrp:has(.audit-momen-card):hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.05);
    }
    .dark .fi-fo-field-wrp:has(.audit-momen-card):hover {
        background: rgba(30, 41, 59, 0.75);
        border-color: rgba(71, 85, 105, 0.85);
    }

    /* Row Layout: Flex for Label (Left) and Buttons (Right) */
    .fi-fo-field-wrp:has(.audit-momen-card) > div {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 1.25rem !important;
        width: 100% !important;
    }
    .fi-fo-field-wrp:has(.audit-momen-card) > div > div:first-child {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        padding-top: 0 !important;
    }
    .fi-fo-field-wrp:has(.audit-momen-card) .fi-fo-field-wrp-label {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
        cursor: default !important;
    }
    .fi-fo-field-wrp:has(.audit-momen-card) .fi-fo-field-wrp-label > span {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
    }
    .fi-fo-field-wrp:has(.audit-momen-card) .fi-fo-field-wrp-label span.text-sm {
        white-space: nowrap !important;
        font-weight: 700 !important;
    }

    /* Compact Buttons Column (Perkecil ukuran Ya / Tidak) */
    .fi-fo-field-wrp:has(.audit-momen-card) > div > div:last-child {
        flex: 0 0 auto !important;
        width: auto !important;
    }
    .audit-momen-card {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 0.35rem !important;
        width: auto !important;
    }
    .audit-momen-card label.fi-btn {
        height: 1.85rem !important;
        min-height: 1.85rem !important;
        padding: 0.2rem 0.65rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        border-radius: 0.5rem !important;
        gap: 0.25rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        cursor: pointer !important;
    }
    .audit-momen-card label.fi-btn svg.fi-btn-icon {
        width: 0.85rem !important;
        height: 0.85rem !important;
    }
    .audit-momen-card label.fi-btn .fi-btn-label {
        font-size: 0.75rem !important;
        line-height: 1rem !important;
    }

    /* Mobile View Optimization for Audit Form Modal */
    @media (max-width: 640px) {
        .fi-modal-window {
            margin: 0.5rem !important;
            max-height: calc(100vh - 1rem) !important;
        }
        .fi-modal-content {
            padding: 0.65rem 0.85rem !important;
        }
        .fi-modal-header {
            padding: 0.65rem 0.85rem !important;
        }
        .fi-modal-header h2 {
            font-size: 1rem !important;
        }
        .fi-modal-footer {
            padding: 0.5rem 0.85rem !important;
        }
        .fi-modal-footer button {
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
        }

        /* Mobile Card & ToggleButtons layout */
        .fi-fo-field-wrp:has(.audit-momen-card) {
            padding: 0.5rem 0.75rem !important;
        }
        .fi-fo-field-wrp:has(.audit-momen-card) .fi-fo-field-wrp-label span.text-sm {
            white-space: normal !important;
        }
        .fi-fo-field-wrp:has(.audit-momen-card) > div {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
        }
        .audit-momen-card {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            width: 100% !important;
            gap: 0.35rem !important;
        }
        .audit-momen-card label.fi-btn {
            width: 100% !important;
            justify-content: center !important;
        }

        /* Ultra compact section padding */
        .fi-section-content {
            padding: 0.5rem 0.65rem !important;
            gap: 0.25rem !important;
        }
        .fi-section-header {
            padding: 0.5rem 0.65rem !important;
        }
        .fi-section-header-heading {
            font-size: 0.875rem !important;
        }
    }
</style>
