<?php

namespace App\Controllers;

use App\Models\DistribusiModel;
use App\Models\ProdusenModel;
use App\Models\PermintaanModel;
use App\Libraries\PdfGenerator;

class Laporan extends BaseController
{
    private const BLOOD_GROUPS = ['A', 'B', 'AB', 'O'];
    private const DIAGNOSES = ['CKD ON HD', 'ANEMIA', 'POST SC', 'DBD', 'THALASEMIA', 'DLL'];
    private const REPORT_BLOOD_GROUPS = ['A', 'B', 'O', 'AB'];

    protected $permintaanModel;
    protected $distribusiModel;

    public function __construct()
    {
        $this->permintaanModel = new PermintaanModel();
        $this->distribusiModel = new DistribusiModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $isBdrs = $role === 'bdrs';
        if (!$this->isAdministrator() && !$isBdrs) {
            return redirect()->to('/dashboard')->with('error', 'Akses tidak diizinkan.');
        }

        $produsen = $isBdrs ? $this->getCurrentProdusen() : null;
        $produsenId = $produsen ? (int) $produsen['id_produsen'] : null;
        if ($isBdrs && $produsenId === null) {
            return redirect()->to('/dashboard')->with('error', 'Data BDRS untuk akun ini belum terhubung.');
        }

        $range = $this->getDateRange();
        if ($range === null) {
            return redirect()->to('/laporan');
        }

        $periods = $this->getPeriods($range['from'], $range['to']);
        $requestGroupTrend = $isBdrs ? [] : $this->permintaanModel->getTrendByGolongan($range['from'], $range['to']);
        $usageGroupTrend = $this->distribusiModel->getTrendByGolongan($range['from'], $range['to'], $produsenId);
        $requestNeedTrend = $isBdrs ? [] : $this->permintaanModel->getTrendByKeperluan($range['from'], $range['to']);
        $usageNeedTrend = $this->distribusiModel->getTrendByKeperluan($range['from'], $range['to'], $produsenId);
        $requestByGroup = $this->indexTrend(
            $requestGroupTrend,
            'gol_dar'
        );
        $usageByGroup = $this->indexTrend(
            $usageGroupTrend,
            'gol_dar'
        );
        $requestByNeed = $this->indexTrend(
            $requestNeedTrend,
            'keperluan'
        );
        $usageByNeed = $this->indexTrend(
            $usageNeedTrend,
            'keperluan'
        );

        $diagnosisReport = $this->buildReportData(
            'keperluan',
            $range['from'],
            $range['to'],
            $requestNeedTrend,
            $usageNeedTrend,
            $isBdrs
        );
        $bloodGroupReport = $this->buildReportData(
            'golongan',
            $range['from'],
            $range['to'],
            $requestGroupTrend,
            $usageGroupTrend,
            $isBdrs
        );

        $groups = [];
        foreach (self::BLOOD_GROUPS as $group) {
            $groups[$group] = true;
        }
        foreach ([$requestByGroup, $usageByGroup] as $trend) {
            foreach ($trend as $row) {
                $groups[$row['category']] = true;
            }
        }
        $groups = array_keys($groups);

        $bloodGroupRows = [];
        $bloodGroupChart = [];
        $colors = ['A' => '#dc3545', 'B' => '#007bff', 'AB' => '#28a745', 'O' => '#f0ad00'];
        foreach ($groups as $group) {
            $requests = [];
            $usage = [];
            foreach ($periods as $period) {
                $key = $period . '|' . $group;
                $requested = $requestByGroup[$key]['total'] ?? 0;
                $used = $usageByGroup[$key]['total'] ?? 0;
                $requests[] = $requested;
                $usage[] = $used;
                $bloodGroupRows[] = [
                    'period' => $period,
                    'category' => $group,
                    'requests' => $requested,
                    'usage' => $used,
                ];
            }
            $color = $colors[$group] ?? '#6c757d';
            if (!$isBdrs) {
                $bloodGroupChart[] = [
                    'label' => $group . ' - Permintaan',
                    'data' => $requests,
                    'borderColor' => $color,
                    'backgroundColor' => $color,
                    'tension' => 0.25,
                ];
            }
            $bloodGroupChart[] = [
                'label' => $group . ($isBdrs ? ' - Distribusi' : ' - Pemakaian'),
                'data' => $usage,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'borderDash' => [6, 4],
                'tension' => 0.25,
            ];
        }

        $needs = [];
        foreach ([$requestByNeed, $usageByNeed] as $trend) {
            foreach ($trend as $row) {
                $needs[$row['category']] = true;
            }
        }
        $needs = array_keys($needs);
        sort($needs, SORT_NATURAL | SORT_FLAG_CASE);

        $needRows = [];
        $needLabels = [];
        $requestNeedDatasets = [];
        $usageNeedDatasets = [];
        foreach ($needs as $need) {
            $requested = [];
            $usage = [];
            foreach ($periods as $period) {
                $key = $period . '|' . $need;
                $requestedCount = $requestByNeed[$key]['total'] ?? 0;
                $usageCount = $usageByNeed[$key]['total'] ?? 0;
                $requested[] = $requestedCount;
                $usage[] = $usageCount;
                $needRows[] = [
                    'period' => $period,
                    'category' => $need,
                    'requests' => $requestedCount,
                    'usage' => $usageCount,
                ];
            }
            $needLabels[] = $need;
            $color = sprintf('hsl(%d, 65%%, 48%%)', (count($needLabels) * 67) % 360);
            if (!$isBdrs) {
                $requestNeedDatasets[] = [
                    'label' => $need . ' - Permintaan',
                    'data' => $requested,
                    'backgroundColor' => $color,
                ];
            }
            $usageNeedDatasets[] = [
                'label' => $need . ($isBdrs ? ' - Distribusi' : ' - Pemakaian'),
                'data' => $usage,
                'backgroundColor' => $color,
            ];
        }

        return view('laporan/index', [
            'title' => 'Laporan Tren Darah',
            'page_title' => $isBdrs
                ? 'Laporan Tren Distribusi Darah'
                : 'Laporan Tren Permintaan dan Pemakaian Darah',
            'is_bdrs' => $isBdrs,
            'bank_name' => $produsen['nama'] ?? 'Semua BDRS',
            'from' => $range['from'],
            'to' => $range['to'],
            'periods' => $periods,
            'period_label' => $this->formatPeriodLabel($range['from'], $range['to']),
            'signature_date' => date('d/m/Y'),
            'signatory_name' => '............................',
            'diagnosis_report' => $diagnosisReport,
            'blood_group_report' => $bloodGroupReport,
            'blood_group_rows' => $bloodGroupRows,
            'blood_group_chart' => $bloodGroupChart,
            'need_rows' => $needRows,
            'need_chart' => [
                'labels' => $periods,
                'requests' => $requestNeedDatasets,
                'usage' => $usageNeedDatasets,
            ],
        ]);
    }

