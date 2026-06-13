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
                        <li class="breadcrumb-item"><a href="<?= base_url('/return') ?>">Return Darah</a></li>
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
                    <form action="<?= base_url('/return/update/' . $return['id_return']) ?>" method="post">
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
                                           value="<?= old('no_kantong', $return['no_kantong']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="gol_dar">Golongan Darah</label>
                                    <input type="text" class="form-control" id="gol_dar" name="gol_dar"
                                           value="<?= old('gol_dar', $return['gol_dar']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_darah">Jenis Darah</label>
                                    <input type="text" class="form-control" id="jenis_darah" name="jenis_darah"
                                           value="<?= old('jenis_darah', $return['jenis_darah']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="nama_rs">Rumah Sakit</label>
                                    <input type="text" class="form-control" id="nama_rs" name="nama_rs"
                                           value="<?= old('nama_rs', $return['nama_rs']) ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_retur">Tanggal Return *</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_retur" name="tanggal_retur"
                                           value="<?= old('tanggal_retur', date('Y-m-d\TH:i', strtotime($return['tanggal_retur']))) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="alasan_return">Alasan Return *</label>
                                    <select class="form-control" id="alasan_return" name="alasan_return" required>
                                        <option value="">Pilih Alasan Return</option>
                                        <option value="Tidak digunakan" <?= old('alasan_return', $return['alasan_return']) == 'Tidak digunakan' ? 'selected' : '' ?>>Tidak digunakan</option>
                                        <option value="Permintaan dibatalkan" <?= old('alasan_return', $return['alasan_return']) == 'Permintaan dibatalkan' ? 'selected' : '' ?>>Permintaan dibatalkan</option>
                                        <option value="Pasien tidak membutuhkan" <?= old('alasan_return', $return['alasan_return']) == 'Pasien tidak membutuhkan' ? 'selected' : '' ?>>Pasien tidak membutuhkan</option>
                                        <option value="Kesalahan permintaan" <?= old('alasan_return', $return['alasan_return']) == 'Kesalahan permintaan' ? 'selected' : '' ?>>Kesalahan permintaan</option>
                                        <option value="Lainnya" <?= old('alasan_return', $return['alasan_return']) == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="kondisi_darah">Kondisi Darah saat Return *</label>
                                    <select class="form-control" id="kondisi_darah" name="kondisi_darah" required>
                                        <option value="baik" <?= old('kondisi_darah', $return['kondisi_darah']) == 'baik' ? 'selected' : '' ?>>Baik - Bisa digunakan kembali</option>
                                        <option value="rusak" <?= old('kondisi_darah', $return['kondisi_darah']) == 'rusak' ? 'selected' : '' ?>>Rusak - Perlu dimusnahkan</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan Tambahan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                              placeholder="Masukkan keterangan tambahan"><?= old('keterangan', $return['keterangan']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="<?= base_url('/return') ?>" class="btn btn-secondary">
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
