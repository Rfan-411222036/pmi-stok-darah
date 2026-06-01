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
                    <form action="<?= base_url('/pemusnahan/store') ?>" method="post">
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
                                    <label for="id_bag">Pilih Stok Darah untuk Dimusnahkan *</label>
                                    <select class="form-control" id="id_bag" name="id_bag" required>
                                        <option value="">Pilih Stok Darah</option>
                                        <?php if (!empty($stok_expired)): ?>
                                            <optgroup label="Stok Expired">
                                                <?php foreach ($stok_expired as $stok): ?>
                                                    <option value="<?= $stok['id_bag'] ?>" <?= old('id_bag') == $stok['id_bag'] ? 'selected' : '' ?>>
                                                        <?= $stok['no_kantong'] ?> - <?= $stok['gol_dar'] ?><?= $stok['rhesus'] ?> (<?= $stok['jenis_darah'] ?>)
                                                        - Expired: <?= date('d/m/Y', strtotime($stok['tanggal_expired'])) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endif; ?>
                                    </select>
                                    <small class="form-text text-muted">Hanya menampilkan stok yang tersedia dan expired/rusak</small>
                                </div>

                                <div class="form-group">
                                    <label for="alasan">Alasan Pemusnahan *</label>
                                    <select class="form-control" id="alasan" name="alasan" required>
                                        <option value="">Pilih Alasan</option>
                                        <option value="expired" <?= old('alasan') == 'expired' ? 'selected' : '' ?>>Expired</option>
                                        <option value="rusak" <?= old('alasan') == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                                        <option value="lainnya" <?= old('alasan') == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_pemusnahan">Tanggal Pemusnahan *</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_pemusnahan" name="tanggal_pemusnahan"
                                           value="<?= old('tanggal_pemusnahan', date('Y-m-d\TH:i')) ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="petugas">Petugas *</label>
                                    <input type="text" class="form-control" id="petugas" name="petugas"
                                           value="<?= old('petugas', session()->get('nama')) ?>"
                                           placeholder="Masukkan nama petugas" required>
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan"
                                              placeholder="Masukkan keterangan pemusnahan" rows="4"><?= old('keterangan') ?></textarea>
                                    <small class="form-text text-muted">Jelaskan alasan detail pemusnahan jika diperlukan</small>
                                </div>

                                <!-- Info Stok Terpilih -->
                                <div class="alert alert-info">
                                    <h6><i class="icon fas fa-info"></i> Informasi</h6>
                                    <small>
                                        - Pemusnahan akan mengubah status stok menjadi "musnah"<br>
                                        - Data pemusnahan akan tercatat secara permanen<br>
                                        - Pastikan stok yang dimusnahkan sudah sesuai
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-trash"></i> Catat Pemusnahan
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const id_bagSelect = document.getElementById('id_bag');
    const alasanSelect = document.getElementById('alasan');

    // Auto-fill petugas dengan nama user yang login
    const petugasField = document.getElementById('petugas');
    if (!petugasField.value) {
        petugasField.value = '<?= session()->get("nama") ?>';
    }

    // Update opsi berdasarkan alasan
    alasanSelect.addEventListener('change', function() {
        const alasan = this.value;
        // Jika alasan bukan expired, bisa tambahkan logika untuk menampilkan stok rusak
    });
});
</script>

<?= $this->include('templates/footer') ?>
