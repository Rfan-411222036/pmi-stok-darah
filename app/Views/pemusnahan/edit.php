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
                        <li class="breadcrumb-item"><a href="<?= base_url('/pemusnahan') ?>">Pemusnahan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('/pemusnahan/update/' . $pemusnahan['id_pemusnahan']) ?>" method="post">
                        <?= csrf_field() ?>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_kantong">No Kantong</label>
                                    <input type="text" class="form-control" id="no_kantong" name="no_kantong"
                                           value="<?= old('no_kantong', $pemusnahan['no_kantong']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="gol_dar">Golongan Darah</label>
                                    <input type="text" class="form-control" id="gol_dar" name="gol_dar"
                                           value="<?= old('gol_dar', $pemusnahan['gol_dar']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_darah">Jenis Darah</label>
                                    <input type="text" class="form-control" id="jenis_darah" name="jenis_darah"
                                           value="<?= old('jenis_darah', $pemusnahan['jenis_darah']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_expired">Tanggal Expired</label>
                                    <input type="date" class="form-control" id="tanggal_expired" name="tanggal_expired"
                                           value="<?= old('tanggal_expired', date('Y-m-d', strtotime($pemusnahan['tanggal_expired']))) ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_pemusnahan">Tanggal Pemusnahan *</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_pemusnahan" name="tanggal_pemusnahan"
                                           value="<?= old('tanggal_pemusnahan', date('Y-m-d\TH:i', strtotime($pemusnahan['tanggal_pemusnahan']))) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="alasan">Alasan Pemusnahan *</label>
                                    <select class="form-control" id="alasan" name="alasan" required>
                                        <option value="">Pilih Alasan</option>
                                        <option value="expired" <?= old('alasan', $pemusnahan['alasan']) == 'expired' ? 'selected' : '' ?>>Expired</option>
                                        <option value="rusak" <?= old('alasan', $pemusnahan['alasan']) == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                                        <option value="lainnya" <?= old('alasan', $pemusnahan['alasan']) == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="petugas">Petugas *</label>
                                    <input type="text" class="form-control" id="petugas" name="petugas"
                                           value="<?= old('petugas', $pemusnahan['petugas']) ?>"
                                           placeholder="Masukkan nama petugas" required>
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                              placeholder="Masukkan keterangan pemusnahan"><?= old('keterangan', $pemusnahan['keterangan']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="<?= base_url('/pemusnahan') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>
