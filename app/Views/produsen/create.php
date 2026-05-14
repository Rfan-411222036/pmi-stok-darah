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
                        <li class="breadcrumb-item"><a href="<?= base_url('/produsen') ?>">BDRS</a></li>
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
                    <form action="<?= base_url('/produsen/store') ?>" method="post">
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
                                    <label for="nama">Nama BDRS *</label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?= old('nama') ?>" 
                                           placeholder="Masukkan nama BDRS" required>
                                </div>

                                <div class="form-group">
                                    <label for="jenis">Jenis BDRS *</label>
                                    <select class="form-control" id="jenis" name="jenis" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="perorangan" <?= old('jenis') == 'perorangan' ? 'selected' : '' ?>>Perorangan</option>
                                        <option value="perusahaan" <?= old('jenis') == 'perusahaan' ? 'selected' : '' ?>>Perusahaan</option>
                                        <option value="instansi" <?= old('jenis') == 'instansi' ? 'selected' : '' ?>>Instansi</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_darah">Jenis Darah</label>
                                    <input type="text" class="form-control" id="jenis_darah" name="jenis_darah" 
                                           value="<?= old('jenis_darah') ?>" 
                                           placeholder="Masukkan jenis darah (contoh: A+, B-, O+)">
                                </div>

                                <div class="form-group">
                                    <label for="no_kantong">No. Kantong Darah *</label>
                                    <input type="text" class="form-control" id="no_kantong" name="no_kantong" 
                                           value="<?= old('no_kantong') ?>" 
                                           placeholder="Masukkan nomor kantong darah" required>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status Darah *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="masih layak" <?= old('status') == 'masih layak' ? 'selected' : '' ?>>Masih Layak</option>
                                        <option value="expired" <?= old('status') == 'expired' ? 'selected' : '' ?>>Expired</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" 
                                              placeholder="Masukkan alamat lengkap" rows="4"><?= old('alamat') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="<?= base_url('/produsen') ?>" class="btn btn-secondary">
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