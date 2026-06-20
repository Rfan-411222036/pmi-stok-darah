<?= $this->include('templates/header') ?>
<?= $this->include('templates/navbar') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <style>
        .pmi-card {
            background: #ffffff;
            border: 1px solid #dc3545;
            color: #111111;
        }
        .pmi-card .card-header {
            background: #ffffff;
            border-bottom: 1px solid #dc3545;
            color: #111111;
        }
        .pmi-card .card-title,
        .pmi-card .text-muted {
            color: #111111 !important;
        }
        .pmi-box {
            background: #dc3545 !important;
            color: #ffffff !important;
            border: none !important;
        }
        .pmi-box .icon {
            color: rgba(255, 255, 255, 0.95);
        }
        .small-box.pmi-bdrs-card {
            background: #ffffff !important;
            color: #111111 !important;
            border: 1px solid #dc3545;
        }
        .small-box.pmi-bdrs-card .icon {
            color: #dc3545;
        }
        .small-box.pmi-bdrs-card .icon i {
            color: #dc3545;
        }
        .small-box.pmi-bdrs-card .small-box-footer {
            color: #dc3545 !important;
        }
    </style>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $page_title ?></h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right text-sm">
                        <span class="badge badge-danger"><?= date('d F Y') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Warning untuk stok mendekati expired -->
            <?php if ($stok_mendekati_expired > 0): ?>
            <div class="alert alert-warning">
                <i class="icon fas fa-exclamation-triangle"></i>
                Ada <strong><?= $stok_mendekati_expired ?></strong> stok darah yang mendekati tanggal expired!
            </div>
            <?php endif; ?>

            <!-- Warning untuk stok expired -->
            <?php if ($stok_expired > 0): ?>
            <div class="alert alert-danger">
                <i class="icon fas fa-exclamation-circle"></i>
                Ada <strong><?= $stok_expired ?></strong> stok darah yang sudah expired dan perlu pemusnahan!
            </div>
            <?php endif; ?>

            <?php if ($current_role === 'admin'): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Laporan & Pemeriksaan Stok</h3>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('/dashboard/laporan/download') ?>" target="_blank" class="form-inline mb-2">
                        <div class="form-group mr-2">
                            <select name="id_produsen" class="form-control">
                                <option value="">Semua BDRS</option>
                                <?php foreach ($produsen_list as $p): ?>
                                    <option value="<?= $p['id_produsen'] ?>"><?= esc($p['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <select name="gol_dar" class="form-control">
                                <option value="">Semua Golongan</option>
                                <?php foreach ($golongan_list as $g): ?>
                                    <option value="<?= esc($g) ?>"><?= esc($g) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <select name="jenis" class="form-control">
                                <option value="">Semua Jenis</option>
                                <?php foreach ($jenis_list as $j): ?>
                                    <option value="<?= esc($j) ?>"><?= esc($j) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="btn btn-danger mr-2" type="submit"><i class="fas fa-file-pdf"></i> Download Stok (PDF)</button>
                    </form>

                    <form method="get" action="<?= base_url('/dashboard/laporan/distribusi') ?>" target="_blank" class="form-inline mb-2">
                        <input type="date" name="from" class="form-control mr-2">
                        <input type="date" name="to" class="form-control mr-2">
                        <button class="btn btn-success" type="submit"><i class="fas fa-file-pdf"></i> Download Distribusi</button>
                    </form>

                    <form method="post" action="<?= base_url('/dashboard/check-low-stock') ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-warning" type="submit"><i class="fas fa-bell"></i> Periksa Stok Rendah Sekarang</button>
                    </form>
                </div>
            </div>
            <div class="card card-outline card-danger mb-3">
                <div class="card-header">
                    <h3 class="card-title">Notifikasi Stok Rendah</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($low_stock_notifications)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($low_stock_notifications as $notification): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <span><?= esc($notification['message']) ?></span>
                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-success mb-0">
                            Tidak ada notifikasi stok rendah saat ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box pmi-box">
                        <div class="inner">
                            <h3><?= number_format($total_stok, 0, ',', '.') ?></h3>
                            <p>Stok Darah Tersedia</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <a href="<?= base_url('/stok') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box pmi-box">
                        <div class="inner">
                            <h3><?= number_format($total_distribusi, 0, ',', '.') ?></h3>
                            <p>Total Distribusi</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <a href="<?= base_url('/distribusi') ?>" class="small-box-footer">
                            Hari ini: <?= $distribusi_hari_ini ?> | Bulan ini: <?= $distribusi_bulan_ini ?>
                        </a>
                    </div>
                </div>

                <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($total_produsen, 0, ',', '.') ?></h3>
                            <p>Data Produsen</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?= base_url('/produsen') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div> -->

                <!-- <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($total_rs, 0, ',', '.') ?></h3>
                            <p>Rumah Sakit</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <a href="<?= base_url('/rumahsakit') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div> -->
            </div>

            <!-- Second Row of Small Boxes -->
            <!-- <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?= number_format($total_users, 0, ',', '.') ?></h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <a href="<?= base_url('/users') ?>" class="small-box-footer">
                            Admin: <?= $total_admins ?> | Staff: <?= $total_staff ?>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3><?= number_format($total_pemusnahan, 0, ',', '.') ?></h3>
                            <p>Pemusnahan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trash"></i>
                        </div>
                        <a href="<?= base_url('/pemusnahan') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($stok_mendekati_expired, 0, ',', '.') ?></h3>
                            <p>Mendekati Expired</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="<?= base_url('/stok') ?>" class="small-box-footer">Perlu perhatian</a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($stok_expired, 0, ',', '.') ?></h3>
                            <p>Stok Expired</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-skull-crossbones"></i>
                        </div>
                        <a href="<?= base_url('/pemusnahan/create') ?>" class="small-box-footer">Segera musnahkan</a>
                    </div>
                </div>
            </div> -->

            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-danger pmi-card">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title"><?= $current_role === 'admin' ? 'Grafik Stok Darah di Setiap BDRS' : 'Grafik Stok Darah BDRS Saya' ?></h3>
                                <p class="text-muted mb-0"><?php if ($current_role === 'admin'): ?>Menampilkan total stok darah untuk setiap BDRS yang terdaftar.<?php else: ?>Menampilkan total stok darah di BDRS Anda sendiri.<?php endif; ?></p>
                            </div>
                            <span class="badge badge-pill badge-danger"><?= $current_role === 'admin' ? 'Admin View' : 'BDRS View' ?></span>
                        </div>
                        <div class="card-body">
                            <div class="chart-wrapper" style="min-height: 360px; position: relative;">
                                <canvas id="dashboardStokDarahChart" style="height: 360px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($monthly_distribusi) && $current_role === 'admin'): ?>
            <!-- <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-outline card-danger pmi-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Grafik Distribusi Bulanan (Klik bar untuk lihat per gudang)</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-wrapper" style="min-height: 300px; position: relative;">
                                <canvas id="monthlyDistribusiChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <?php endif; ?>

            <!-- Card Laporan PDF -->
            <!-- <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf"></i> Laporan Stok Darah
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Download laporan stok darah dalam format PDF dengan statistik lengkap.</p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('/dashboard/laporan/download') ?>" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf"></i> Laporan Distribusi
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Download laporan distribusi darah ke rumah sakit dalam format PDF.</p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('/dashboard/laporan/distribusi') ?>" class="btn btn-success btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf"></i> Laporan Pemusnahan
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Download laporan pemusnahan darah kadaluarsa dalam format PDF.</p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('/dashboard/laporan/pemusnahan') ?>" class="btn btn-warning btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf"></i> Laporan Retur
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Download laporan retur darah dari rumah sakit dalam format PDF.</p>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('/dashboard/laporan/retur') ?>" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- Stok Darah per BDRS -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-hospital"></i> <?= $current_role === 'admin' ? 'Stok Darah di Setiap BDRS' : 'Stok Darah BDRS Anda' ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($stok_per_bdrs)): ?>
                                <div class="row">
                                    <?php foreach ($stok_per_bdrs as $stok): ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                        <div class="small-box bg-primary">
                                            <div class="inner">
                                                <h3><?= number_format($stok['jumlah_stok'], 0, ',', '.') ?></h3>
                                                <p><?= $stok['nama_produsen'] ?? $stok['nama'] ?? 'Unknown' ?></p>
                                                <p class="mb-1">
                                                    <small>Masih layak pakai: <strong><?= number_format($stok['layak_pakai'], 0, ',', '.') ?></strong></small>
                                                </p>
                                                <p class="mb-0">
                                                    <small>Sudah expired: <strong><?= number_format($stok['sudah_expired'], 0, ',', '.') ?></strong></small>
                                                </p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-tint"></i>
                                            </div>
                                            <a href="<?= base_url('/stok?id_produsen=' . $stok['id_produsen']) ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">Tidak ada data stok per BDRS</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Stok by Golongan Darah -->
                <!-- <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stok Darah per Golongan</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($stok_by_golongan)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Golongan Darah</th>
                                                <th>Jumlah Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stok_by_golongan as $stok): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-danger"><?= $stok['gol_dar'] ?></span>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($stok['total'], 0, ',', '.') ?></strong> kantong
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">Tidak ada data stok</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div> -->

                <!-- Stok by Jenis Darah -->
                <!-- <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stok Darah per Jenis</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($stok_by_jenis)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Jenis Darah</th>
                                                <th>Jumlah Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stok_by_jenis as $stok): ?>
                                            <tr>
                                                <td><?= $stok['jenis_darah'] ?></td>
                                                <td>
                                                    <strong><?= number_format($stok['total'], 0, ',', '.') ?></strong> kantong
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">Tidak ada data stok</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Distribusi Terbaru -->
            <div class="row">
                <!-- <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Distribusi Terbaru</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recent_distribusi)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No Kantong</th>
                                                <th>Golongan</th>
                                                <th>Rumah Sakit</th>
                                                <th>Penerima</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_distribusi as $dist): ?>
                                            <tr>
                                                <td><?= $dist['no_kantong'] ?></td>
                                                <td>
                                                    <span class="badge badge-danger"><?= $dist['gol_dar'] ?></span>
                                                </td>
                                                <td><?= $dist['nama_rs'] ?></td>
                                                <td><?= $dist['penerima'] ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($dist['tanggal_distribusi'])) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center p-3">Tidak ada data distribusi</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('dashboardStokDarahChart');
        if (!ctx) return;

        var labels = <?= json_encode($chart_labels) ?>;
        var chartData = <?= json_encode($chart_data) ?>;

        var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 360);
        gradient.addColorStop(0, 'rgba(220, 53, 69, 0.35)');
        gradient.addColorStop(1, 'rgba(220, 53, 69, 0.05)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Jumlah Stok Darah',
                        data: chartData,
                        borderColor: '#dc3545',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#dc3545',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#495057' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) { return value.toLocaleString('id-ID'); },
                            color: '#495057'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });

                // Monthly distribusi chart (admin) with click -> per-gudang
                <?php if (!empty($monthly_distribusi) && $current_role === 'admin'): ?>
                (function(){
                        var mCtx = document.getElementById('monthlyDistribusiChart');
                        if (!mCtx) return;

                        var mLabels = <?= json_encode($monthly_distribusi['labels']) ?>;
                        var mData = <?= json_encode($monthly_distribusi['data']) ?>;

                        var monthlyChart = new Chart(mCtx, {
                                type: 'bar',
                                data: {
                                        labels: mLabels,
                                        datasets: [{
                                                label: 'Jumlah Distribusi',
                                                data: mData,
                                                backgroundColor: '#dc3545',
                                                borderColor: '#b21f2b'
                                        }]
                                },
                                options: {
                                        maintainAspectRatio: false,
                                        onClick: function (evt, activeEls) {
                                                var points = this.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                                                if (!points.length) return;
                                                var idx = points[0].index; // month index 0..11
                                                var month = idx + 1;

                                                // fetch per-gudang data
                                                fetch('<?= base_url('/dashboard/distribusi-per-gudang') ?>?month=' + month)
                                                        .then(res => res.json())
                                                        .then(json => {
                                                                if (!json.success) return alert('Data tidak tersedia');
                                                                var labels = json.data.map(function(i){ return i.nama_produsen || 'Unknown'; });
                                                                var data = json.data.map(function(i){ return parseInt(i.jumlah) || 0; });

                                                                // show modal with chart
                                                                var modalId = 'perGudangModal';
                                                                var existing = document.getElementById(modalId);
                                                                if (existing) existing.remove();

                                                                var modalHtml = `
                                                                <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog">
                                                                    <div class="modal-dialog modal-lg" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Distribusi per Gudang - ${mLabels[idx]}</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <canvas id="perGudangChartCanvas" style="height:360px"></canvas>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>`;

                                                                document.body.insertAdjacentHTML('beforeend', modalHtml);
                                                                $('#'+modalId).modal('show');

                                                                setTimeout(function(){
                                                                        var pc = document.getElementById('perGudangChartCanvas');
                                                                        new Chart(pc, {
                                                                                type: 'bar',
                                                                                data: { labels: labels, datasets: [{ label: 'Jumlah Distribusi', data: data, backgroundColor: '#dc3545' }] },
                                                                                options: { maintainAspectRatio: false }
                                                                        });
                                                                }, 300);
                                                        })
                                                        .catch(err => { console.error(err); alert('Gagal mengambil data.'); });
                                        },
                                        plugins: { legend: { display: false } }
                                }
                        });
                })();
                <?php endif; ?>
    });
</script>

<?= $this->include('templates/footer') ?>
