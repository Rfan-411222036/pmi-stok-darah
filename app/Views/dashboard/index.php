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
                    <div class="float-sm-right text-sm">
                        <span class="badge badge-info"><?= date('d F Y') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Warning untuk stok mendekati expired -->
            <?php if ($stok_mendekati_expired > 0): ?>
            <div class="alert alert-warning">
                <i class="icon fas fa-exclamation-triangle"></i>
                Ada <strong><?= $stok_mendekati_expired ?></strong> stok darah yang mendekati tanggal expired!
            </div>
            <?php endif; ?>

            <!-- Warning untuk stok expired -->
            <?php if ($stok_expired > 0): ?>
            <div class="alert alert-danger">
                <i class="icon fas fa-exclamation-circle"></i>
                Ada <strong><?= $stok_expired ?></strong> stok darah yang sudah expired dan perlu pemusnahan!
            </div>
            <?php endif; ?>

            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= number_format($total_stok, 0, ',', '.') ?></h3>
                            <p>Stok Darah Tersedia</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <a href="<?= base_url('/stok') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= number_format($total_distribusi, 0, ',', '.') ?></h3>
                            <p>Total Distribusi</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <a href="<?= base_url('/distribusi') ?>" class="small-box-footer">
                            Hari ini: <?= $distribusi_hari_ini ?> | Bulan ini: <?= $distribusi_bulan_ini ?>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($total_produsen, 0, ',', '.') ?></h3>
                            <p>Data Produsen</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?= base_url('/produsen') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($total_rs, 0, ',', '.') ?></h3>
                            <p>Rumah Sakit</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <a href="<?= base_url('/rumahsakit') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Second Row of Small Boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?= number_format($total_users, 0, ',', '.') ?></h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <a href="<?= base_url('/users') ?>" class="small-box-footer">
                            Admin: <?= $total_admins ?> | Staff: <?= $total_staff ?>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3><?= number_format($total_pemusnahan, 0, ',', '.') ?></h3>
                            <p>Pemusnahan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trash"></i>
                        </div>
                        <a href="<?= base_url('/pemusnahan') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($stok_mendekati_expired, 0, ',', '.') ?></h3>
                            <p>Mendekati Expired</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="<?= base_url('/stok') ?>" class="small-box-footer">Perlu perhatian</a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($stok_expired, 0, ',', '.') ?></h3>
                            <p>Stok Expired</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-skull-crossbones"></i>
                        </div>
                        <a href="<?= base_url('/pemusnahan/create') ?>" class="small-box-footer">Segera musnahkan</a>
                    </div>
                </div>
            </div>

            <!-- Card Laporan PDF -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf"></i> Laporan Stok Darah
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Download laporan stok darah dalam format PDF dengan statistik lengkap.</p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('/dashboard/laporan/download') ?>" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf"></i> Laporan Distribusi
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Download laporan distribusi darah ke rumah sakit dalam format PDF.</p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('/dashboard/laporan/distribusi') ?>" class="btn btn-success btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf"></i> Laporan Pemusnahan
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Download laporan pemusnahan darah kadaluarsa dalam format PDF.</p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('/dashboard/laporan/pemusnahan') ?>" class="btn btn-warning btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf"></i> Laporan Retur
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Download laporan retur darah dari rumah sakit dalam format PDF.</p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('/dashboard/laporan/retur') ?>" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Stok by Golongan Darah -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stok Darah per Golongan</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($stok_by_golongan)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Golongan Darah</th>
                                                <th>Jumlah Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stok_by_golongan as $stok): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-danger"><?= $stok['goldar'] ?></span>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($stok['total'], 0, ',', '.') ?></strong> kantong
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">Tidak ada data stok</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Stok by Jenis Darah -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stok Darah per Jenis</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($stok_by_jenis)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Jenis Darah</th>
                                                <th>Jumlah Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stok_by_jenis as $stok): ?>
                                            <tr>
                                                <td><?= $stok['jenisdarah'] ?></td>
                                                <td>
                                                    <strong><?= number_format($stok['total'], 0, ',', '.') ?></strong> kantong
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">Tidak ada data stok</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribusi Terbaru -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Distribusi Terbaru</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recent_distribusi)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No Kantong</th>
                                                <th>Golongan</th>
                                                <th>Rumah Sakit</th>
                                                <th>Penerima</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_distribusi as $dist): ?>
                                            <tr>
                                                <td><?= $dist['no_kantong'] ?></td>
                                                <td>
                                                    <span class="badge badge-danger"><?= $dist['goldar'] ?></span>
                                                </td>
                                                <td><?= $dist['nama_rs'] ?></td>
                                                <td><?= $dist['penerima'] ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($dist['tanggal_distribusi'])) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center p-3">Tidak ada data distribusi</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>