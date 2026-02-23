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
                <li class="nav-item">
                    <a href="<?= base_url('/dashboard') ?>" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('/produsen') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Produsen</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/rumahsakit') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Rumah Sakit</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/stok') ?>" class="nav-link">
                        <i class="nav-icon fas fa-tint"></i>
                        <p>Stok Darah</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/distribusi') ?>" class="nav-link">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Distribusi</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/pemusnahan') ?>" class="nav-link">
                        <i class="nav-icon fas fa-trash"></i>
                        <p>Pemusnahan</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/return') ?>" class="nav-link">
                        <i class="nav-icon fas fa-undo"></i>
                        <p>Return Darah</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/users') ?>" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>User Management</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>