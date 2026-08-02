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
        <?php
            $notifCount = 0;
            $notifications = [];
            if (session()->has('id_user')) {
                $notifModel = new \App\Models\NotificationModel();
                $notifCount = $notifModel->where('user_id', session()->get('id_user'))->where('is_read', 0)->countAllResults();
                $notifications = $notifModel->where('user_id', session()->get('id_user'))->orderBy('created_at', 'DESC')->findAll(5);
            }
        ?>

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-bell"></i>
                <?php if ($notifCount): ?>
                        <span class="badge badge-danger navbar-badge"><?= $notifCount ?></span>
                    <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-header">Notifikasi (<?= $notifCount ?>)</span>
                <div class="dropdown-divider"></div>
                <?php if ($notifCount): ?>
                    <?php foreach ($notifications as $n): ?>
                        <a href="<?= base_url('/notifications/mark-read/' . $n['id']) ?>" class="dropdown-item<?= $n['is_read'] ? '' : ' font-weight-bold' ?>">
                            <strong><?= esc($n['title']) ?></strong><br>
                            <small class="text-muted"><?= esc(substr($n['message'], 0, 100)) ?></small>
                            <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></div>
                        </a>
                        <div class="dropdown-divider"></div>
                    <?php endforeach; ?>
                    <a href="<?= base_url('/notifications') ?>" class="dropdown-item text-center">Lihat semua notifikasi</a>
                <?php else: ?>
                    <a class="dropdown-item text-center text-muted">Tidak ada notifikasi</a>
                <?php endif; ?>
            </div>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user mr-1"></i>
                <?= session()->get('nama') ?>
                <span class="badge badge-<?= (session()->get('role') == 'admin' || session()->get('role') == 'pimpinan') ? 'danger' : 'info' ?> ml-1">
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