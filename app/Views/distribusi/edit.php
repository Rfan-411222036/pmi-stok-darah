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
                    <form action="<?= base_url('/distribusi/update/' . $distribusi['id_distribusi']) ?>" method="post">
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
                                    <label for="no_kantong">No Kantong</label>
                                    <input type="text" class="form-control" id="no_kantong" name="no_kantong"
                                           value="<?= old('no_kantong', $distribusi['no_kantong']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="gol_dar">Golongan Darah</label>
                                    <input type="text" class="form-control" id="gol_dar" name="gol_dar"
                                           value="<?= old('gol_dar', $distribusi['gol_dar']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_darah">Jenis Darah</label>
                                    <input type="text" class="form-control" id="jenis_darah" name="jenis_darah"
                                           value="<?= old('jenis_darah', $distribusi['jenis_darah']) ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="id_rs">Rumah Sakit *</label>
                                    <select class="form-control" id="id_rs" name="id_rs" required>
                                        <option value="">Pilih Rumah Sakit</option>
                                        <?php foreach ($rumah_sakit as $rs): ?>
                                            <option value="<?= $rs['id_rs'] ?>" <?= old('id_rs', $distribusi['id_rs']) == $rs['id_rs'] ? 'selected' : '' ?> >
                                                <?= $rs['nama_rs'] ?> (<?= $rs['jenis_rs'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_distribusi">Tanggal Distribusi *</label>
                                    <input type="datetime-local" class="form-control" id="tanggal_distribusi" name="tanggal_distribusi"
                                           value="<?= old('tanggal_distribusi', date('Y-m-d\TH:i', strtotime($distribusi['tanggal_distribusi']))) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="penerima">Penerima *</label>
                                    <input type="text" class="form-control" id="penerima" name="penerima"
                                           value="<?= old('penerima', $distribusi['penerima']) ?>"
                                           placeholder="Masukkan nama penerima" required>
                                </div>

                                <div class="form-group">
                                    <label for="keperluan">Keperluan</label>
                                    <textarea class="form-control" id="keperluan" name="keperluan" rows="3"
                                              placeholder="Masukkan keperluan distribusi"><?= old('keperluan', $distribusi['keperluan']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="no_permintaan">No Permintaan</label>
                                    <input type="text" class="form-control" id="no_permintaan" name="no_permintaan"
                                           value="<?= old('no_permintaan', $distribusi['no_permintaan']) ?>"
                                           placeholder="Masukkan nomor permintaan">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
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
