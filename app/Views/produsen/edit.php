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
                        <li class="breadcrumb-item"><a href="<?= base_url('/produsen') ?>">Bank Darah Rumah Sakit</a></li>
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
                    <form action="<?= base_url('/produsen/update/' . $produsen['id_produsen']) ?>" method="post">
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
                                     <label for="nama">Nama Bank Darah Rumah Sakit *</label>
                                     <input type="text" class="form-control" id="nama" name="nama"
                                         value="<?= old('nama', $produsen['nama']) ?>"
                                         placeholder="Masukkan nama Bank Darah Rumah Sakit" required>
                                </div>

                                <div class="form-group">
                                    <label for="jenis">Jenis Bank Darah Rumah Sakit *</label>
                                    <select class="form-control" id="jenis" name="jenis" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="umum" <?= old('jenis', $produsen['jenis']) == 'umum' ? 'selected' : '' ?>>Umum</option>
                                        <option value="pemerintah" <?= old('jenis', $produsen['jenis']) == 'pemerintah' ? 'selected' : '' ?>>Pemerintah</option>
                                        <option value="swasta" <?= old('jenis', $produsen['jenis']) == 'swasta' ? 'selected' : '' ?>>Swasta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_darah">Jenis Darah</label>
                                    <input type="text" class="form-control" id="jenis_darah" name="jenis_darah"
                                           value="<?= old('jenis_darah', $produsen['jenis_darah']) ?>"
                                           placeholder="Masukkan jenis darah (contoh: A+, B-, O+)">
                                </div>

                                <div class="form-group">
                                    <label for="no_kantong">No. Kantong Darah *</label>
                                    <input type="text" class="form-control" id="no_kantong" name="no_kantong"
                                           value="<?= old('no_kantong', $produsen['no_kantong']) ?>"
                                           placeholder="Masukkan nomor kantong darah" required>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status Darah *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="masih layak" <?= old('status', $produsen['status']) == 'masih layak' ? 'selected' : '' ?>>Masih Layak</option>
                                        <option value="expired" <?= old('status', $produsen['status']) == 'expired' ? 'selected' : '' ?>>Expired</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat"
                                              placeholder="Masukkan alamat lengkap" rows="3"><?= old('alamat', $produsen['alamat']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="min_threshold">Minimum Threshold (unit)</label>
                                    <input type="number" class="form-control" id="min_threshold" name="min_threshold"
                                           value="<?= old('min_threshold', $produsen['min_threshold'] ?? 30) ?>" min="1">
                                    <small class="form-text text-muted">Auto-request replenishment jika stok di bawah nilai ini.</small>
                                </div>

                                <div class="form-group">
                                    <label for="priority_order">Prioritas Failover</label>
                                    <input type="number" class="form-control" id="priority_order" name="priority_order"
                                           value="<?= old('priority_order', $produsen['priority_order'] ?? 0) ?>" min="0">
                                    <small class="form-text text-muted">Urutan saat failover routing (0 = prioritas tertinggi).</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_central_hub"
                                               name="is_central_hub" value="1"
                                               <?= old('is_central_hub', $produsen['is_central_hub'] ?? 0) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="is_central_hub">
                                            Tandai sebagai Central Hub (PMI Pusat / Tier 1)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
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