    public function download()
    {
        $role = session()->get('role');
        $isBdrs = $role === 'bdrs';
        if (!$this->isAdministrator() && !$isBdrs) {
            return redirect()->to('/dashboard')->with('error', 'Akses tidak diizinkan.');
        }

        $produsen = $isBdrs ? $this->getCurrentProdusen() : null;
        $produsenId = $produsen ? (int) $produsen['id_produsen'] : null;
        if ($isBdrs && $produsenId === null) {
            return redirect()->to('/dashboard')->with('error', 'Data BDRS untuk akun ini belum terhubung.');
        }

        $range = $this->getDateRange();
        if ($range === null) {
            return redirect()->to('/laporan');
        }

        $type = $this->request->getGet('type');
        if (!in_array($type, ['golongan', 'keperluan'], true)) {
            return redirect()->to('/laporan')->with('error', 'Jenis laporan tidak valid.');
        }

        $report = $this->getReportData($type, $range['from'], $range['to'], $produsenId, $isBdrs);
        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo($report['title'], date('d F Y'));
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 7, 'Nama Bank Darah: ' . ($produsen['nama'] ?? 'Semua BDRS'), 0, 1, 'L');
        $pdf->Cell(0, 7, $report['period'], 0, 1, 'L');
        $pdf->addTable(
            ['No', 'Diagnosa', 'Permintaan', 'Pemakaian'],
            $report['table_rows'],
            [15, 75, 45, 45]
        );
        $pdf->addSignature('............................');

