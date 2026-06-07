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
                    <form action="<?= base_url('/return/store') ?>" method="post">
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
                                    <label for="id_distribusi">Pilih Distribusi untuk Direturn *</label>
                                    <select class="form-control" id="id_distribusi" name="id_distribusi" required>
                                        <option value="">Pilih Distribusi</option>
                                        <?php foreach ($distribusi as $d): ?>
                                            <option value="<?= $d['id_distribusi'] ?>" data-id_bag="<?= $d['id_bag'] ?>" data-id_rs="<?= $d['id_rs'] ?>" data-nama_produsen="<?= $d['nama_produsen'] ?>">
                                                <?= $d['no_kantong'] ?> - <?= $d['gol_dar'] ?> (<?= $d['jenis_darah'] ?>)
                                                - Sumber BDRS: <?= $d['nama_produsen'] ?>
                                                - Distribusi: <?= date('d/m/Y', strtotime($d['tanggal_distribusi'])) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Pilih distribusi yang akan diretur</small>
                                </div>

                                <div class="form-group">
                                    <label for="id_bag">ID Kantong Darah *</label>
                                    <input type="text" class="form-control" id="id_bag" name="id_bag" readonly required>
                                </div>

                                <div class="form-group">
                                    <label for="sumber_bdrs">Sumber BDRS</label>
                                    <input type="text" class="form-control" id="sumber_bdrs" name="sumber_bdrs" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="alasan_return">Alasan Return *</label>
                                    <select class="form-control" id="alasan_return" name="alasan_return" required>
                                        <option value="">Pilih Alasan Return</option>
                                        <option value="Tidak digunakan">Tidak digunakan</option>
                                        <option value="Permintaan dibatalkan">Permintaan dibatalkan</option>
                                        <option value="Pasien tidak membutuhkan">Pasien tidak membutuhkan</option>
                                        <option value="Kesalahan permintaan">Kesalahan permintaan</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_retur">Tanggal Return *</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_retur" name="tanggal_retur"
                                           value="<?= old('tanggal_retur', date('Y-m-d\TH:i')) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="kondisi_darah">Kondisi Darah saat Return *</label>
                                    <select class="form-control" id="kondisi_darah" name="kondisi_darah" required>
                                        <option value="baik" <?= old('kondisi_darah') == 'baik' ? 'selected' : '' ?>>Baik - Bisa digunakan kembali</option>
                                        <option value="rusak" <?= old('kondisi_darah') == 'rusak' ? 'selected' : '' ?>>Rusak - Perlu dimusnahkan</option>
                                    </select>
                                    <small class="form-text text-muted">Kondisi darah menentukan apakah akan kembali ke stok atau dimusnahkan</small>
                                </div>

                                <!-- Removed 'Ditangani Oleh' per BDRS->PMI return workflow -->

                                <div class="form-group">
                                    <label for="keterangan">Keterangan Tambahan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan"
                                              placeholder="Masukkan keterangan tambahan" rows="3"><?= old('keterangan') ?></textarea>
                                </div>

                                <!-- Info Distribusi -->
                                <div id="distribusi-info" class="alert alert-info" style="display: none;">
                                    <h6><i class="icon fas fa-info"></i> Informasi Distribusi</h6>
                                    <div id="info-content"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Return
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const distribusiSelect = document.getElementById('id_distribusi');
    const id_bagInput = document.getElementById('id_bag');
    const id_rsInput = document.getElementById('id_rs');
    const infoDiv = document.getElementById('distribusi-info');
    const infoContent = document.getElementById('info-content');

    distribusiSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value) {
            const id_bag = selectedOption.getAttribute('data-id_bag');
            const id_rs = selectedOption.getAttribute('data-id_rs');
            const namaProdusen = selectedOption.getAttribute('data-nama_produsen') || '';

            id_bagInput.value = id_bag;
            id_rsInput.value = id_rs;
            document.getElementById('sumber_bdrs').value = namaProdusen;

            // Tampilkan info distribusi (sumber BDRS)
            infoDiv.style.display = 'block';
            infoContent.innerHTML = `
                <strong>No Kantong:</strong> ${selectedOption.text.split(' - ')[0]}<br>
                <strong>Sumber BDRS:</strong> ${namaProdusen}<br>
                <strong>Tanggal Distribusi:</strong> ${selectedOption.text.split('Distribusi: ')[1]}
            `;
        } else {
            infoDiv.style.display = 'none';
            id_bagInput.value = '';
            id_rsInput.value = '';
        }
    });

    // Trigger change event jika ada value yang dipilih
    if (distribusiSelect.value) {
        distribusiSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<?= $this->include('templates/footer') ?>
