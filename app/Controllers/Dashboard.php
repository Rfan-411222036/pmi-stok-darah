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

    public function checkLowStock()
    {
        $role = session()->get('role');
        if ($role !== 'admin' && $role !== 'pimpinan') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $thresholds = [ 'A' => 10, 'B' => 10, 'O' => 15, 'AB' => 5 ];
        $produsenList = $this->produsenModel->findAll();
        $notificationModel = new \App\Models\NotificationModel();
        $userModel = new \App\Models\UserModel();
        $adminUsers = $userModel->where('role', 'admin')->findAll();
        $created = 0;

        foreach ($produsenList as $prod) {
            $prodId = $prod['id_produsen'];
            foreach ($thresholds as $gol => $th) {
                $remaining = $this->stokModel->countAvailableByProdusenGolonganJenis($prodId, $gol, null);
                if ($remaining < $th) {
                    foreach ($adminUsers as $admin) {
                        $notificationModel->insert([
                            'user_id' => $admin['id_user'],
                            'title' => 'Stok Rendah: ' . $gol,
                            'message' => 'Stok kantong darah ' . $gol . ' untuk BDRS ' . $prod['nama'] . ' tersisa ' . $remaining . ' dan di bawah batas minimal.',
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        $created++;
                    }
                }
            }
        }

        if ($created > 0) {
            return redirect()->back()->with('success', "Notifikasi stok rendah dibuat: $created");
        }

        return redirect()->back()->with('success', 'Tidak ada stok yang berada di bawah ambang saat ini');
    }

    public function index()
    {
        $currentRole = session()->get('role');
        $currentUserId = session()->get('id_user');
        $isAdminLike = in_array($currentRole, ['admin', 'pimpinan'], true);

        $produsenId = null;
        if ($currentRole === 'bdrs') {
            $ownProdusen = $this->produsenModel->getProdusenByUser($currentUserId);
            $produsenId = $ownProdusen['id_produsen'] ?? null;
        }

        $totalStok = $this->stokModel->getStokTersedia($produsenId);
        $totalProdusen = $this->produsenModel->getTotalProdusen($isAdminLike ? null : $currentUserId);
        $totalRS = $this->rsModel->getTotalRumahSakit($isAdminLike ? null : $currentUserId);
        $totalDistribusi = $this->distribusiModel->getTotalDistribusi();
        $totalPemusnahan = $this->pemusnahanModel->getTotalPemusnahan();
        $stokExpired = $this->stokModel->getStokExpired();
        $stokMendekatiExpired = $this->stokModel->getStokMendekatiExpired();
        $distribusiHariIni = $this->distribusiModel->getDistribusiHariIni();
        $distribusiBulanIni = $this->distribusiModel->getDistribusiBulanIni();

        $stokByGolongan = $this->stokModel->getStokByGolongan($produsenId);
        $stokByJenis = $this->stokModel->getStokByJenis($produsenId);

        if ($isAdminLike) {
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
        $priorityNotifications = [];
        if (in_array($currentRole, ['admin', 'pimpinan', 'bdrs', 'rs'], true)) {
            $priorityNotifications = $this->notificationModel
                ->where('user_id', $currentUserId)
                ->orderBy('created_at', 'DESC')
                ->findAll(5);
        }

        if ($isAdminLike) {
            $lowStockNotifications = $this->notificationModel->orderBy('created_at', 'DESC')->findAll(5);
        }

        $recentDistribusi = $this->distribusiModel->getRecentDistribusi(5);

        // Monthly distribusi chart (for admin)
        $monthlyDistribusi = [];
        if ($isAdminLike) {
            $monthlyCounts = $this->distribusiModel->getDistribusiPerMonth();
            $monthlyLabels = array_map(function($m){ return date('F', mktime(0,0,0,$m,1)); }, range(1,12));
            $monthlyDistribusi = [
                'labels' => $monthlyLabels,
                'data' => array_values($monthlyCounts)
            ];
        }

        $produsenList = $this->produsenModel->orderBy('nama')->findAll();

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
            'priority_notifications' => $priorityNotifications,
            'recent_distribusi' => $recentDistribusi,
            'monthly_distribusi' => $monthlyDistribusi,
            'produsen_list' => $produsenList,
            'golongan_list' => $this->stokModel->getDistinctGolongan(),
            'jenis_list' => $this->stokModel->getDistinctJenis(),
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

    public function previewLaporan()
    {
        $produsenFilter = $this->request->getGet('id_produsen') ?: null;
        $golFilter = $this->request->getGet('gol_dar') ?: null;
        $jenisFilter = $this->request->getGet('jenis') ?: null;

        $builder = $this->stokModel->select('stok.id_bag, stok.no_kantong, stok.jenis_darah, stok.gol_dar, stok.rhesus, stok.volume, stok.tanggal_produksi, stok.tanggal_expired, stok.status, stok.keterangan, produsen.nama as nama_produsen, produsen.jenis as jenis_produsen')
                                   ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left');

        if ($produsenFilter) {
            $builder = $builder->where('stok.id_produsen', $produsenFilter);
        }
        if ($golFilter) {
            $builder = $builder->where('stok.gol_dar', $golFilter);
        }
        if ($jenisFilter) {
            $builder = $builder->where('stok.jenis_darah', $jenisFilter);
        }

        $stokData = $builder->orderBy('stok.tanggal_expired', 'ASC')->findAll(10);

        return $this->response->setJSON([
            'success' => true,
            'rows' => array_map(function ($item) {
                return [
                    'no_kantong' => $item['no_kantong'] ?? '-',
                    'nama_produsen' => $item['nama_produsen'] ?? '-',
                    'jenis_darah' => $item['jenis_darah'] ?? '-',
                    'gol_dar' => $item['gol_dar'] ?? '-',
                    'rhesus' => $item['rhesus'] ?? '-',
                    'volume' => ($item['volume'] ?? '-') . ' ml',
                    'tanggal_produksi' => $item['tanggal_produksi'] ?? '-',
                    'tanggal_expired' => $item['tanggal_expired'] ?? '-',
                    'status' => ucfirst($item['status'] ?? '-'),
                    'keterangan' => $item['keterangan'] ?? '-',
                ];
            }, $stokData),
        ]);
    }

    public function downloadLaporan()
    {
        $produsenFilter = $this->request->getGet('id_produsen') ?: null;
        $golFilter = $this->request->getGet('gol_dar') ?: null;
        $jenisFilter = $this->request->getGet('jenis') ?: null;

        $builder = $this->stokModel->select('stok.id_bag, stok.no_kantong, stok.jenis_darah, stok.gol_dar, stok.rhesus, stok.volume, stok.tanggal_produksi, stok.tanggal_expired, stok.status, stok.keterangan, produsen.nama as nama_produsen, produsen.jenis as jenis_produsen')
                                   ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left');

        if ($produsenFilter) {
            $builder = $builder->where('stok.id_produsen', $produsenFilter);
        }
        if ($golFilter) {
            $builder = $builder->where('stok.gol_dar', $golFilter);
        }
        if ($jenisFilter) {
            $builder = $builder->where('stok.jenis_darah', $jenisFilter);
        }

        $stokData = $builder->orderBy('stok.tanggal_expired', 'ASC')->findAll();

        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN STOK DARAH', date('d F Y'));

        $pdf->addTitle('LAPORAN STOK DARAH');
        $pdf->Ln(3);

        $meta = [];
        if ($produsenFilter) {
            $produsen = $this->produsenModel->find($produsenFilter);
            if ($produsen) {
                $meta[] = 'BDRS: ' . ($produsen['nama'] ?? '-');
            }
        }
        if ($golFilter) {
            $meta[] = 'Golongan: ' . $golFilter;
        }
        if ($jenisFilter) {
            $meta[] = 'Jenis: ' . $jenisFilter;
        }

        if (!empty($meta)) {
            foreach ($meta as $line) {
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 6, $line, 0, 1, 'L');
            }
            $pdf->Ln(2);
        }

        $stats = [
            ['label' => 'Total Unit', 'value' => count($stokData)],
        ];
        $pdf->addStats($stats);

        if (empty($stokData)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data stok darah.', 0, 1, 'C');
        } else {
            $headers = ['No', 'No. Kantong', 'BDRS', 'Jenis', 'Gol', 'Rhesus', 'Volume', 'Produksi', 'Expired', 'Status', 'Keterangan'];
            $tableData = [];

            $no = 1;
            foreach ($stokData as $item) {
                $tableData[] = [
                    $no++,
                    $item['no_kantong'] ?? '-',
                    $item['nama_produsen'] ?? '-',
                    $item['jenis_darah'] ?? '-',
                    $item['gol_dar'] ?? '-',
                    $item['rhesus'] ?? '-',
                    ($item['volume'] ?? '-') . ' ml',
                    $item['tanggal_produksi'] ?? '-',
                    $item['tanggal_expired'] ?? '-',
                    ucfirst($item['status'] ?? '-'),
                    substr($item['keterangan'] ?? '-', 0, 25),
                ];
            }

            $columnWidths = [8, 24, 30, 20, 12, 12, 18, 18, 20, 20, 30];
            $pdf->addTable($headers, $tableData, $columnWidths);
        }

        $pdf->Output('Laporan_Stok_' . date('d-m-Y') . '.pdf', 'D');
    }

    public function previewDistribusi()
    {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');
        $produsenFilter = $this->request->getGet('id_produsen') ?: null;
        $rsFilter = $this->request->getGet('id_rs') ?: null;

        $builder = $this->distribusiModel->select('id_distribusi, id_bag, id_rs, tanggal_distribusi, penerima, keperluan');

        if ($from) {
            $builder = $builder->where('tanggal_distribusi >=', $from);
        }
        if ($to) {
            $builder = $builder->where('tanggal_distribusi <=', $to);
        }
        if ($produsenFilter) {
            $builder = $builder->where('id_produsen', $produsenFilter);
        }
        if ($rsFilter) {
            $builder = $builder->where('id_rs', $rsFilter);
        }

        $distribusiData = $builder->orderBy('tanggal_distribusi', 'DESC')->findAll(10);

        return $this->response->setJSON([
            'success' => true,
            'rows' => array_map(function ($item) {
                $rs = $this->rsModel->find($item['id_rs']);
                $rsName = $rs['nama_rs'] ?? '-';
                $bag = $this->stokModel->find($item['id_bag']);
                $noKantong = $bag['no_kantong'] ?? '-';

                return [
                    'no_kantong' => $noKantong,
                    'tanggal_distribusi' => date('d-m-Y', strtotime($item['tanggal_distribusi'] ?? 'now')),
                    'penerima' => $item['penerima'] ?? '-',
                    'keperluan' => $item['keperluan'] ?? '-',
                    'rs' => $rsName,
                ];
            }, $distribusiData),
        ]);
    }

    public function downloadDistribusi()
    {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');
        $produsenFilter = $this->request->getGet('id_produsen') ?: null;
        $rsFilter = $this->request->getGet('id_rs') ?: null;

        $builder = $this->distribusiModel->select('id_distribusi, id_bag, id_rs, tanggal_distribusi, penerima, keperluan');
        if ($from) $builder = $builder->where('tanggal_distribusi >=', $from);
        if ($to) $builder = $builder->where('tanggal_distribusi <=', $to);
        if ($produsenFilter) $builder = $builder->where('id_produsen', $produsenFilter);
        if ($rsFilter) $builder = $builder->where('id_rs', $rsFilter);

        $distribusiData = $builder->orderBy('tanggal_distribusi', 'DESC')->findAll();

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
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        $builder = $this->pemusnahanModel->select('id_pemusnahan, id_bag, tanggal_pemusnahan, alasan, keterangan, petugas');
        if ($from) $builder = $builder->where('tanggal_pemusnahan >=', $from);
        if ($to) $builder = $builder->where('tanggal_pemusnahan <=', $to);

        $pemusnahanData = $builder->orderBy('tanggal_pemusnahan', 'DESC')->findAll();

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
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        $builder = $returnModel->select('return_darah.id_return, return_darah.tanggal_retur, return_darah.alasan_return, return_darah.kondisi_darah, stok.no_kantong, produsen.nama as nama_produsen')
                     ->join('stok', 'stok.id_bag = return_darah.id_bag')
                     ->join('produsen', 'produsen.id_produsen = stok.id_produsen', 'left');
        if ($from) $builder = $builder->where('return_darah.tanggal_retur >=', $from);
        if ($to) $builder = $builder->where('return_darah.tanggal_retur <=', $to);

        $returData = $builder->orderBy('return_darah.tanggal_retur', 'DESC')->findAll();

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
