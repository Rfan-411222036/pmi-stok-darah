<?= $this->include('templates/header') ?>
<?= $this->include('templates/navbar') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1><?= $page_title ?></h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Recall Stok</li>
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

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Tiket ini dibuat otomatis saat sistem mendeteksi kantong darah yang mendekati atau melewati batas TTL <strong>14 hari</strong>.
                Proses <em>swap</em> untuk mengganti dengan stok segar dari Central Hub, atau <em>musnahkan</em> jika sudah expired.
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Tiket Recall & Swap</h3>
                    <div class="card-tools">
                        <span class="badge badge-danger">
                            <?= count(array_filter($tickets, fn($t) => $t['status'] === 'pending')) ?> Pending
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('/recall') ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                           placeholder="Cari no kantong, BDRS, status..."
                                           value="<?= esc($search) ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                        <?php if ($search): ?>
                                            <a href="<?= base_url('/recall') ?>" class="btn btn-default"><i class="fas fa-times"></i></a>
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
                                    <th>No Kantong</th>
                                    <th>Golongan</th>
                                    <th>BDRS Node</th>
                                    <th>Alasan</th>
                                    <th>Tgl Expired</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tickets)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                    <?php foreach ($tickets as $t): ?>
                                    <tr class="<?= $t['reason'] === 'expired' ? 'table-danger' : ($t['status'] === 'pending' ? 'table-warning' : '') ?>">
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($t['no_kantong']) ?></td>
                                        <td>
                                            <span class="badge badge-danger"><?= $t['gol_dar'] ?><?= $t['rhesus'] ?></span>
                                            <?= esc($t['jenis_darah']) ?>
                                        </td>
                                        <td><?= esc($t['nama_produsen']) ?></td>
                                        <td>
                                            <?php if ($t['reason'] === 'expired'): ?>
                                                <span class="badge badge-danger"><i class="fas fa-skull"></i> Expired</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning"><i class="fas fa-clock"></i> Mendekati Exp</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($t['tanggal_expired'])) ?></td>
                                        <td>
                                            <?php if ($t['status'] === 'pending'): ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php elseif ($t['status'] === 'swapped'): ?>
                                                <span class="badge badge-success">Di-Swap</span>
                                            <?php elseif ($t['status'] === 'destroyed'): ?>
                                                <span class="badge badge-dark">Dimusnahkan</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Dibatalkan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('/recall/' . $t['id_recall']) ?>"
                                               class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Tidak ada tiket recall</td>
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