        $filename = $type === 'golongan' ? 'laporan-golongan-darah' : 'laporan-diagnosa';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader(
                'Content-Disposition',
                'attachment; filename="' . $filename . '-' . $range['from'] . '-sd-' . $range['to'] . '.pdf"'
            )
            ->setBody($pdf->Output($filename . '.pdf', 'S'));
    }

    private function isAdministrator(): bool
    {
        return session()->get('role') === 'admin';
    }

    private function getCurrentProdusen(): ?array
    {
        $userId = session()->get('id_user');
        $produsen = $userId ? (new ProdusenModel())->getProdusenByUser($userId) : null;
        if (!$produsen && session()->has('id_produsen')) {
            $produsen = (new ProdusenModel())->find(session()->get('id_produsen'));
        }

        return $produsen ?: null;
    }

    private function getDateRange(): ?array
    {
        $from = $this->request->getGet('from') ?: date('Y-01-01');
        $to = $this->request->getGet('to') ?: date('Y-m-d');

        if (
            !is_string($from)
            || !is_string($to)
            || !$this->isValidDate($from)
            || !$this->isValidDate($to)
            || $from > $to
        ) {
            session()->setFlashdata('error', 'Periode laporan tidak valid.');
            return null;
        }

        return ['from' => $from, 'to' => $to];
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    private function getPeriods(string $from, string $to): array
    {
        $first = new \DateTimeImmutable(substr($from, 0, 7) . '-01');
        $last = new \DateTimeImmutable(substr($to, 0, 7) . '-01');
        $periods = [];

        for ($period = $first; $period <= $last; $period = $period->modify('+1 month')) {
            $periods[] = $period->format('Y-m');
        }

        return $periods;
    }

    private function indexTrend(array $rows, string $categoryField): array
    {
        $indexed = [];
        foreach ($rows as $row) {
            $key = $row['period'] . '|' . $row[$categoryField];
            $indexed[$key] = [
                'category' => $row[$categoryField],
                'total' => (int) $row['total'],
            ];
        }

        return $indexed;
    }

    private function aggregateTrend(array $rows, string $categoryField, ?callable $normalize = null): array
    {
        $totals = [];
        foreach ($rows as $row) {
            $category = $normalize ? $normalize($row[$categoryField]) : trim((string) $row[$categoryField]);
            $totals[$category] = ($totals[$category] ?? 0) + (int) $row['total'];
        }

        return $totals;
    }

    private function buildSummaryRows(array $categories, array $requests, array $usage): array
    {
        $rows = [];
        foreach ($categories as $index => $category) {
            $label = match ($category) {
                'CKD ON HD' => 'CKD on HD',
                'DLL' => 'DLL',
                default => in_array($category, self::REPORT_BLOOD_GROUPS, true)
                    ? 'Golongan Darah ' . $category
                    : $category,
            };
            $rows[] = [
                'number' => $index + 1,
                'label' => $label,
                'requests' => $requests[$category] ?? 0,
                'usage' => $usage[$category] ?? 0,
            ];
        }

        return $rows;
    }

    private function getSummaryTotals(array $rows): array
    {
        return [
            'requests' => array_sum(array_column($rows, 'requests')),
            'usage' => array_sum(array_column($rows, 'usage')),
        ];
    }

    private function getReportData(
        string $type,
        string $from,
        string $to,
        ?int $produsenId = null,
        bool $distributionOnly = false
    ): array {
        if ($type === 'golongan') {
            $usageRows = $this->distribusiModel->getTrendByGolongan($from, $to, $produsenId);
            $report = $this->buildReportData(
                'golongan',
                $from,
                $to,
                $distributionOnly ? [] : $this->permintaanModel->getTrendByGolongan($from, $to),
                $usageRows,
                $distributionOnly
            );
        } else {
            $usageRows = $this->distribusiModel->getTrendByKeperluan($from, $to, $produsenId);
            $report = $this->buildReportData(
                'keperluan',
                $from,
                $to,
                $distributionOnly ? [] : $this->permintaanModel->getTrendByKeperluan($from, $to),
                $usageRows,
                $distributionOnly
            );
        }

        return $report;
    }

    private function buildReportData(
        string $type,
        string $from,
        string $to,
        array $requestRows,
        array $usageRows,
        bool $distributionOnly = false
    ): array {
        if ($type === 'golongan') {
            $rows = $this->buildSummaryRows(
                self::REPORT_BLOOD_GROUPS,
                $this->aggregateTrend($requestRows, 'gol_dar'),
                $this->aggregateTrend($usageRows, 'gol_dar')
            );
            $title = $distributionOnly
                ? 'Laporan Distribusi Darah Berdasarkan Golongan Darah'
                : 'Laporan Kebutuhan Darah Berdasarkan Golongan Darah';
            $categoryLabel = 'Golongan Darah';
        } else {
            $rows = $this->buildSummaryRows(
                self::DIAGNOSES,
                $this->aggregateTrend($requestRows, 'keperluan', [$this, 'normalizeDiagnosis']),
                $this->aggregateTrend($usageRows, 'keperluan', [$this, 'normalizeDiagnosis'])
            );
            $title = $distributionOnly
                ? 'Laporan Distribusi Darah Berdasarkan Diagnosa/Keperluan'
                : 'Laporan Kebutuhan Darah Berdasarkan Diagnosa';
            $categoryLabel = 'Diagnosa';
        }

        $tableRows = array_map(static function (array $row): array {
            return [
                (string) $row['number'],
                $row['label'],
                number_format($row['requests'], 0, ',', '.'),
                number_format($row['usage'], 0, ',', '.'),
            ];
        }, $rows);
        $totals = $this->getSummaryTotals($rows);
        $tableRows[] = [
            '',
            'TOTAL',
            number_format($totals['requests'], 0, ',', '.'),
            number_format($totals['usage'], 0, ',', '.'),
        ];

        return [
            'title' => $title,
            'category_label' => $categoryLabel,
            'period' => $this->formatPeriodLabel($from, $to),
            'rows' => $rows,
            'totals' => $totals,
            'table_rows' => $tableRows,
        ];
    }

    public function normalizeDiagnosis(string $diagnosis): string
    {
        $diagnosis = mb_strtoupper(trim($diagnosis));
        if ($diagnosis === 'LAIN-LAIN' || $diagnosis === 'LAIN LAIN' || $diagnosis === 'DLL') {
            return 'DLL';
        }

        foreach (self::DIAGNOSES as $category) {
            if ($category !== 'DLL' && $diagnosis === $category) {
                return $category;
            }
        }

        return 'DLL';
    }

    private function formatPeriodLabel(string $from, string $to): string
    {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $format = static function (string $date) use ($months): string {
            $value = new \DateTimeImmutable($date);
            return $value->format('j') . ' ' . $months[(int) $value->format('n')] . ' ' . $value->format('Y');
        };

        $fromDate = new \DateTimeImmutable($from);
        $toDate = new \DateTimeImmutable($to);
        if ($fromDate->format('Y-m') === $toDate->format('Y-m')) {
            return 'Periode Tanggal: ' . $fromDate->format('j') . ' '
                . $months[(int) $fromDate->format('n')] . ' - '
                . $toDate->format('j') . ' ' . $months[(int) $toDate->format('n')]
                . ' ' . $toDate->format('Y');
        }

        return 'Periode Tanggal: ' . $format($from) . ' - ' . $format($to);
    }

}
