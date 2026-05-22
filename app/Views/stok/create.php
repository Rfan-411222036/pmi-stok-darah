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
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('/stok/store') ?>" method="post">
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
                                           value="<?= old('no_kantong') ?>" 
                                           placeholder="Contoh: KB001/XI/2024" required>
                                    <small class="form-text text-muted">Format: KBxxx/bulan/tahun</small>
                                </div>

                                <div class="form-group">
                                    <label for="idprodusen">Bank Darah Rumah Sakit *</label>
                                    <select class="form-control" id="idprodusen" name="idprodusen" required>
                                        <option value="">Pilih Bank Darah Rumah Sakit</option>
                                        <?php foreach ($produsen as $p): ?>
                                            <option value="<?= $p['idprodusen'] ?>" <?= old('idprodusen') == $p['idprodusen'] ? 'selected' : '' ?>>
                                                <?= $p['nama'] ?> (<?= $p['jenis'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="goldar">Golongan Darah *</label>
                                    <select class="form-control" id="goldar" name="goldar" required>
                                        <option value="">Pilih Golongan</option>
                                        <option value="A" <?= old('goldar') == 'A' ? 'selected' : '' ?>>A</option>
                                        <option value="B" <?= old('goldar') == 'B' ? 'selected' : '' ?>>B</option>
                                        <option value="AB" <?= old('goldar') == 'AB' ? 'selected' : '' ?>>AB</option>
                                        <option value="O" <?= old('goldar') == 'O' ? 'selected' : '' ?>>O</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="rhesus">Rhesus *</label>
                                    <select class="form-control" id="rhesus" name="rhesus" required>
                                        <option value="+" <?= old('rhesus') == '+' ? 'selected' : '' ?>>Positif (+)</option>
                                        <option value="-" <?= old('rhesus') == '-' ? 'selected' : '' ?>>Negatif (-)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenisdarah">Jenis Darah *</label>
                                    <select class="form-control" id="jenisdarah" name="jenisdarah" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="Whole" <?= old('jenisdarah') == 'Whole' ? 'selected' : '' ?>>Whole Blood</option>
                                        <option value="PRC" <?= old('jenisdarah') == 'PRC' ? 'selected' : '' ?>>Packed Red Cells</option>
                                        <option value="TC" <?= old('jenisdarah') == 'TC' ? 'selected' : '' ?>>Thrombocyte Concentrate</option>
                                        <option value="FFP" <?= old('jenisdarah') == 'FFP' ? 'selected' : '' ?>>Fresh Frozen Plasma</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="volume">Volume (ml) *</label>
                                    <input type="number" class="form-control" id="volume" name="volume" 
                                           value="<?= old('volume', 450) ?>" 
                                           placeholder="Masukkan volume" min="1" required>
                                    <small class="form-text text-muted">Standar: 450ml untuk Whole Blood</small>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_produksi">Tanggal Produksi *</label>
                                    <input type="date" class="form-control" id="tanggal_produksi" name="tanggal_produksi" 
                                           value="<?= old('tanggal_produksi', date('Y-m-d')) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_expired">Tanggal Expired *</label>
                                    <input type="date" class="form-control" id="tanggal_expired" name="tanggal_expired" 
                                           value="<?= old('tanggal_expired', date('Y-m-d', strtotime('+35 days'))) ?>" required>
                                    <small class="form-text text-muted">Maksimal 35 hari dari tanggal produksi untuk Whole Blood</small>
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" 
                                              placeholder="Masukkan keterangan tambahan" rows="2"><?= old('keterangan') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
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