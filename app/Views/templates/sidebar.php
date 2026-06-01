<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4" style="background:#1a1a2e;">
    <!-- Brand Logo -->
    <a href="<?= base_url('/dashboard') ?>" class="brand-link sidebar-brand-link" style="border-bottom:1px solid rgba(255,255,255,0.1); padding:12px 16px; display:flex; align-items:center; gap:10px;">
        <img src="<?= base_url('/icon.png') ?>" alt="BLOODJek" style="width:32px;height:32px;object-fit:contain;">
        <span class="brand-text" style="font-weight:700; font-size:18px; letter-spacing:1px; color:#fff;">BLOOD<span style="color:#c94b4b;">Jek</span></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-2 d-flex align-items-center sidebar-user-panel" style="border-bottom:1px solid rgba(255,255,255,0.08); padding:0 12px 12px;">
            <div class="image flex-shrink-0">
                <div class="sidebar-avatar">
                    <?= strtoupper(substr(session()->get('nama') ?? 'U', 0, 1)) ?>
                </div>
            </div>
            <div class="info sidebar-user-info" style="padding-left:10px; overflow:hidden;">
                <a href="#" class="d-block text-truncate" style="color:#fff;font-weight:600;font-size:13px;line-height:1.3;">
                    <?= esc(session()->get('nama') ?? '-') ?>
                </a>
                <span style="color:rgba(255,255,255,0.45);font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">
                    <?= esc(session()->get('role') ?? '-') ?>
                </span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-1">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"
                style="font-size:12px;font-weight:600;letter-spacing:0.5px;">
                <?php
                    use App\Helpers\MenuHelper;
                    $menus = MenuHelper::getMenusByRole(session()->get('role'));
                    echo MenuHelper::renderMenuItems($menus);
                ?>
            </ul>
        </nav>
    </div>
</aside>

<style>
/* ── Colour tokens ── */
:root {
    --sb-bg:        #1a1a2e;
    --sb-red:       rgba(185, 70, 70, 0.85);
    --sb-red-hover: rgba(185, 70, 70, 0.14);
    --sb-red-sub:   rgba(185, 70, 70, 0.55);
}

/* Sidebar base */
.main-sidebar, .main-sidebar .sidebar { background: var(--sb-bg) !important; }

/* Avatar */
.sidebar-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--sb-red);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: #fff; font-size: 13px;
    flex-shrink: 0;
}

/* Hide user text when sidebar is collapsed */
.sidebar-mini.sidebar-collapse .sidebar-user-info { display: none !important; }
.sidebar-mini.sidebar-collapse .sidebar-user-panel {
    justify-content: center !important;
    padding: 0 0 12px 0 !important;
}
.sidebar-mini.sidebar-collapse .sidebar-user-panel .image {
    margin: 0 auto !important;
}

/* Center brand link when collapsed */
.sidebar-mini.sidebar-collapse .sidebar-brand-link {
    justify-content: center !important;
    padding: 12px 8px !important;
}
.sidebar-mini.sidebar-collapse .sidebar-brand-link .brand-text { display: none !important; }

/* Nav link default */
.main-sidebar .nav-sidebar .nav-link {
    color: rgba(255,255,255,0.62) !important;
    border-radius: 6px !important;
    margin: 1px 8px !important;
    padding: 9px 12px !important;
    transition: background 0.18s ease, color 0.18s ease !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    letter-spacing: 0.4px !important;
}

/* Nav link hover */
.main-sidebar .nav-sidebar .nav-link:hover {
    background: var(--sb-red-hover) !important;
    color: #fff !important;
}

/* Active nav link */
.main-sidebar .nav-sidebar .nav-link.active {
    background: var(--sb-red) !important;
    color: #fff !important;
    box-shadow: none !important;
}

/* Icons */
.main-sidebar .nav-sidebar .nav-icon {
    color: rgba(255,255,255,0.4) !important;
    font-size: 14px !important;
    min-width: 1.6rem !important;
}
.main-sidebar .nav-sidebar .nav-link.active .nav-icon,
.main-sidebar .nav-sidebar .nav-link:hover .nav-icon {
    color: rgba(255,255,255,0.9) !important;
}

/* Collapsed icon-only active: just tint the icon, no block */
.sidebar-mini.sidebar-collapse .main-sidebar .nav-sidebar .nav-link.active {
    background: transparent !important;
    box-shadow: none !important;
}
.sidebar-mini.sidebar-collapse .main-sidebar .nav-sidebar .nav-link.active .nav-icon {
    color: #c97070 !important;
}
.sidebar-mini.sidebar-collapse .main-sidebar .nav-sidebar .nav-link:hover {
    background: rgba(185,70,70,0.10) !important;
}

/* Treeview sub-menu */
.main-sidebar .nav-treeview {
    background: rgba(0,0,0,0.15) !important;
    border-radius: 6px !important;
    margin: 2px 8px !important;
    padding: 4px 0 !important;
}
.main-sidebar .nav-treeview .nav-link {
    margin: 1px 4px !important;
    padding: 8px 10px 8px 22px !important;
    font-size: 11px !important;
    color: rgba(255,255,255,0.52) !important;
}
.main-sidebar .nav-treeview .nav-link .nav-icon {
    font-size: 12px !important;
    min-width: 1.5rem !important;
    margin-right: 6px !important;
}
.main-sidebar .nav-treeview .nav-link:hover {
    color: #fff !important;
    background: rgba(185,70,70,0.10) !important;
}
.main-sidebar .nav-treeview .nav-link.active {
    background: var(--sb-red-sub) !important;
    color: #fff !important;
    box-shadow: none !important;
}

/* Arrow icon */
.main-sidebar .nav-sidebar .nav-link .right { color: rgba(255,255,255,0.25) !important; }
.main-sidebar .nav-sidebar .nav-link.active .right,
.main-sidebar .nav-sidebar .nav-link:hover .right { color: rgba(255,255,255,0.7) !important; }
</style>