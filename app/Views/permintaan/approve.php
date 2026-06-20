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
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="mb-2">
                <small class="text-muted">DEBUG: session role = <?= esc(session()->get('role')) ?>, user id = <?= esc(session()->get('id_user')) ?></small>
            </div>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Detail Permintaan</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Rumah Sakit</th>
                                <td><?= esc($permintaan['nama_rs'] ?? $permintaan['id_rs']) ?></td>
                            </tr>
                            <tr>
                                <th>BDRS</th>
                                <td><?= esc($permintaan['nama_produsen'] ?? $permintaan['id_produsen']) ?></td>
                            </tr>
                            <tr>
                                <th>Jumlah</th>
                                <td><?= esc($permintaan['jumlah']) ?></td>
                            </tr>
                            <tr>
                                <th>Golongan</th>
                                <td><?= esc($permintaan['gol_dar'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Jenis</th>
                                <td><?= esc($permintaan['jenis'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Diagnosa</th>
                                <td><?= esc($permintaan['keterangan'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Nama Penerima</th>
                                <td><?= esc($permintaan['nama_penerima'] ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>

                    <form action="<?= base_url('/permintaan/approve/' . $permintaan['id_permintaan']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="id_bag">No Kantong Darah *</label>
                            <select name="id_bag" id="id_bag" class="form-control" required>
                                <option value="">Pilih nomor kantong darah</option>
                                <?php foreach ($available_stock as $bag): ?>
                                    <option value="<?= $bag['id_bag'] ?>" <?= old('id_bag') == $bag['id_bag'] ? 'selected' : '' ?>>
                                        <?= esc($bag['no_kantong']) ?> - <?= esc($bag['gol_dar']) ?> <?= esc($bag['jenis_darah']) ?> - Exp <?= date('d/m/Y', strtotime($bag['tanggal_expired'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="approval_note">Keterangan Persetujuan *</label>
                            <textarea name="approval_note" id="approval_note" class="form-control" rows="3" required><?= old('approval_note') ?></textarea>
                        </div>

                        <?php if (empty($available_stock)): ?>
                            <div class="alert alert-warning">
                                Tidak ada kantong darah yang sesuai dengan golongan/jenis permintaan ini.
                                Silakan perbarui stok atau pilih permintaan lain.
                            </div>
                            <?php if (!empty($alternative_stock)): ?>
                                <div class="alert alert-info">
                                    Namun ada stok lain yang tersedia untuk BDRS ini:
                                    <ul class="mb-0">
                                        <?php foreach ($alternative_stock as $alt): ?>
                                            <li><?= esc($alt['no_kantong']) ?> - <?= esc($alt['gol_dar']) ?> <?= esc($alt['jenis_darah']) ?> - Exp <?= date('d/m/Y', strtotime($alt['tanggal_expired'])) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Setujui Permintaan</button>
                            <a href="<?= base_url('/permintaan') ?>" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>