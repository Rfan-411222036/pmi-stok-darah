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
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Replenishment</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Tiket ini dibuat otomatis oleh sistem saat stok BDRS turun di bawah threshold minimum.
                Admin/PMI memproses tiket dengan mengirim stok ke BDRS bersangkutan.
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Tiket Replenishment</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">
                            <?= count(array_filter($tickets, fn($t) => $t['status'] === 'pending')) ?> Pending
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('/replenishment') ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                           placeholder="Cari BDRS, golongan, status..."
                                           value="<?= esc($search) ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                        <?php if ($search): ?>
                                            <a href="<?= base_url('/replenishment') ?>" class="btn btn-default"><i class="fas fa-times"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>BDRS Node</th>
                                    <th>Golongan / Jenis</th>
                                    <th>Diminta</th>
                                    <th>Dipenuhi</th>
                                    <th>Status</th>
                                    <th>Tanggal Request</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tickets)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                    <?php foreach ($tickets as $t): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($t['nama_produsen']) ?></td>
                                        <td>
                                            <?php if ($t['gol_dar']): ?>
                                                <span class="badge badge-danger"><?= $t['gol_dar'] ?><?= $t['rhesus'] ?></span>
                                                <?= esc($t['jenis_darah']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Semua jenis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $t['requested_units'] ?> unit</td>
                                        <td><?= $t['fulfilled_units'] ?> unit</td>
                                        <td>
                                            <?php if ($t['status'] === 'pending'): ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php elseif ($t['status'] === 'fulfilled'): ?>
                                                <span class="badge badge-success">Dipenuhi</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Dibatalkan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $t['requested_at'] ? date('d/m/Y H:i', strtotime($t['requested_at'])) : '-' ?></td>
                                        <td>
                                            <a href="<?= base_url('/replenishment/' . $t['id_replenishment']) ?>"
                                               class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Tidak ada tiket replenishment</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between">
                        <p class="text-muted">Menampilkan <?= count($tickets) ?> dari <?= $pager->getTotal() ?> tiket</p>
                        <div><?= $pager->links() ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>
