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
                            <?php $role = session()->get('role'); $shouldDisableRs = ($role === 'rs') || (isset($selected_rs) && $selected_rs); ?>
                            <select name="id_rs" id="id_rs" class="form-control" <?= $shouldDisableRs ? 'disabled' : '' ?> required>
                                <option value="">Pilih RS</option>
                                <?php foreach ($rumah_sakit as $r): ?>
                                    <?php $isSelected = (old('id_rs') !== null) ? (old('id_rs') == $r['id_rs']) : (isset($selected_rs) && $selected_rs == $r['id_rs']); ?>
                                    <option value="<?= $r['id_rs'] ?>" <?= $isSelected ? 'selected' : '' ?>><?= $r['nama_rs'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($shouldDisableRs): ?>
                                <?php $hiddenRsVal = old('id_rs') ?: ($selected_rs ?? session()->get('id_rs')); ?>
                                <input type="hidden" name="id_rs" value="<?= $hiddenRsVal ?>">
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="id_produsen">Tujuan BDRS</label>
                            <select name="id_produsen" id="id_produsen" class="form-control" required>
                                <option value="">Pilih BDRS</option>
                                <?php foreach ($produsen as $p): ?>
                                    <option value="<?= $p['id_produsen'] ?>" <?= old('id_produsen') == $p['id_produsen'] ? 'selected' : '' ?>><?= $p['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="<?= old('jumlah') ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="gol_dar">Golongan Darah (opsional)</label>
                                <select name="gol_dar" id="gol_dar" class="form-control">
                                    <option value="">- Pilih Golongan -</option>
                                    <?php if(!empty($golongan_list)): ?>
                                        <?php foreach($golongan_list as $g): ?>
                                            <option value="<?= esc($g) ?>" <?= old('gol_dar') == $g ? 'selected' : '' ?>><?= esc($g) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="jenis">Jenis Darah (opsional)</label>
                                <select name="jenis" id="jenis" class="form-control">
                                    <option value="">- Pilih Jenis -</option>
                                    <?php if(!empty($jenis_list)): ?>
                                        <?php foreach($jenis_list as $j): ?>
                                            <option value="<?= esc($j) ?>" <?= old('jenis') == $j ? 'selected' : '' ?>><?= esc($j) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="diagnosa">Diagnosa</label>
                            <select name="diagnosa" id="diagnosa" class="form-control" required>
                                <option value="">- Pilih Diagnosa -</option>
                                <option value="CKD ON HD" <?= old('diagnosa') === 'CKD ON HD' ? 'selected' : '' ?>>CKD ON HD</option>
                                <option value="ANEMIA" <?= old('diagnosa') === 'ANEMIA' ? 'selected' : '' ?>>ANEMIA</option>
                                <option value="POST SC" <?= old('diagnosa') === 'POST SC' ? 'selected' : '' ?>>POST SC</option>
                                <option value="DBD" <?= old('diagnosa') === 'DBD' ? 'selected' : '' ?>>DBD</option>
                                <option value="THALASEMIA" <?= old('diagnosa') === 'THALASEMIA' ? 'selected' : '' ?>>THALASEMIA</option>
                                <option value="Lain-lain" <?= old('diagnosa') === 'Lain-lain' ? 'selected' : '' ?>>Lain-lain</option>
                            </select>
                        </div>

                        <div class="form-group" id="diagnosa_lain_group" style="display:none;">
                            <label for="diagnosa_lain">Diagnosa Lain-lain</label>
                            <input type="text" name="diagnosa_lain" id="diagnosa_lain" class="form-control" placeholder="Masukkan diagnosa lain-lain" value="<?= old('diagnosa_lain') ?>">
                        </div>

                        <div class="form-group">
                            <label for="priority_cito">Priority Cito</label>
                            <select name="priority_cito" id="priority_cito" class="form-control" required>
                                <option value="">Pilih Prioritas</option>
                                <option value="high" <?= old('priority_cito') === 'high' ? 'selected' : '' ?>>High</option>
                                <option value="medium" <?= old('priority_cito') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="low" <?= old('priority_cito') === 'low' ? 'selected' : '' ?>>Low</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nama_penerima">Nama Penerima Pasien</label>
                            <input type="text" name="nama_penerima" id="nama_penerima" class="form-control" placeholder="Masukkan nama penerima pasien" value="<?= old('nama_penerima') ?>" required>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var diagnosaSelect = document.getElementById('diagnosa');
        var diagnosaLainGroup = document.getElementById('diagnosa_lain_group');
        var diagnosaLainInput = document.getElementById('diagnosa_lain');

        function toggleDiagnosaLain() {
            if (diagnosaSelect.value === 'Lain-lain') {
                diagnosaLainGroup.style.display = 'block';
                diagnosaLainInput.required = true;
            } else {
                diagnosaLainGroup.style.display = 'none';
                diagnosaLainInput.required = false;
                diagnosaLainInput.value = '';
            }
        }

        diagnosaSelect.addEventListener('change', toggleDiagnosaLain);
        toggleDiagnosaLain();
    });
</script>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var produsenStockGolongan = <?= json_encode($produsen_stock_golongan) ?>;
        var produsenSelect = document.getElementById('id_produsen');
        var golSelect = document.getElementById('gol_dar');
        var currentGol = <?= json_encode(old('gol_dar')) ?>;

        function buildGolonganOptions(values) {
            golSelect.innerHTML = '';

            if (!values || values.length === 0) {
                var noOption = document.createElement('option');
                noOption.value = '';
                noOption.textContent = 'Tidak ada golongan darah tersedia untuk BDRS ini';
                golSelect.appendChild(noOption);
                golSelect.disabled = true;
                return;
            }

            var defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '- Pilih Golongan -';
            golSelect.appendChild(defaultOption);

            values.forEach(function (gol) {
                var option = document.createElement('option');
                option.value = gol;
                option.textContent = gol;
                if (gol === currentGol) {
                    option.selected = true;
                }
                golSelect.appendChild(option);
            });
            golSelect.disabled = false;
        }

        function updateGolonganList() {
            var produsenId = produsenSelect.value;
            if (!produsenId) {
                golSelect.innerHTML = '<option value="">Pilih BDRS dulu</option>';
                golSelect.disabled = true;
                return;
            }

            var available = produsenStockGolongan[produsenId] || [];
            buildGolonganOptions(available);
        }

        produsenSelect.addEventListener('change', function () {
            currentGol = null;
            updateGolonganList();
        });

        updateGolonganList();
    });
</script>

<?= $this->include('templates/footer') ?>

