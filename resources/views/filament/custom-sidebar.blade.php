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
</style>
