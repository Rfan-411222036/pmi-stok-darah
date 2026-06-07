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
                        <li class="breadcrumb-item active">Distribusi</li>
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
                    <h3 class="card-title">Daftar Distribusi Darah</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('/distribusi/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Distribusi
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('/distribusi') ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari no kantong, rumah sakit, penerima..." value="<?= $search ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <?php if ($search): ?>
                                            <a href="<?= base_url('/distribusi') ?>" class="btn btn-default">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card mb-3">
                        <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-0">Permintaan Darah</h5>
                                <p class="text-muted mb-0">Form permintaan hanya tersedia di halaman Permintaan.</p>
                            </div>
                            <div class="btn-group">
                                <a href="<?= base_url('/permintaan/create') ?>" class="btn btn-primary">
                                    <i class="fas fa-file-alt"></i> Ajukan Permintaan
                                </a>
                                <a href="<?= base_url('/permintaan') ?>" class="btn btn-secondary">
                                    <i class="fas fa-list"></i> Lihat Daftar Permintaan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>No Kantong</th>
                                    <th>Golongan Darah</th>
                                    <th>Sumber (BDRS)</th>
                                    <th>Rumah Sakit</th>
                                    <th>Penerima</th>
                                    <th>Keperluan</th>
                                    <th>No Permintaan</th>
                                    <th>Tanggal Distribusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($distribusi)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                    <?php foreach ($distribusi as $item): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $item['no_kantong'] ?></td>
                                        <td>
                                            <span class="badge badge-danger"><?= $item['gol_dar'] ?></span>
                                        </td>
                                        <td><?= $item['nama_produsen'] ?: '-' ?></td>
                                        <td><?= $item['nama_rs'] ?></td>
                                        <td><?= $item['penerima'] ?></td>
                                        <td><?= $item['keperluan'] ?: '-' ?></td>
                                        <td><?= $item['no_permintaan'] ?: '-' ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($item['tanggal_distribusi'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Tidak ada data distribusi</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted">
                                Menampilkan <?= count($distribusi) ?> dari <?= $pager->getTotal() ?> data
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
