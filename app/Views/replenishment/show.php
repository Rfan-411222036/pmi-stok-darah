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
                        <li class="breadcrumb-item"><a href="<?= base_url('/replenishment') ?>">Replenishment</a></li>
                        <li class="breadcrumb-item active">Detail #<?= $ticket['id_replenishment'] ?></li>
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Tiket #<?= $ticket['id_replenishment'] ?></h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="180">BDRS Node</th>
                                    <td><?= esc($ticket['nama_produsen']) ?></td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td><?= esc($ticket['alamat_produsen'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Golongan Darah</th>
                                    <td>
                                        <?php if ($ticket['gol_dar']): ?>
                                            <span class="badge badge-danger"><?= $ticket['gol_dar'] ?><?= $ticket['rhesus'] ?></span>
                                            <?= esc($ticket['jenis_darah']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Semua jenis (general replenishment)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unit Diminta</th>
                                    <td><strong><?= $ticket['requested_units'] ?> unit</strong></td>
                                </tr>
                                <tr>
                                    <th>Unit Dipenuhi</th>
                                    <td><?= $ticket['fulfilled_units'] ?> unit</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($ticket['status'] === 'pending'): ?>
                                            <span class="badge badge-warning badge-lg">Pending</span>
                                        <?php elseif ($ticket['status'] === 'fulfilled'): ?>
                                            <span class="badge badge-success badge-lg">Dipenuhi</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary badge-lg">Dibatalkan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Request</th>
                                    <td><?= $ticket['requested_at'] ? date('d/m/Y H:i', strtotime($ticket['requested_at'])) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Dipenuhi</th>
                                    <td><?= $ticket['fulfilled_at'] ? date('d/m/Y H:i', strtotime($ticket['fulfilled_at'])) : '-' ?></td>
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
                    <!-- Fulfill -->
                    <div class="card card-success">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-check"></i> Penuhi Tiket</h3></div>
                        <div class="card-body">
                            <form action="<?= base_url('/replenishment/fulfill/' . $ticket['id_replenishment']) ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label>Unit yang Dikirim</label>
                                    <input type="number" name="fulfilled_units" class="form-control"
                                           min="1" max="<?= $ticket['requested_units'] ?>"
                                           value="<?= $ticket['requested_units'] ?>" required>
                                    <small class="text-muted">Diminta: <?= $ticket['requested_units'] ?> unit</small>
                                </div>
                                <div class="form-group">
                                    <label>Catatan</label>
                                    <textarea name="notes" class="form-control" rows="2"
                                              placeholder="Catatan pengiriman..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-paper-plane"></i> Penuhi & Kirim
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Cancel -->
                    <div class="card card-danger">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-times"></i> Batalkan Tiket</h3></div>
                        <div class="card-body">
                            <p class="text-muted">Batalkan tiket ini jika tidak perlu diproses.</p>
                            <a href="<?= base_url('/replenishment/cancel/' . $ticket['id_replenishment']) ?>"
                               class="btn btn-danger btn-block"
                               onclick="return confirm('Batalkan tiket ini?')">
                                <i class="fas fa-ban"></i> Batalkan Tiket
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <a href="<?= base_url('/replenishment') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>
