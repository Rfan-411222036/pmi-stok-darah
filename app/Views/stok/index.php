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
                        <li class="breadcrumb-item active">Stok Darah</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Stok Darah</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('/stok/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Stok
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('/stok') ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari no kantong, golongan, jenis..." value="<?= $search ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <?php if ($search): ?>
                                            <a href="<?= base_url('/stok') ?>" class="btn btn-default">
                                                <i class="fas fa-times"></i>
                                            </a>
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
                                    <th>Bank Darah Rumah Sakit</th>
                                    <th>Golongan Darah</th>
                                    <th>Jenis Darah</th>
                                    <th>Volume</th>
                                    <th>Tanggal Produksi</th>
                                    <th>Tanggal Expired</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stok)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                    <?php foreach ($stok as $item): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $item['no_kantong'] ?></td>
                                        <td><?= $item['nama_produsen'] ?? 'N/A' ?></td>
                                        <td>
                                            <span class="badge badge-danger"><?= $item['goldar'] ?><?= $item['rhesus'] ?></span>
                                        </td>
                                        <td><?= $item['jenisdarah'] ?></td>
                                        <td><?= $item['volume'] ?> ml</td>
                                        <td><?= date('d/m/Y', strtotime($item['tanggal_produksi'])) ?></td>
                                        <td>
                                            <?php 
                                                $expiredDate = strtotime($item['tanggal_expired']);
                                                $today = strtotime(date('Y-m-d'));
                                                $diff = $expiredDate - $today;
                                                $daysLeft = floor($diff / (60 * 60 * 24));
                                                
                                                if ($daysLeft < 0) {
                                                    echo '<span class="badge badge-danger">' . date('d/m/Y', $expiredDate) . ' (EXPIRED)</span>';
                                                } elseif ($daysLeft <= 7) {
                                                    echo '<span class="badge badge-warning">' . date('d/m/Y', $expiredDate) . ' (' . $daysLeft . ' hari)</span>';
                                                } else {
                                                    echo '<span class="badge badge-success">' . date('d/m/Y', $expiredDate) . '</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($item['status'] == 'tersedia'): ?>
                                                <span class="badge badge-success">Tersedia</span>
                                            <?php elseif ($item['status'] == 'terdistribusi'): ?>
                                                <span class="badge badge-info">Terdistribusi</span>
                                            <?php elseif ($item['status'] == 'expired'): ?>
                                                <span class="badge badge-danger">Expired</span>
                                            <?php elseif ($item['status'] == 'musnah'): ?>
                                                <span class="badge badge-secondary">Musnah</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><?= $item['status'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('/stok/edit/' . $item['idbag']) ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ($item['status'] == 'tersedia'): ?>
                                                <a href="<?= base_url('/stok/delete/' . $item['idbag']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus stok ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled title="Stok tidak dapat dihapus">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Tidak ada data stok</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted">
                                Menampilkan <?= count($stok) ?> dari <?= $pager->getTotal() ?> data
                            </p>
                        </div>
                        <div>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>