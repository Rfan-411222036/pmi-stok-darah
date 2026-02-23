<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user mr-1"></i>
                <?= session()->get('nama') ?>
                <span class="badge badge-<?= session()->get('role') == 'admin' ? 'danger' : 'info' ?> ml-1">
                    <?= ucfirst(session()->get('role')) ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-header">
                    Logged in as<br>
                    <strong><?= session()->get('nama') ?></strong>
                </span>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('/logout') ?>" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->