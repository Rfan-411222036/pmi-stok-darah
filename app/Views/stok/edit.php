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
                        <li class="breadcrumb-item"><a href="<?= base_url('/stok') ?>">Stok Darah</a></li>
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
                    <form action="<?= base_url('/stok/update/' . $stok['id_bag']) ?>" method="post">
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
                                    <label for="no_kantong">No Kantong *</label>
                                    <input type="text" class="form-control" id="no_kantong" name="no_kantong"
                                           value="<?= old('no_kantong', $stok['no_kantong']) ?>"
                                           placeholder="Contoh: KB001/XI/2024" required>
                                </div>

                                <div class="form-group">
                                    <label for="id_produsen">Bank Darah Rumah Sakit *</label>
                                    <select class="form-control" id="id_produsen" name="id_produsen" required>
                                        <option value="">Pilih Bank Darah Rumah Sakit</option>
                                        <?php foreach ($produsen as $p): ?>
                                            <?php $jenisLabel = ($p['jenis'] == 'pemerintah') ? 'Pemerintah' : (($p['jenis'] == 'swasta') ? 'Swasta' : 'Umum'); ?>
                                            <option value="<?= $p['id_produsen'] ?>" <?= old('id_produsen', $stok['id_produsen']) == $p['id_produsen'] ? 'selected' : '' ?>>
                                                <?= $p['nama'] ?> (<?= $jenisLabel ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="gol_dar">Golongan Darah *</label>
                                    <select class="form-control" id="gol_dar" name="gol_dar" required>
                                        <option value="">Pilih Golongan</option>
                                        <option value="A" <?= old('gol_dar', $stok['gol_dar']) == 'A' ? 'selected' : '' ?>>A</option>
                                        <option value="B" <?= old('gol_dar', $stok['gol_dar']) == 'B' ? 'selected' : '' ?>>B</option>
                                        <option value="AB" <?= old('gol_dar', $stok['gol_dar']) == 'AB' ? 'selected' : '' ?>>AB</option>
                                        <option value="O" <?= old('gol_dar', $stok['gol_dar']) == 'O' ? 'selected' : '' ?>>O</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="rhesus">Rhesus *</label>
                                    <select class="form-control" id="rhesus" name="rhesus" required>
                                        <option value="+" <?= old('rhesus', $stok['rhesus']) == '+' ? 'selected' : '' ?>>Positif (+)</option>
                                        <option value="-" <?= old('rhesus', $stok['rhesus']) == '-' ? 'selected' : '' ?>>Negatif (-)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_darah">Jenis Darah *</label>
                                    <select class="form-control" id="jenis_darah" name="jenis_darah" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="Whole" <?= old('jenis_darah', $stok['jenis_darah']) == 'Whole' ? 'selected' : '' ?>>Whole Blood</option>
                                        <option value="PRC" <?= old('jenis_darah', $stok['jenis_darah']) == 'PRC' ? 'selected' : '' ?>>Packed Red Cells</option>
                                        <option value="TC" <?= old('jenis_darah', $stok['jenis_darah']) == 'TC' ? 'selected' : '' ?>>Thrombocyte Concentrate</option>
                                        <option value="FFP" <?= old('jenis_darah', $stok['jenis_darah']) == 'FFP' ? 'selected' : '' ?>>Fresh Frozen Plasma</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="volume">Volume (ml) *</label>
                                    <input type="number" class="form-control" id="volume" name="volume"
                                           value="<?= old('volume', $stok['volume']) ?>"
                                           placeholder="Masukkan volume" min="1" required>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_produksi">Tanggal Produksi *</label>
                                    <input type="date" class="form-control" id="tanggal_produksi" name="tanggal_produksi"
                                           value="<?= old('tanggal_produksi', $stok['tanggal_produksi']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_expired">Tanggal Expired *</label>
                                    <input type="date" class="form-control" id="tanggal_expired" name="tanggal_expired"
                                           value="<?= old('tanggal_expired', $stok['tanggal_expired']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan"
                                              placeholder="Masukkan keterangan tambahan" rows="2"><?= old('keterangan', $stok['keterangan']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="<?= base_url('/stok') ?>" class="btn btn-secondary">
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
