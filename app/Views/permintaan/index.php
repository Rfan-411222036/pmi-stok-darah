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
                    <div class="float-sm-right">
                        <a href="<?= base_url('/permintaan/create') ?>" class="btn btn-primary btn-sm">Buat Permintaan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>RS</th>
                                    <th>BDRS</th>
                                    <th>Jumlah</th>
                                    <th>Gol</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list)): $no = 1; ?>
                                    <?php foreach ($list as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['id_rs'] ?></td>
                                        <td><?= $row['id_produsen'] ?></td>
                                        <td><?= $row['jumlah'] ?></td>
                                        <td><?= $row['gol_dar'] ?></td>
                                        <td><?= ucfirst($row['status']) ?></td>
                                        <td>
                                            <?php if (session()->get('role') === 'bdrs' && $row['status'] === 'pending'): ?>
                                                <a href="<?= base_url('/permintaan/approve/'.$row['id_permintaan']) ?>" class="btn btn-sm btn-success">Setujui</a>
                                                <a href="<?= base_url('/permintaan/reject/'.$row['id_permintaan']) ?>" class="btn btn-sm btn-danger">Tolak</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted">Tidak ada permintaan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>
