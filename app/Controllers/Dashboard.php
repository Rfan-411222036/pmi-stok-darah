<?php

namespace App\Controllers;

use App\Models\StokModel;
use App\Models\ProdusenModel;
use App\Models\RumahSakitModel;
use App\Models\DistribusiModel;
use App\Models\PemusnahanModel;
use App\Models\UserModel;
use App\Models\NotificationModel;
use App\Libraries\PdfGenerator;

class Dashboard extends BaseController
{
    protected $stokModel;
    protected $produsenModel;
    protected $rsModel;
    protected $distribusiModel;
    protected $pemusnahanModel;
    protected $userModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->stokModel = new StokModel();
        $this->produsenModel = new ProdusenModel();
        $this->rsModel = new RumahSakitModel();
        $this->distribusiModel = new DistribusiModel();
        $this->pemusnahanModel = new PemusnahanModel();
        $this->userModel = new UserModel();
        $this->notificationModel = new NotificationModel();

        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        try {
            if (!$db->tableExists('notifications')) {
                $forge->addField([
                    'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
                    'user_id' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
                    'title' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => false ],
                    'message' => [ 'type' => 'TEXT', 'null' => false ],
                    'created_at' => [ 'type' => 'DATETIME', 'null' => false ]
                ]);
                $forge->addKey('id', true);
                $forge->createTable('notifications', true);
            }
        } catch (\Exception $e) {
            // Tabel notifikasi tidak tersedia; akan ditangani di view jika perlu.
        }
    }

    public function index()
    {
        $currentRole = session()->get('role');
        $currentUserId = session()->get('id_user');

        $produsenId = null;
        if ($currentRole === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser($currentUserId);
            $produsenId = $ownProdusen['id_produsen'] ?? null;
        }

        $totalStok = $this->stokModel->getStokTersedia($produsenId);
        $totalProdusen = $this->produsenModel->getTotalProdusen($currentRole === 'admin' ? null : $currentUserId);
        $totalRS = $this->rsModel->getTotalRumahSakit($currentRole === 'admin' ? null : $currentUserId);
        $totalDistribusi = $this->distribusiModel->getTotalDistribusi();
        $totalPemusnahan = $this->pemusnahanModel->getTotalPemusnahan();
        $stokExpired = $this->stokModel->getStokExpired();
        $stokMendekatiExpired = $this->stokModel->getStokMendekatiExpired();
        $distribusiHariIni = $this->distribusiModel->getDistribusiHariIni();
        $distribusiBulanIni = $this->distribusiModel->getDistribusiBulanIni();

        $stokByGolongan = $this->stokModel->getStokByGolongan($produsenId);
        $stokByJenis = $this->stokModel->getStokByJenis($produsenId);

        if ($currentRole === 'admin') {
            $stokPerBDRS = $this->distribusiModel->getStokPerBDRS();
        } else {
            $stokPerBDRS = $this->distribusiModel->getStokPerBDRS($currentUserId);
        }

        $chartLabels = [];
        $chartData = [];
        foreach ($stokPerBDRS as $item) {
            $label = $item['nama_produsen'] ?? $item['nama'] ?? ($item['nama_rs'] ?? 'Unknown');
            $chartLabels[] = $label;
            $chartData[] = (int) ($item['jumlah_stok'] ?? 0);
        }

        $totalUsers = $this->userModel->getTotalUsers();
        $totalAdmins = $this->userModel->getTotalAdmins();
        $totalStaff = $this->userModel->getTotalStaff();

        $lowStockNotifications = [];
        if ($currentRole === 'admin') {
            $lowStockNotifications = $this->notificationModel->orderBy('created_at', 'DESC')->findAll(5);
        }

        $recentDistribusi = $this->distribusiModel->getRecentDistribusi(5);

        // Monthly distribusi chart (for admin)
        $monthlyDistribusi = [];
        if ($currentRole === 'admin') {
            $monthlyCounts = $this->distribusiModel->getDistribusiPerMonth();
            $monthlyLabels = array_map(function($m){ return date('F', mktime(0,0,0,$m,1)); }, range(1,12));
            $monthlyDistribusi = [
                'labels' => $monthlyLabels,
                'data' => array_values($monthlyCounts)
            ];
        }

        $data = [
            'title' => 'Dashboard',
            'page_title' => 'Dashboard Management Stok Darah PMI',
            'total_stok' => $totalStok,
            'total_produsen' => $totalProdusen,
            'total_rs' => $totalRS,
            'total_distribusi' => $totalDistribusi,
            'total_pemusnahan' => $totalPemusnahan,
            'distribusi_hari_ini' => $distribusiHariIni,
            'distribusi_bulan_ini' => $distribusiBulanIni,
            'stok_expired' => $stokExpired,
            'stok_mendekati_expired' => $stokMendekatiExpired,
            'stok_by_golongan' => $stokByGolongan,
            'stok_by_jenis' => $stokByJenis,
            'stok_per_bdrs' => $stokPerBDRS,
            'current_role' => $currentRole,
            'chart_labels' => $chartLabels,
            'chart_data' => $chartData,
            'total_users' => $totalUsers,
            'total_admins' => $totalAdmins,
            'total_staff' => $totalStaff,
            'low_stock_notifications' => $lowStockNotifications,
            'recent_distribusi' => $recentDistribusi,
            'monthly_distribusi' => $monthlyDistribusi,
        ];

        return view('dashboard/index', $data);
    }

    public function distribusiPerGudang()
    {
        $month = (int) $this->request->getGet('month');
        $year = (int) $this->request->getGet('year') ?: date('Y');

        if (!$month || $month < 1 || $month > 12) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid month']);
        }

        $rows = $this->distribusiModel->getDistribusiPerGudangForMonth($month, $year);
        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    public function downloadLaporan()
    {
        $stokData = $this->stokModel->select('id_bag, no_kantong, jenis_darah, gol_dar, rhesus, volume, tanggal_produksi, tanggal_expired, status')
                                     ->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN STOK DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN STOK DARAH');
        $pdf->Ln(3);

        $stats = [
            ['label' => 'Total Unit', 'value' => count($stokData)],
        ];
        $pdf->addStats($stats);

        if (empty($stokData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data stok darah.', 0, 1, 'C');
        } else {
            $headers = ['No', 'No. Kantong', 'Jenis', 'Goldar', 'Volume', 'Expired Date'];
            $tableData = [];

            $no = 1;
            foreach ($stokData as $item) {
                $tableData[] = [
                    $no++,
                    $item['no_kantong'] ?? '-',
                    ($item['jenis_darah'] ?? '-') . ' ' . ($item['gol_dar'] ?? '-') . ($item['rhesus'] ?? '+'),
                    $item['gol_dar'] ?? '-',
                    ($item['volume'] ?? '-') . ' ml',
                    $item['tanggal_expired'] ?? '-',
                ];
            }

            $columnWidths = [8, 25, 25, 15, 20, 27];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Stok_' . date('d-m-Y') . '.pdf', 'D');
    }

    public function downloadDistribusi()
    {
        $distribusiData = $this->distribusiModel->select('id_distribusi, id_bag, id_rs, tanggal_distribusi, penerima, keperluan')
                                                ->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN DISTRIBUSI DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN DISTRIBUSI DARAH');
        $pdf->Ln(3);

        $stats = [
            ['label' => 'Total Transaksi', 'value' => count($distribusiData)],
        ];
        $pdf->addStats($stats);

        if (empty($distribusiData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data distribusi.', 0, 1, 'C');
        } else {
            $headers = ['No', 'No. Kantong', 'Tanggal', 'Penerima', 'Keperluan', 'Rumah Sakit'];
            $tableData = [];

            $no = 1;
            foreach ($distribusiData as $item) {
                $rs = $this->rsModel->find($item['id_rs']);
                $rsName = $rs['nama_rs'] ?? '-';

                $bag = $this->stokModel->find($item['id_bag']);
                $noKantong = $bag['no_kantong'] ?? '-';

                $tableData[] = [
                    $no++,
                    $noKantong,
                    date('d-m-Y', strtotime($item['tanggal_distribusi'] ?? 'now')),
                    $item['penerima'] ?? '-',
                    substr($item['keperluan'] ?? '-', 0, 15),
                    $rsName,
                ];
            }

            $columnWidths = [8, 30, 18, 25, 25, 34];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Distribusi_' . date('d-m-Y') . '.pdf', 'D');
    }

    public function downloadPemusnahan()
    {
        $pemusnahanData = $this->pemusnahanModel->select('id_pemusnahan, id_bag, tanggal_pemusnahan, alasan, keterangan, petugas')
                                                ->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN PEMUSNAHAN DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN PEMUSNAHAN DARAH');
        $pdf->Ln(3);

        $stats = [
            ['label' => 'Total Pemusnahan', 'value' => count($pemusnahanData)],
        ];
        $pdf->addStats($stats);

        if (empty($pemusnahanData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data pemusnahan.', 0, 1, 'C');
        } else {
            $headers = ['No', 'No. Kantong', 'Tanggal', 'Alasan', 'Petugas', 'Keterangan'];
            $tableData = [];

            $no = 1;
            foreach ($pemusnahanData as $item) {
                $bag = $this->stokModel->find($item['id_bag']);
                $noKantong = $bag['no_kantong'] ?? '-';

                $tableData[] = [
                    $no++,
                    $noKantong,
                    date('d-m-Y', strtotime($item['tanggal_pemusnahan'] ?? 'now')),
                    $item['alasan'] ?? '-',
                    $item['petugas'] ?? '-',
                    substr($item['keterangan'] ?? '-', 0, 20),
                ];
            }

            $columnWidths = [8, 30, 18, 20, 25, 39];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Pemusnahan_' . date('d-m-Y') . '.pdf', 'D');
    }

    public function downloadRetur()
    {
        $returnModel = new \App\Models\ReturnModel();
        $returData = $returnModel->select('return_darah.id_return, return_darah.tanggal_retur, return_darah.alasan_return, return_darah.kondisi_darah, stok.no_kantong, produsen.nama as nama_produsen')
                     ->join('stok', 'stok.id_bag = return_darah.id_bag')
                     ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left')
                     ->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN RETUR DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN RETUR DARAH');
        $pdf->Ln(3);

        $stats = [
            ['label' => 'Total Retur', 'value' => count($returData)],
        ];
        $pdf->addStats($stats);

        if (empty($returData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data retur.', 0, 1, 'C');
        } else {
            $headers = ['No', 'No. Kantong', 'Tanggal', 'Alasan Retur', 'Kondisi'];
            $tableData = [];

            $no = 1;
            foreach ($returData as $item) {
                $tableData[] = [
                    $no++,
                    $item['no_kantong'] ?? '-',
                    date('d-m-Y', strtotime($item['tanggal_retur'] ?? 'now')),
                    $item['alasan_return'] ?? '-',
                    $item['kondisi_darah'] ?? '-',
                ];
            }

            $columnWidths = [8, 30, 18, 28, 18, 28];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Retur_' . date('d-m-Y') . '.pdf', 'D');
    }
}
