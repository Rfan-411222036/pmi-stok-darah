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
                    <form action="<?= base_url('/stok/update/' . $stok['idbag']) ?>" method="post">
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
                                    <label for="idprodusen">Bank Darah Rumah Sakit *</label>
                                    <select class="form-control" id="idprodusen" name="idprodusen" required>
                                        <option value="">Pilih Bank Darah Rumah Sakit</option>
                                        <?php foreach ($produsen as $p): ?>
                                            <?php $jenisLabel = ($p['jenis'] == 'pemerintah') ? 'Pemerintah' : (($p['jenis'] == 'swasta') ? 'Swasta' : 'Umum'); ?>
                                            <option value="<?= $p['idprodusen'] ?>" <?= old('idprodusen', $stok['idprodusen']) == $p['idprodusen'] ? 'selected' : '' ?>>
                                                <?= $p['nama'] ?> (<?= $jenisLabel ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="goldar">Golongan Darah *</label>
                                    <select class="form-control" id="goldar" name="goldar" required>
                                        <option value="">Pilih Golongan</option>
                                        <option value="A" <?= old('goldar', $stok['goldar']) == 'A' ? 'selected' : '' ?>>A</option>
                                        <option value="B" <?= old('goldar', $stok['goldar']) == 'B' ? 'selected' : '' ?>>B</option>
                                        <option value="AB" <?= old('goldar', $stok['goldar']) == 'AB' ? 'selected' : '' ?>>AB</option>
                                        <option value="O" <?= old('goldar', $stok['goldar']) == 'O' ? 'selected' : '' ?>>O</option>
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
                                    <label for="jenisdarah">Jenis Darah *</label>
                                    <select class="form-control" id="jenisdarah" name="jenisdarah" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="Whole" <?= old('jenisdarah', $stok['jenisdarah']) == 'Whole' ? 'selected' : '' ?>>Whole Blood</option>
                                        <option value="PRC" <?= old('jenisdarah', $stok['jenisdarah']) == 'PRC' ? 'selected' : '' ?>>Packed Red Cells</option>
                                        <option value="TC" <?= old('jenisdarah', $stok['jenisdarah']) == 'TC' ? 'selected' : '' ?>>Thrombocyte Concentrate</option>
                                        <option value="FFP" <?= old('jenisdarah', $stok['jenisdarah']) == 'FFP' ? 'selected' : '' ?>>Fresh Frozen Plasma</option>
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