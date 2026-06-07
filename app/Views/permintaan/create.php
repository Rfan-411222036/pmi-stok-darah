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
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('/permintaan/store') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="id_rs">Rumah Sakit</label>
                            <select name="id_rs" id="id_rs" class="form-control" required>
                                <option value="">Pilih RS</option>
                                <?php foreach ($rumah_sakit as $r): ?>
                                    <option value="<?= $r['id_rs'] ?>"><?= $r['nama_rs'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="id_produsen">Tujuan BDRS</label>
                            <select name="id_produsen" id="id_produsen" class="form-control" required>
                                <option value="">Pilih BDRS</option>
                                <?php foreach ($produsen as $p): ?>
                                    <option value="<?= $p['id_produsen'] ?>"><?= $p['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="gol_dar">Golongan Darah (opsional)</label>
                            <input type="text" name="gol_dar" id="gol_dar" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary">Ajukan Permintaan</button>
                            <a href="<?= base_url('/permintaan') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('templates/footer') ?>
