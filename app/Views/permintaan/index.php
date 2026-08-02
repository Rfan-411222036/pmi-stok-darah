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
                    <div class="float-sm-right">
                        <?php $role = session()->get('role'); ?>
                        <?php if ($role === 'rs' || $role === 'admin'): ?>
                            <a href="<?= base_url('/permintaan/create') ?>" class="btn btn-primary btn-sm">Buat Permintaan</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning"><?= session()->getFlashdata('warning') ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('/permintaan') ?>" method="get" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Cari</label>
                                    <input type="text" name="search" id="search" class="form-control" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari RS, BDRS, penerima, keterangan...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="from">Dari</label>
                                    <input type="date" name="from" id="from" class="form-control" value="<?= esc($filters['from'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="to">Sampai</label>
                                    <input type="date" name="to" id="to" class="form-control" value="<?= esc($filters['to'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="keperluan">Keperluan</label>
                                    <select name="keperluan" id="keperluan" class="form-control">
                                        <option value="">Semua</option>
                                        <?php foreach ($keteranganOptions as $option): ?>
                                            <?php $value = $option['keterangan'] ?? $option; ?>
                                            <option value="<?= esc($value) ?>" <?= isset($filters['keperluan']) && $filters['keperluan'] === $value ? 'selected' : '' ?>><?= esc($value) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gol_dar">Golongan</label>
                                    <input type="text" name="gol_dar" id="gol_dar" class="form-control" value="<?= esc($filters['gol_dar'] ?? '') ?>" placeholder="A, B, AB, O">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jenis">Jenis</label>
                                    <input type="text" name="jenis" id="jenis" class="form-control" value="<?= esc($filters['jenis'] ?? '') ?>" placeholder="Contoh: DARAH">
                                </div>
                            </div>
                            <div class="col-md-9 d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-primary mr-2">Terapkan Filter</button>
                                <button type="button" class="btn btn-info mr-2 preview-permintaan-pdf" data-toggle="modal" data-target="#previewPdfModal">Preview PDF</button>
                                <a href="<?= base_url('/permintaan/download?' . http_build_query($filters)) ?>" class="btn btn-success">Download PDF</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>RS</th>
                                    <th>BDRS</th>
                                    <th>Jumlah</th>
                                    <th>Gol</th>
                                    <th>No Kantong</th>
                                    <th>Priority Cito</th>
                                    <th>Diagnosa</th>
                                    <th>Nama Penerima</th>
                                    <th>Keterangan Setujui</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list)): $no = 1; ?>
                                    <?php foreach ($list as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['nama_rs'] ?? $row['id_rs'] ?></td>
                                        <td><?= $row['nama_produsen'] ?? $row['id_produsen'] ?></td>
                                        <td><?= $row['jumlah'] ?></td>
                                        <td><?= $row['gol_dar'] ?></td>
                                        <td><?= esc($row['no_kantong'] ?? '-') ?></td>
                                        <td>
                                            <?php
                                                $priority = strtolower($row['priority_cito'] ?? 'medium');
                                                $priorityClass = [
                                                    'high' => 'badge-danger',
                                                    'medium' => 'badge-warning',
                                                    'low' => 'badge-success',
                                                ];
                                                $priorityLabel = [
                                                    'high' => 'High',
                                                    'medium' => 'Medium',
                                                    'low' => 'Low',
                                                ];
                                            ?>
                                            <span class="badge <?= $priorityClass[$priority] ?? 'badge-secondary' ?>"><?= $priorityLabel[$priority] ?? ucfirst($priority) ?></span>
                                        </td>
                                        <td><?= esc($row['keterangan'] ?? '-') ?></td>
                                        <td><?= esc($row['nama_penerima'] ?? '-') ?></td>
                                        <td><?= esc($row['approval_note'] ?? '-') ?></td>
                                        <td><?= isset($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-' ?></td>
                                        <td>
                                            <?php if ($row['status'] === 'approved'): ?>
                                                <span class="badge badge-success">Disetujui</span>
                                            <?php elseif ($row['status'] === 'completed'): ?>
                                                <span class="badge badge-primary">Selesai</span>
                                            <?php elseif ($row['status'] === 'rejected'): ?>
                                                <span class="badge badge-danger">Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Menunggu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (session()->get('role') === 'bdrs' && $row['status'] === 'pending'): ?>
                                                <a href="<?= base_url('/permintaan/approve/'.$row['id_permintaan']) ?>" class="btn btn-sm btn-success">Setujui</a>
                                                <a href="<?= base_url('/permintaan/reject/'.$row['id_permintaan']) ?>" class="btn btn-sm btn-danger">Tolak</a>
                                            <?php elseif (session()->get('role') === 'rs' && $row['status'] === 'approved'): ?>
                                                <a href="<?= base_url('/permintaan/complete/'.$row['id_permintaan']) ?>" class="btn btn-sm btn-primary" onclick="return confirm('Yakin ingin menyelesaikan permintaan ini?')">Selesaikan</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="13" class="text-center text-muted">Tidak ada permintaan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="previewPdfModal" tabindex="-1" role="dialog" aria-labelledby="previewPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewPdfModalLabel">Preview Laporan Permintaan PDF</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body bg-light">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>LAPORAN PERMINTAAN DARAH</strong>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="text-muted">Tanggal: <?= date('d F Y') ?></span>
                            </div>
                        </div>
                        <div class="row small text-muted">
                            <div class="col-md-3">Periode: <span id="previewPeriod">-</span></div>
                            <div class="col-md-3">Keperluan: <span id="previewKeperluan">-</span></div>
                            <div class="col-md-3">Golongan: <span id="previewGol">-</span></div>
                            <div class="col-md-3">Jenis: <span id="previewJenis">-</span></div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>RS</th>
                                <th>BDRS</th>
                                <th>Jumlah</th>
                                <th>Gol</th>
                                <th>No Kantong</th>
                                <th>Priority Cito</th>
                                <th>Diagnosa</th>
                                <th>Nama Penerima</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="previewPermintaanBody">
                            <tr>
                                <td colspan="10" class="text-center text-muted">Klik preview untuk melihat data laporan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a href="<?= base_url('/permintaan/download?' . http_build_query($filters)) ?>" class="btn btn-success">Download PDF</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var previewButton = document.querySelector('.preview-permintaan-pdf');
        var previewModal = document.getElementById('previewPdfModal');
        var previewBody = document.getElementById('previewPermintaanBody');
        var previewPeriod = document.getElementById('previewPeriod');
        var previewKeperluan = document.getElementById('previewKeperluan');
        var previewGol = document.getElementById('previewGol');
        var previewJenis = document.getElementById('previewJenis');

        if (previewButton && previewModal) {
            previewButton.addEventListener('click', function () {
                var form = previewButton.closest('form');
                if (!form) {
                    return;
                }

                var formData = new URLSearchParams(new FormData(form));
                previewPeriod.textContent = (formData.get('from') || '-') + ' s/d ' + (formData.get('to') || '-');
                previewKeperluan.textContent = formData.get('keperluan') || '-';
                previewGol.textContent = formData.get('gol_dar') || '-';
                previewJenis.textContent = formData.get('jenis') || '-';

                fetch('<?= base_url('/permintaan/preview') ?>?' + formData.toString())
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        if (!payload || !payload.success || !payload.rows || payload.rows.length === 0) {
                            previewBody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">Tidak ada data untuk preview.</td></tr>';
                            return;
                        }

                        previewBody.innerHTML = payload.rows.map(function (row, index) {
                            return '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + (row.nama_rs || '-') + '</td>' +
                                '<td>' + (row.nama_produsen || '-') + '</td>' +
                                '<td>' + (row.jumlah || '-') + '</td>' +
                                '<td>' + (row.gol_dar || '-') + '</td>' +
                                '<td>' + (row.no_kantong || '-') + '</td>' +
                                '<td>' + (row.priority_cito || '-') + '</td>' +
                                '<td>' + (row.keterangan || '-') + '</td>' +
                                '<td>' + (row.nama_penerima || '-') + '</td>' +
                                '<td>' + (row.status || '-') + '</td>' +
                                '</tr>';
                        }).join('');
                    });
            });
        }
    });
</script>

<?= $this->include('templates/footer') ?>
