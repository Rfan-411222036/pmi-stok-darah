<?= $this->include('templates/header') ?>
<?= $this->include('templates/navbar') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <style>
        @media print {
            @page {
                margin: 12mm;
            }

            body.printing-report * {
                visibility: hidden !important;
            }

            body.printing-report #printReportTarget,
            body.printing-report #printReportTarget * {
                visibility: visible !important;
            }

            body.printing-report #printReportTarget {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                border: 0;
            }

            body.printing-report #printReportTarget .report-actions,
            body.printing-report #printReportTarget .report-chart-block {
                display: none !important;
            }

            body.printing-report #printReportTarget .card-header {
                border: 0;
                padding-left: 0;
            }

            body.printing-report #printReportTarget table {
                width: 100%;
                border-collapse: collapse;
            }

            body.printing-report #printReportTarget th,
            body.printing-report #printReportTarget td {
                border: 1px solid #000 !important;
                color: #000 !important;
            }

            body.printing-report #printReportTarget .report-signature {
                color: #000 !important;
            }
        }

        .report-signature {
            width: 240px;
            min-height: 150px;
            margin: 32px 0 0 auto;
            text-align: center;
        }

        .report-signature-name {
            display: inline-block;
            min-width: 180px;
            margin-top: 76px;
            padding-top: 4px;
            border-top: 1px solid #000;
            font-weight: 600;
        }
    </style>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-8">
                    <h1><?= esc($page_title) ?></h1>
                </div>
                <div class="col-sm-4">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="get" action="<?= base_url('/laporan') ?>" class="form-inline">
                        <label for="from" class="mr-2">Periode</label>
                        <input id="from" type="date" name="from" class="form-control mr-2 mb-2" value="<?= esc($from) ?>" required>
                        <span class="mr-2 mb-2">s/d</span>
                        <input type="date" name="to" class="form-control mr-2 mb-2" value="<?= esc($to) ?>" required>
                        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-filter"></i> Tampilkan</button>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-danger report-card" id="diagnosisReport">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <?= $is_bdrs
                            ? 'Laporan Distribusi Darah Berdasarkan Diagnosa/Keperluan'
                            : 'Laporan Kebutuhan Darah Berdasarkan Diagnosa' ?>
                    </h3>
                    <div class="report-actions">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-print-report="diagnosisReport">
                            <i class="fas fa-print"></i> Print Preview
                        </button>
                        <a class="btn btn-success btn-sm" href="<?= base_url('/laporan/download?' . http_build_query(['type' => 'keperluan', 'from' => $from, 'to' => $to])) ?>">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-1">Nama Bank Darah: <?= esc($bank_name) ?></p>
                    <p><?= esc($period_label) ?></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr><th class="text-center">No</th><th>Diagnosa</th><th class="text-center">Permintaan</th><th class="text-center">Pemakaian</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($diagnosis_report['rows'] as $row): ?>
                                    <tr>
                                        <td><?= esc($row['number']) ?></td>
                                        <td><?= esc($row['label']) ?></td>
                                        <td class="text-center"><?= number_format($row['requests'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?= number_format($row['usage'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="2" class="text-right font-weight-bold">TOTAL</td>
                                    <td class="text-center font-weight-bold"><?= number_format($diagnosis_report['totals']['requests'], 0, ',', '.') ?></td>
                                    <td class="text-center font-weight-bold"><?= number_format($diagnosis_report['totals']['usage'], 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="report-signature">
                        <div>Bekasi, <?= esc($signature_date) ?></div>
                        <div>Petugas,</div>
                        <div class="report-signature-name"><?= esc($signatory_name) ?></div>
                    </div>
                    <div class="report-chart-block">
                        <h5 class="mt-4">Tren per Bulan</h5>
                        <p class="text-muted">
                            <?= $is_bdrs
                                ? 'Jumlah distribusi per diagnosa/keperluan berdasarkan bulan.'
                                : 'Jumlah permintaan dan pemakaian per diagnosa/keperluan berdasarkan bulan.' ?>
                        </p>
                        <div style="height: 350px;">
                        <canvas id="needChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary report-card" id="bloodGroupReport">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <?= $is_bdrs
                            ? 'Laporan Distribusi Darah Berdasarkan Golongan Darah'
                            : 'Laporan Kebutuhan Darah Berdasarkan Golongan Darah' ?>
                    </h3>
                    <div class="report-actions">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-print-report="bloodGroupReport">
                            <i class="fas fa-print"></i> Print Preview
                        </button>
                        <a class="btn btn-success btn-sm" href="<?= base_url('/laporan/download?' . http_build_query(['type' => 'golongan', 'from' => $from, 'to' => $to])) ?>">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-1">Nama Bank Darah: <?= esc($bank_name) ?></p>
                    <p><?= esc($period_label) ?></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr><th class="text-center">No</th><th>Diagnosa</th><th class="text-center">Permintaan</th><th class="text-center">Pemakaian</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blood_group_report['rows'] as $row): ?>
                                    <tr>
                                        <td><?= esc($row['number']) ?></td>
                                        <td><?= esc($row['label']) ?></td>
                                        <td class="text-center"><?= number_format($row['requests'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?= number_format($row['usage'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="2" class="text-right font-weight-bold">TOTAL</td>
                                    <td class="text-center font-weight-bold"><?= number_format($blood_group_report['totals']['requests'], 0, ',', '.') ?></td>
                                    <td class="text-center font-weight-bold"><?= number_format($blood_group_report['totals']['usage'], 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="report-signature">
                        <div>Bekasi, <?= esc($signature_date) ?></div>
                        <div>Petugas,</div>
                        <div class="report-signature-name"><?= esc($signatory_name) ?></div>
                    </div>
                    <div class="report-chart-block">
                        <h5 class="mt-4">Tren per Bulan</h5>
                        <p class="text-muted">
                            <?= $is_bdrs
                                ? 'Jumlah distribusi per golongan darah berdasarkan bulan.'
                                : 'Jumlah permintaan dan pemakaian per golongan darah berdasarkan bulan.' ?>
                        </p>
                        <div style="height: 350px;">
                        <canvas id="bloodGroupChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.querySelectorAll('[data-print-report]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.body.classList.add('printing-report');
            document.querySelectorAll('.report-card').forEach(function (card) {
                card.id === button.dataset.printReport
                    ? card.setAttribute('id', 'printReportTarget')
                    : card.classList.add('d-print-none');
            });
            window.print();
        });
    });

    window.addEventListener('afterprint', function () {
        document.body.classList.remove('printing-report');
        var target = document.getElementById('printReportTarget');
        if (target) {
            target.id = target.dataset.originalId;
        }
        document.querySelectorAll('.report-card').forEach(function (card) {
            card.classList.remove('d-print-none');
        });
    });

    document.querySelectorAll('.report-card').forEach(function (card) {
        card.dataset.originalId = card.id;
    });

    const reportPeriods = <?= json_encode($periods, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const bloodGroupDatasets = <?= json_encode($blood_group_chart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const needChartData = <?= json_encode($need_chart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    new Chart(document.getElementById('bloodGroupChart'), {
        type: 'line',
        data: { labels: reportPeriods, datasets: bloodGroupDatasets },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    new Chart(document.getElementById('needChart'), {
        type: 'bar',
        data: {
            labels: needChartData.labels,
            datasets: needChartData.requests.concat(needChartData.usage)
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
</script>

<?= $this->include('templates/footer') ?>
