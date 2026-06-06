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

            <?php if ($current_role === 'admin'): ?>
            <!-- Admin: Monthly Distribution Bar Chart -->
            <div class="row mb-3" id="monthlyChartRow">
                <div class="col-12">
                    <div class="card card-outline card-danger pmi-card">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title">Distribusi Darah Bulanan (12 Bulan Terakhir)</h3>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-hand-pointer mr-1"></i>
                                    Klik bar bulan untuk melihat rincian per BDRS
                                </p>
                            </div>
                            <span class="badge badge-pill badge-danger">Admin View</span>
                        </div>
                        <div class="card-body">
                            <div style="min-height:320px; position:relative;">
                                <canvas id="monthlyDistChart" style="height:320px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin: Drill-down per BDRS (hidden until bar clicked) -->
            <div class="row mb-3" id="drilldownRow" style="display:none;">
                <div class="col-12">
                    <div class="card card-outline" style="border-color:#4682b9;">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title" id="drilldownTitle">Detail Distribusi per BDRS</h3>
                                <p class="text-muted mb-0">Rincian jumlah distribusi pada bulan yang dipilih</p>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" id="drilldownBack">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="drilldownEmpty" class="text-center text-muted py-4" style="display:none;">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>Tidak ada distribusi pada bulan ini.</p>
                            </div>
                            <div id="drilldownChartWrap" style="min-height:260px; position:relative;">
                                <canvas id="drilldownChart" style="height:260px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Non-admin: Stock per BDRS line chart -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-danger pmi-card">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title">Grafik Stok Darah BDRS Saya</h3>
                                <p class="text-muted mb-0">Menampilkan total stok darah di BDRS Anda sendiri.</p>
                            </div>
                            <span class="badge badge-pill badge-danger">BDRS View</span>
                        </div>
                        <div class="card-body">
                            <div style="min-height:360px; position:relative;">
                                <canvas id="dashboardStokDarahChart" style="height:360px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                                <p><?= $stok['nama'] ?></p>
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
                                            <a href="<?= base_url('/distribusi') ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
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

<?php if ($current_role === 'admin'): ?>

    /* ── Monthly distribution bar chart ── */
    var monthlyLabels = <?= json_encode($monthly_labels) ?>;
    var monthlyData   = <?= json_encode($monthly_totals) ?>;
    var monthlyMeta   = <?= json_encode($monthly_meta) ?>;
    var drillInstance = null;

    var monthlyCtx = document.getElementById('monthlyDistChart').getContext('2d');
    var monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Total Distribusi',
                data: monthlyData,
                backgroundColor: 'rgba(185, 70, 70, 0.72)',
                borderColor:     'rgba(185, 70, 70, 1)',
                borderWidth: 1,
                borderRadius: 4,
                hoverBackgroundColor: 'rgba(185, 70, 70, 0.95)',
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            cursor: 'pointer',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return '  ' + ctx.parsed.y.toLocaleString('id-ID') + ' distribusi';
                        },
                        afterLabel: function () {
                            return '  ↗ Klik untuk detail per BDRS';
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#495057' } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (v) { return v.toLocaleString('id-ID'); },
                        color: '#495057'
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            },
            onClick: function (event, elements) {
                if (!elements.length) return;
                var idx  = elements[0].index;
                var meta = monthlyMeta[idx];
                var lbl  = monthlyLabels[idx];
                loadDrilldown(meta.year, meta.month, lbl);
            }
        }
    });

    /* pointer cursor on hover */
    document.getElementById('monthlyDistChart').style.cursor = 'pointer';

    function loadDrilldown(year, month, label) {
        document.getElementById('drilldownTitle').textContent = 'Distribusi per BDRS — ' + label;
        document.getElementById('drilldownRow').style.display = '';
        document.getElementById('drilldownEmpty').style.display = 'none';
        document.getElementById('drilldownChartWrap').style.display = '';

        var url = '<?= base_url('/dashboard/chart/drilldown') ?>/' + year + '/' + month;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                if (!json.labels.length) {
                    document.getElementById('drilldownChartWrap').style.display = 'none';
                    document.getElementById('drilldownEmpty').style.display = '';
                    return;
                }
                renderDrilldown(json);
                document.getElementById('drilldownRow').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
    }

    function renderDrilldown(json) {
        if (drillInstance) drillInstance.destroy();
        var ctx = document.getElementById('drilldownChart').getContext('2d');
        drillInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: json.labels,
                datasets: [{
                    label: 'Distribusi',
                    data: json.data,
                    backgroundColor: 'rgba(70, 130, 185, 0.72)',
                    borderColor:     'rgba(70, 130, 185, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return '  ' + ctx.parsed.x.toLocaleString('id-ID') + ' distribusi';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { callback: function (v) { return v.toLocaleString('id-ID'); }, color: '#495057' },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: { grid: { display: false }, ticks: { color: '#495057' } }
                }
            }
        });
    }

    document.getElementById('drilldownBack').addEventListener('click', function () {
        document.getElementById('drilldownRow').style.display = 'none';
        if (drillInstance) { drillInstance.destroy(); drillInstance = null; }
    });

<?php else: ?>

    /* ── Non-admin: stock per BDRS line chart ── */
    var ctx = document.getElementById('dashboardStokDarahChart');
    if (!ctx) return;

    var labels    = <?= json_encode($chart_labels) ?>;
    var chartData = <?= json_encode($chart_data) ?>;

    var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 360);
    gradient.addColorStop(0, 'rgba(220, 53, 69, 0.35)');
    gradient.addColorStop(1, 'rgba(220, 53, 69, 0.05)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
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
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: { labels: { boxWidth: 12, padding: 20 } },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#495057' } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (v) { return v.toLocaleString('id-ID'); },
                        color: '#495057'
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });

<?php endif; ?>

});
</script>

<?= $this->include('templates/footer') ?>
