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
                        <li class="breadcrumb-item"><a href="<?= base_url('/distribusi') ?>">Distribusi</a></li>
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
                    <form action="<?= base_url('/distribusi/store') ?>" method="post">
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
                                    <label for="id_bag">Pilih Stok Darah *</label>
                                    <select class="form-control" id="id_bag" name="id_bag" required>
                                        <option value="">Pilih Stok Darah</option>
                                        <?php foreach ($stok as $s): ?>
                                            <option value="<?= $s['id_bag'] ?>" <?= old('id_bag') == $s['id_bag'] ? 'selected' : '' ?>>
                                                <?= $s['no_kantong'] ?> - <?= $s['gol_dar'] ?><?= $s['rhesus'] ?> (<?= $s['jenis_darah'] ?>)
                                                - Expired: <?= date('d/m/Y', strtotime($s['tanggal_expired'])) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Hanya menampilkan stok yang tersedia dan belum expired</small>
                                </div>

                                <div class="form-group">
                                    <label for="id_rs">Rumah Sakit *</label>
                                    <select class="form-control" id="id_rs" name="id_rs" required>
                                        <option value="">Pilih Rumah Sakit</option>
                                        <?php foreach ($rumah_sakit as $rs): ?>
                                            <option value="<?= $rs['id_rs'] ?>" <?= old('id_rs', $prefill_id_rs ?? '') == $rs['id_rs'] ? 'selected' : '' ?>>
                                                <?= $rs['nama_rs'] ?> (<?= $rs['jenis_rs'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="penerima">Penerima *</label>
                                    <input type="text" class="form-control" id="penerima" name="penerima"
                                           value="<?= old('penerima', $prefill_penerima ?? '') ?>"
                                           placeholder="Masukkan nama penerima" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_distribusi">Tanggal Distribusi *</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_distribusi" name="tanggal_distribusi"
                                           value="<?= old('tanggal_distribusi', date('Y-m-d\TH:i')) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="keperluan">Keperluan</label>
                                    <textarea class="form-control" id="keperluan" name="keperluan"
                                              placeholder="Masukkan keperluan distribusi" rows="3"><?= old('keperluan') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="no_permintaan">No Permintaan</label>
                                    <input type="text" class="form-control" id="no_permintaan" name="no_permintaan"
                                           value="<?= old('no_permintaan', $prefill_no_permintaan ?? '') ?>"
                                           placeholder="Masukkan nomor permintaan">
                                </div>

                                <div class="alert alert-info">
                                    <h6><i class="icon fas fa-info"></i> Informasi</h6>
                                    <small>
                                        - Distribusi akan mengubah status stok menjadi "terdistribusi"<br>
                                        - Pastikan stok yang didistribusikan sesuai dengan permintaan<br>
                                        - Data distribusi akan tercatat secara permanen
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-truck"></i> Catat Distribusi
                            </button>
                            <a href="<?= base_url('/distribusi') ?>" class="btn btn-secondary">
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
