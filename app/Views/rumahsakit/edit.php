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
                        <li class="breadcrumb-item"><a href="<?= base_url('/rumahsakit') ?>">Rumah Sakit</a></li>
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
                    <form action="<?= base_url('/rumahsakit/update/' . $rumah_sakit['id_rs']) ?>" method="post">
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
                                    <label for="nama_rs">Nama Rumah Sakit *</label>
                                    <input type="text" class="form-control" id="nama_rs" name="nama_rs"
                                           value="<?= old('nama_rs', $rumah_sakit['nama_rs']) ?>"
                                           placeholder="Masukkan nama rumah sakit" required>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_rs">Jenis Rumah Sakit *</label>
                                    <select class="form-control" id="jenis_rs" name="jenis_rs" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="umum" <?= old('jenis_rs', $rumah_sakit['jenis_rs']) == 'umum' ? 'selected' : '' ?>>Umum</option>
                                        <option value="khusus" <?= old('jenis_rs', $rumah_sakit['jenis_rs']) == 'khusus' ? 'selected' : '' ?>>Khusus</option>
                                        <option value="swasta" <?= old('jenis_rs', $rumah_sakit['jenis_rs']) == 'swasta' ? 'selected' : '' ?>>Swasta</option>
                                        <option value="pemerintah" <?= old('jenis_rs', $rumah_sakit['jenis_rs']) == 'pemerintah' ? 'selected' : '' ?>>Pemerintah</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="telepon">Telepon *</label>
                                    <input type="text" class="form-control" id="telepon" name="telepon"
                                           value="<?= old('telepon', $rumah_sakit['telepon']) ?>"
                                           placeholder="Masukkan nomor telepon" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= old('email', $rumah_sakit['email']) ?>"
                                           placeholder="Masukkan email">
                                </div>

                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat"
                                              placeholder="Masukkan alamat lengkap" rows="4"><?= old('alamat', $rumah_sakit['alamat']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="<?= base_url('/rumahsakit') ?>" class="btn btn-secondary">
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
