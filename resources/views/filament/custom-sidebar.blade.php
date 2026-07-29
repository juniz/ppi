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
    
    /* Group headings - stronger and more distinct */
    .fi-sidebar-group-label {
        color: rgba(var(--primary-600), 1) !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-top: 0.75rem;
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
</style>
