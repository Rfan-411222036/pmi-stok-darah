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
                        <li class="breadcrumb-item"><a href="<?= base_url('/recall') ?>">Recall Stok</a></li>
                        <li class="breadcrumb-item active">Detail #<?= $ticket['id_recall'] ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-7">
                    <div class="card <?= $ticket['reason'] === 'expired' ? 'card-danger' : 'card-warning' ?>">
                        <div class="card-header">
                            <h3 class="card-title">
                                <?php if ($ticket['reason'] === 'expired'): ?>
                                    <i class="fas fa-skull"></i> Kantong Expired
                                <?php else: ?>
                                    <i class="fas fa-clock"></i> Mendekati Expiry (TTL 14 Hari)
                                <?php endif; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="180">No Kantong</th>
                                    <td><strong><?= esc($ticket['no_kantong']) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Golongan Darah</th>
                                    <td>
                                        <span class="badge badge-danger"><?= $ticket['gol_dar'] ?><?= $ticket['rhesus'] ?></span>
                                        <?= esc($ticket['jenis_darah']) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Volume</th>
                                    <td><?= $ticket['volume'] ?> ml</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Produksi</th>
                                    <td><?= date('d/m/Y', strtotime($ticket['tanggal_produksi'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Expired</th>
                                    <td>
                                        <strong class="text-danger"><?= date('d/m/Y', strtotime($ticket['tanggal_expired'])) ?></strong>
                                        <?php
                                            $daysLeft = (int) ceil((strtotime($ticket['tanggal_expired']) - time()) / 86400);
                                            if ($daysLeft < 0): ?>
                                                <span class="badge badge-danger">Sudah expired <?= abs($daysLeft) ?> hari lalu</span>
                                            <?php elseif ($daysLeft === 0): ?>
                                                <span class="badge badge-danger">Expired hari ini</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning"><?= $daysLeft ?> hari lagi</span>
                                            <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>BDRS Node</th>
                                    <td><?= esc($ticket['nama_produsen']) ?></td>
                                </tr>
                                <tr>
                                    <th>Alasan Recall</th>
                                    <td>
                                        <?php if ($ticket['reason'] === 'expired'): ?>
                                            <span class="badge badge-danger">Expired</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Mendekati Expiry (dalam TTL 14 hari)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($ticket['status'] === 'pending'): ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php elseif ($ticket['status'] === 'swapped'): ?>
                                            <span class="badge badge-success">Di-Swap</span>
                                        <?php elseif ($ticket['status'] === 'destroyed'): ?>
                                            <span class="badge badge-dark">Dimusnahkan</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Dibatalkan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Diflag Pada</th>
                                    <td><?= $ticket['flagged_at'] ? date('d/m/Y H:i', strtotime($ticket['flagged_at'])) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Diproses Pada</th>
                                    <td><?= $ticket['swapped_at'] ? date('d/m/Y H:i', strtotime($ticket['swapped_at'])) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Catatan</th>
                                    <td><?= esc($ticket['notes'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <?php if ($ticket['status'] === 'pending'): ?>
                <div class="col-md-5">
                    <!-- Swap -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-sync-alt"></i> Recall & Swap</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Tandai kantong ini sudah di-recall dan digantikan dengan stok segar dari Central Hub (PMI).
                            </p>
                            <form action="<?= base_url('/recall/swap/' . $ticket['id_recall']) ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label>Catatan Swap</label>
                                    <textarea name="notes" class="form-control" rows="2"
                                              placeholder="Diganti dengan kantong no..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-sync-alt"></i> Konfirmasi Swap
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Destroy -->
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-trash"></i> Musnahkan</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Gunakan jika kantong tidak bisa di-swap dan harus langsung dimusnahkan.
                            </p>
                            <form action="<?= base_url('/recall/destroy/' . $ticket['id_recall']) ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label>Catatan Pemusnahan</label>
                                    <textarea name="notes" class="form-control" rows="2"
                                              placeholder="Alasan pemusnahan langsung..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-block"
                                        onclick="return confirm('Kantong akan dimusnahkan. Lanjutkan?')">
                                    <i class="fas fa-trash"></i> Musnahkan Kantong
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <a href="<?= base_url('/recall') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>
