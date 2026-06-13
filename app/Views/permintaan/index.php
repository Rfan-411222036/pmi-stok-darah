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
                        <?php $role = session()->get('role'); ?>
                        <?php if ($role === 'rs' || $role === 'admin'): ?>
                            <a href="<?= base_url('/permintaan/create') ?>" class="btn btn-primary btn-sm">Buat Permintaan</a>
                        <?php endif; ?>
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
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning"><?= session()->getFlashdata('warning') ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>RS</th>
                                    <th>BDRS</th>
                                    <th>Jumlah</th>
                                    <th>Gol</th>
                                    <th>No Kantong</th>
                                    <th>Diagnosa</th>
                                    <th>Nama Penerima</th>
                                    <th>Keterangan Setujui</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list)): $no = 1; ?>
                                    <?php foreach ($list as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['nama_rs'] ?? $row['id_rs'] ?></td>
                                        <td><?= $row['nama_produsen'] ?? $row['id_produsen'] ?></td>
                                        <td><?= $row['jumlah'] ?></td>
                                        <td><?= $row['gol_dar'] ?></td>
                                        <td><?= esc($row['no_kantong'] ?? '-') ?></td>
                                        <td><?= esc($row['keterangan'] ?? '-') ?></td>
                                        <td><?= esc($row['nama_penerima'] ?? '-') ?></td>
                                        <td><?= esc($row['approval_note'] ?? '-') ?></td>
                                        <td><?= isset($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-' ?></td>
                                        <td>
                                            <?php if ($row['status'] === 'approved'): ?>
                                                <span class="badge badge-success">Disetujui</span>
                                            <?php elseif ($row['status'] === 'rejected'): ?>
                                                <span class="badge badge-danger">Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Menunggu</span>
                                            <?php endif; ?>
                                        </td>
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
                                    <tr><td colspan="12" class="text-center text-muted">Tidak ada permintaan</td></tr>
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
