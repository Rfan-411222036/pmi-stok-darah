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
                        <li class="breadcrumb-item active">Produsen</li>
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
                    <h3 class="card-title">Daftar Produsen</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('/produsen/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Produsen
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('/produsen') ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari nama, jenis, telepon..." value="<?= $search ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <?php if ($search): ?>
                                            <a href="<?= base_url('/produsen') ?>" class="btn btn-default">
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
                            <!-- Di dalam tabel, tambahkan kolom status -->
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Produsen</th>
                                    <th>Jenis</th>
                                    <th>Telepon</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                <?php foreach ($produsen as $item): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $item['nama'] ?></td>
                                        <td>
                                            <span class="badge badge-info"><?= $item['jenis'] ?></span>
                                        </td>
                                        <td><?= $item['telepon'] ?></td>
                                        <td><?= $item['email'] ?></td>
                                        <td>
                                                <?php $active = isset($item['is_active']) ? $item['is_active'] : 1; ?>
                                                <?php if ($active == 1): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Non-Aktif</span>
                                                <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('/produsen/edit/' . $item['idprodusen']) ?>"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ($active == 1): ?>
                                                <a href="<?= base_url('/produsen/delete/' . $item['idprodusen']) ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menonaktifkan produsen ini?')">
                                                    <i class="fas fa-trash"></i> Nonaktifkan
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled>Non-Aktif</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted">
                                Menampilkan <?= count($produsen) ?> dari <?= $pager->getTotal() ?> data
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