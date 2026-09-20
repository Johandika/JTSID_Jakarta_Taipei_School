<style>
    /* JTS admin brand theme. */
    :root {
        --brand-green: #176443;
        --brand-green-hover: #125236;
        --brand-green-light: #eaf4ee;
        --brand-green-border: #b8deca;
        --canvas-bg: #f8faf9;
    }

    /* Page background & full responsive container overrides */
    body, .fi-main-ctn, .fi-layout {
        background-color: var(--canvas-bg) !important;
    }

    /* Comfortable spacing for the full-width panel container. */
    html body .fi-main,
    html body main.fi-main {
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
        padding-top: 1.5rem !important;
        padding-bottom: 2.5rem !important;
    }

    @media (min-width: 1024px) {
        html body .fi-main,
        html body main.fi-main {
            padding-left: 2rem !important;
            padding-right: 2rem !important;
            padding-top: 2rem !important;
            padding-bottom: 3rem !important;
        }
    }

    /* Clean white sidebar with subtle border */
    .fi-sidebar {
        background-color: #ffffff !important;
        border-right: 1px solid #eaecf0 !important;
        box-shadow: none !important;
    }

    .fi-sidebar-header {
        min-height: 5.5rem;
        padding-top: 0.875rem;
        padding-bottom: 0.875rem;
        border-bottom: 1px solid #f2f4f7 !important;
    }

    .fi-sidebar-header-logo-ctn {
        justify-content: flex-start;
        width: 100%;
    }

    .fi-sidebar-footer {
        padding: 0.875rem;
        border-top: 1px solid #f2f4f7;
        background-color: #ffffff;
    }

    .fi-sidebar-footer .fi-user-menu-trigger {
        width: 100%;
        padding: 0.625rem;
        border-radius: 0.75rem;
    }

    .fi-sidebar-footer .fi-user-menu-trigger:hover,
    .fi-sidebar-footer .fi-user-menu-trigger:focus-visible {
        background-color: #f0f7f3;
    }

    .jts-user-menu-chevron {
        width: 1rem;
        height: 1rem;
        margin-left: auto;
        color: #a1a1aa;
    }

    /* Sidebar navigation items */
    .fi-sidebar-item-btn {
        border-radius: 0.625rem !important;
        transition: all 0.15s ease-in-out !important;
        font-weight: 500 !important;
        padding: 0.55rem 0.85rem !important;
    }

    /* Inactive sidebar item hover */
    .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-btn:hover {
        background-color: #f0f7f3 !important;
        color: var(--brand-green) !important;
    }

    .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
        color: var(--brand-green) !important;
    }

    /* Active sidebar item */
    .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
        background-color: var(--brand-green) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(23, 100, 67, 0.25) !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-btn .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active .fi-sidebar-item-btn .fi-sidebar-item-icon {
        color: #ffffff !important;
    }

    /* Cards & Sections - Clean rounded corners & subtle shadows */
    .fi-section,
    .fi-ta-ctn,
    .fi-wi-stats-overview-stat {
        border-radius: 1rem !important;
        border: 1px solid #eaecf0 !important;
        box-shadow: 0 1px 3px 0 rgba(16, 24, 40, 0.04) !important;
        background-color: #ffffff !important;
    }

    .fi-section-header {
        border-bottom: 1px solid #f2f4f7 !important;
        padding-bottom: 0.875rem !important;
    }

    /* Input focus styles */
    .fi-layout :is(input, select, textarea):focus {
        border-color: var(--brand-green) !important;
        --tw-ring-color: rgba(23, 100, 67, 0.2) !important;
    }

    /* Primary buttons */
    .fi-btn-color-primary {
        background-color: var(--brand-green) !important;
        color: #ffffff !important;
        border-radius: 0.625rem !important;
        font-weight: 600 !important;
        transition: background-color 0.15s ease !important;
    }

    .fi-btn-color-primary:hover {
        background-color: var(--brand-green-hover) !important;
    }

    /* Featured dashboard statistic */
    .stat-card-featured {
        background-color: var(--brand-green-light) !important;
        border: 1px solid var(--brand-green-border) !important;
    }
    .stat-card-featured .fi-wi-stats-overview-stat-value {
        color: var(--brand-green) !important;
        font-weight: 700 !important;
    }

    .jts-dashboard-header {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1rem;
    }

    .jts-dashboard-eyebrow,
    .jts-dashboard-actions,
    .jts-dashboard-status {
        display: flex;
        align-items: center;
    }

    .jts-dashboard-eyebrow {
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 0.35rem;
        color: #71717a;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .jts-dashboard-eyebrow-current {
        color: #27272a;
        font-weight: 600;
    }

    .jts-dashboard-status {
        gap: 0.35rem;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        background-color: var(--brand-green-light);
        color: var(--brand-green);
        font-size: 0.6875rem;
        font-weight: 600;
    }

    .jts-dashboard-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        background-color: var(--brand-green);
    }

    .jts-dashboard-title {
        margin: 0;
        color: #18181b;
        font-size: 1.625rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.2;
    }

    .jts-dashboard-description {
        margin: 0.35rem 0 0;
        color: #71717a;
        font-size: 0.875rem;
    }

    .jts-dashboard-actions {
        flex-wrap: wrap;
        gap: 0.625rem;
    }

    .jts-attention-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .jts-attention-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        border: 1px solid #e4e4e7;
        border-radius: 0.75rem;
        color: inherit;
        text-decoration: none;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .jts-attention-item:hover,
    .jts-attention-item:focus-visible {
        border-color: var(--brand-green);
        background-color: #f0f7f3;
        outline: none;
    }

    .jts-attention-item-warning:hover,
    .jts-attention-item-warning:focus-visible {
        border-color: #d97706;
        background-color: #fffbeb;
    }

    .jts-attention-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .jts-attention-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: none;
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        background-color: var(--brand-green-light);
        color: var(--brand-green);
    }

    .jts-attention-item-warning .jts-attention-icon {
        background-color: #fef3c7;
        color: #b45309;
    }

    .jts-attention-icon svg {
        width: 20px;
        height: 20px;
    }

    .jts-attention-title,
    .jts-attention-description {
        display: block;
    }

    .jts-attention-title {
        color: #18181b;
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .jts-attention-description,
    .jts-attention-footer {
        color: #71717a;
        font-size: 0.75rem;
    }

    .jts-attention-arrow {
        flex: none;
        width: 16px;
        height: 16px;
        color: #a1a1aa;
    }

    .jts-attention-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .jts-attention-footer strong {
        color: var(--brand-green);
        font-weight: 700;
    }

    @media (min-width: 640px) {
        .jts-dashboard-header {
            flex-direction: row;
            align-items: center;
        }
    }
</style>
