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
                    <form action="<?= base_url('/distribusi/store') ?>" method="post" id="distribusiForm">
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
                            <!-- Step 1: Hospital + blood type selection -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-muted">Langkah 1 — Pilih Rumah Sakit & Jenis Darah</h5>

                                <div class="form-group">
                                    <label for="id_rs">Rumah Sakit *</label>
                                    <select class="form-control" id="id_rs" name="id_rs" required>
                                        <option value="">Pilih Rumah Sakit</option>
                                        <?php foreach ($rumah_sakit as $rs): ?>
                                            <option value="<?= $rs['id_rs'] ?>"
                                                    data-primary-bdrs="<?= $rs['id_primary_bdrs'] ?? '' ?>"
                                                    <?= old('id_rs') == $rs['id_rs'] ? 'selected' : '' ?>>
                                                <?= esc($rs['nama_rs']) ?> (<?= $rs['jenis_rs'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="gol_dar">Golongan Darah *</label>
                                            <select class="form-control" id="gol_dar" required>
                                                <option value="">Pilih</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="AB">AB</option>
                                                <option value="O">O</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="rhesus">Rhesus *</label>
                                            <select class="form-control" id="rhesus" required>
                                                <option value="">Pilih</option>
                                                <option value="+">Positif (+)</option>
                                                <option value="-">Negatif (-)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_darah">Jenis Darah *</label>
                                    <select class="form-control" id="jenis_darah" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="Whole">Whole Blood</option>
                                        <option value="PRC">Packed Red Cells</option>
                                        <option value="TC">Thrombocyte Concentrate</option>
                                        <option value="FFP">Fresh Frozen Plasma</option>
                                    </select>
                                </div>

                                <button type="button" class="btn btn-info btn-block mb-3" id="btnCekStok">
                                    <i class="fas fa-search"></i> Cek Ketersediaan Stok
                                </button>

                                <div class="alert alert-info">
                                    <i class="fas fa-random"></i> <strong>Failover Routing</strong><br>
                                    <small>Sistem akan otomatis mengalihkan ke BDRS lain jika primary BDRS kosong.</small>
                                </div>
                            </div>

                            <!-- Step 2: Result + form fields -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-muted">Langkah 2 — Konfirmasi Distribusi</h5>

                                <div id="stockLoading" style="display:none;" class="text-center p-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                    <p class="mt-2 text-muted">Memeriksa ketersediaan stok...</p>
                                </div>

                                <div id="stockEmpty" style="display:none;">
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <strong>Stok tidak tersedia.</strong> Tidak ada BDRS yang memiliki stok untuk
                                        golongan darah ini. Pertimbangkan replenishment dari Central Hub (PMI).
                                    </div>
                                </div>

                                <div id="stockResult" style="display:none;">
                                    <div id="failoverAlert" class="alert" style="display:none;"></div>

                                    <div class="form-group">
                                        <label for="id_bag">
                                            Pilih Kantong *
                                            <span id="sourceBdrsLabel" class="badge badge-secondary ml-1"></span>
                                        </label>
                                        <select class="form-control" id="id_bag" name="id_bag" required>
                                            <option value="">Pilih Kantong</option>
                                        </select>
                                        <small class="form-text text-muted">Diurutkan FEFO (First Expired First Out)</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="penerima">Penerima *</label>
                                        <input type="text" class="form-control" id="penerima" name="penerima"
                                               value="<?= old('penerima') ?>"
                                               placeholder="Nama penerima" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="tanggal_distribusi">Tanggal Distribusi *</label>
                                        <input type="datetime-local" class="form-control" id="tanggal_distribusi"
                                               name="tanggal_distribusi"
                                               value="<?= old('tanggal_distribusi', date('Y-m-d\TH:i')) ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="keperluan">Keperluan</label>
                                        <textarea class="form-control" id="keperluan" name="keperluan"
                                                  rows="2" placeholder="Keperluan distribusi"><?= old('keperluan') ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="no_permintaan">No Permintaan</label>
                                        <input type="text" class="form-control" id="no_permintaan" name="no_permintaan"
                                               value="<?= old('no_permintaan') ?>"
                                               placeholder="Nomor permintaan">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-truck"></i> Catat Distribusi
                                        </button>
                                        <a href="<?= base_url('/distribusi') ?>" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>

                                <div id="stockIdle" class="text-center p-4 text-muted">
                                    <i class="fas fa-tint fa-3x mb-3" style="opacity:0.2"></i>
                                    <p>Pilih rumah sakit dan jenis darah, lalu klik <strong>Cek Ketersediaan Stok</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('btnCekStok').addEventListener('click', function () {
    const id_rs       = document.getElementById('id_rs').value;
    const gol_dar     = document.getElementById('gol_dar').value;
    const rhesus      = document.getElementById('rhesus').value;
    const jenis_darah = document.getElementById('jenis_darah').value;

    if (!id_rs || !gol_dar || !rhesus || !jenis_darah) {
        alert('Lengkapi Rumah Sakit, Golongan Darah, Rhesus, dan Jenis Darah terlebih dahulu.');
        return;
    }

    document.getElementById('stockIdle').style.display   = 'none';
    document.getElementById('stockResult').style.display = 'none';
    document.getElementById('stockEmpty').style.display  = 'none';
    document.getElementById('stockLoading').style.display = 'block';

    const params = new URLSearchParams({ id_rs, gol_dar, rhesus, jenis_darah });

    fetch(`<?= base_url('/distribusi/checkAvailability') ?>?${params}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('stockLoading').style.display = 'none';

            if (data.error || data.count === 0) {
                document.getElementById('stockEmpty').style.display = 'block';
                return;
            }

            // Populate bag dropdown
            const bagSelect = document.getElementById('id_bag');
            bagSelect.innerHTML = '<option value="">Pilih Kantong</option>';
            data.bags.forEach(bag => {
                const opt = document.createElement('option');
                opt.value = bag.id_bag;
                opt.textContent = `${bag.no_kantong} — ${bag.gol_dar}${bag.rhesus} ${bag.jenis_darah} — Exp: ${bag.tanggal_expired}`;
                bagSelect.appendChild(opt);
            });

            // Failover notice
            const alertEl = document.getElementById('failoverAlert');
            if (data.failover) {
                const primaryName = data.primary_bdrs ? data.primary_bdrs.nama : 'tidak diatur';
                alertEl.className = 'alert alert-warning';
                alertEl.innerHTML = `<i class="fas fa-random"></i> <strong>Failover aktif.</strong>
                    Primary BDRS <em>${primaryName}</em> tidak memiliki stok.
                    Permintaan dialihkan ke <strong>${data.source_bdrs.nama}</strong>.`;
                alertEl.style.display = 'block';
            } else {
                alertEl.style.display = 'none';
            }

            // Source label
            const sourceLabel = document.getElementById('sourceBdrsLabel');
            sourceLabel.textContent = data.source_bdrs ? 'dari: ' + data.source_bdrs.nama : '';

            document.getElementById('stockResult').style.display = 'block';
        })
        .catch(() => {
            document.getElementById('stockLoading').style.display = 'none';
            alert('Gagal memeriksa stok. Silakan coba lagi.');
        });
});
</script>

<?= $this->include('templates/footer') ?>
