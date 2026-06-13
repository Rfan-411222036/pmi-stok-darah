<?= $this->include('templates/header') ?>
<?= $this->include('templates/navbar') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $page_title ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <p class="text-muted">Tidak ada notifikasi.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($notifications as $n): ?>
                                <li class="list-group-item<?= $n['is_read'] ? '' : ' font-weight-bold' ?>">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong><?= esc($n['title']) ?></strong>
                                            <p class="mb-1"><?= esc($n['message']) ?></p>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
                                        </div>
                                        <div>
                                            <?php if (!$n['is_read']): ?>
                                                <a href="<?= base_url('/notifications/mark-read/' . $n['id']) ?>" class="btn btn-sm btn-outline-primary">Tandai sudah dibaca</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="mt-3">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>
