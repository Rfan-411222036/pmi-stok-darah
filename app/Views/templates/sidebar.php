<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url('/dashboard') ?>" class="brand-link">
        <span class="brand-text font-weight-light">PMI Stok Darah</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php 
                    use App\Helpers\MenuHelper;
                    $menus = MenuHelper::getMenusByRole(session()->get('role'));
                    echo MenuHelper::renderMenuItems($menus);
                ?>
            </ul>
        </nav>
    </div>
</aside>