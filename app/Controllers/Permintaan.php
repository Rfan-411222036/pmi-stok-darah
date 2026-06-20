<?php

namespace App\Controllers;

use App\Libraries\PdfGenerator;
use App\Models\PermintaanModel;
use App\Models\ProdusenModel;
use App\Models\RumahSakitModel;
use App\Models\StokModel;
use App\Models\UserModel;
use App\Models\NotificationModel;

class Permintaan extends BaseController
{
    protected $permintaanModel;
    protected $produsenModel;
    protected $rsModel;
    protected $stokModel;
    protected $userModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->permintaanModel = new PermintaanModel();
        $this->produsenModel = new ProdusenModel();
        $this->rsModel = new RumahSakitModel();
        $this->stokModel = new StokModel();
        $this->userModel = new UserModel();
        $this->notificationModel = new NotificationModel();

        // Ensure `permintaan` table exists; create fallback table if migrations weren't run
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        try {
            if (!$db->tableExists('permintaan')) {
                $fields = [
                    'id_permintaan' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true
                    ],
                    'id_rs' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
                    'id_produsen' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
                    'jumlah' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
                    'gol_dar' => [ 'type' => 'VARCHAR', 'constraint' => 10, 'null' => true ],
                    'jenis' => [ 'type' => 'VARCHAR', 'constraint' => 50, 'null' => true ],
                    'keterangan' => [ 'type' => 'TEXT', 'null' => true ],
                    'nama_penerima' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ],
                    'status' => [ 'type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending' ],
                    'created_by' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
                    'approved_by' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
                    'approved_at' => [ 'type' => 'DATETIME', 'null' => true ],
                    'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
                ];

                $forge->addField($fields);
                $forge->addKey('id_permintaan', true);
                $forge->createTable('permintaan', true);
            } else {
                if (!$db->fieldExists('nama_penerima', 'permintaan')) {
                    $forge->addColumn('permintaan', [
                        'nama_penerima' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true ]
                    ]);
                }
                if (!$db->fieldExists('id_bag', 'permintaan')) {
                    $forge->addColumn('permintaan', [
                        'id_bag' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ]
                    ]);
                }
                if (!$db->fieldExists('approval_note', 'permintaan')) {
                    $forge->addColumn('permintaan', [
                        'approval_note' => [ 'type' => 'TEXT', 'null' => true ]
                    ]);
                }
            }

            if (!$db->tableExists('notifications')) {
                $forge->addField([
                    'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
                    'user_id' => [ 'type' => 'INT', 'constraint' => 11, 'null' => false ],
                    'title' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => false ],
                    'message' => [ 'type' => 'TEXT', 'null' => false ],
                    'is_read' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 0 ],
                    'created_at' => [ 'type' => 'DATETIME', 'null' => false ]
                ]);
                $forge->addKey('id', true);
                $forge->createTable('notifications', true);
            } else {
                if (!$db->fieldExists('is_read', 'notifications')) {
                    $forge->addColumn('notifications', [
                        'is_read' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 0 ]
                    ]);
                }
            }
        } catch (\Exception $e) {
            // swallow and allow controller to handle errors gracefully later
        }
    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');
        $filters = [
            'search' => $this->request->getGet('search'),
            'from' => $this->request->getGet('from'),
            'to' => $this->request->getGet('to'),
            'keperluan' => $this->request->getGet('keperluan'),
            'gol_dar' => $this->request->getGet('gol_dar'),
            'jenis' => $this->request->getGet('jenis'),
        ];

        if ($role === 'bdrs') {
            // find produsen for this user; show all their requests, not only pending
            $produsen = $this->produsenModel->where('id_user', $userId)->first();
            if ($produsen) {
                $dataList = $this->permintaanModel->getForProdusen($produsen['id_produsen'], $filters);
            } elseif ($userId) {
                $dataList = $this->permintaanModel->getByCreator($userId, $filters);
            } else {
                $dataList = [];
            }
        } else if ($role === 'rs') {
            // if RS, show their own requests only; use hospital mapping or created_by fallback
            $rs = null;
            try {
                $fields = $this->rsModel->db->getFieldNames($this->rsModel->table);
            } catch (\Exception $e) {
                $fields = [];
            }

            if (in_array('id_user', $fields) && $userId) {
                $rs = $this->rsModel->where('id_user', $userId)->first();
            }

            if (!$rs && session()->has('id_rs')) {
                $rs = $this->rsModel->find(session()->get('id_rs'));
            }

            if ($rs) {
                $dataList = $this->permintaanModel->getByRs($rs['id_rs'], $filters);
            } elseif ($userId) {
                $dataList = $this->permintaanModel->getByCreator($userId, $filters);
            } else {
                $dataList = [];
            }
        } else {
            $dataList = $this->permintaanModel->getAllWithNames($filters);
        }

        return view('permintaan/index', [
            'title' => 'Permintaan Darah',
            'page_title' => 'Permintaan Darah',
            'list' => $dataList,
            'filters' => $filters,
            'keteranganOptions' => $this->permintaanModel->getDistinctKeterangan(),
        ]);
    }

    public function downloadReport()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'from' => $this->request->getGet('from'),
            'to' => $this->request->getGet('to'),
            'keperluan' => $this->request->getGet('keperluan'),
            'gol_dar' => $this->request->getGet('gol_dar'),
            'jenis' => $this->request->getGet('jenis'),
        ];

        $requests = $this->permintaanModel->getAllWithNames($filters);
        $pdf = new PdfGenerator();
        $pdf->AddPage();
        $pdf->setHeaderInfo('LAPORAN PERMINTAAN DARAH', date('d F Y'));
        $pdf->addTitle('LAPORAN PERMINTAAN DARAH');

        $meta = [];
        if (!empty($filters['from']) || !empty($filters['to'])) {
            $meta[] = 'Periode: ' . ($filters['from'] ?: '-') . ' s/d ' . ($filters['to'] ?: '-');
        }
        if (!empty($filters['keperluan'])) {
            $meta[] = 'Keperluan: ' . $filters['keperluan'];
        }
        if (!empty($filters['search'])) {
            $meta[] = 'Search: ' . $filters['search'];
        }
        if (!empty($filters['gol_dar'])) {
            $meta[] = 'Golongan: ' . $filters['gol_dar'];
        }
        if (!empty($filters['jenis'])) {
            $meta[] = 'Jenis: ' . $filters['jenis'];
        }

        if (!empty($meta)) {
            foreach ($meta as $line) {
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 6, $line, 0, 1, 'L');
            }
            $pdf->Ln(2);
        }

        if (empty($requests)) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 10, 'Tidak ada data permintaan untuk filter ini.', 0, 1, 'C');
            $pdf->Output('Laporan_Permintaan_' . date('d-m-Y') . '.pdf', 'D');
            return;
        }

        $headers = ['No', 'RS', 'BDRS', 'Jumlah', 'Gol', 'Jenis', 'Diagnosa/Keperluan', 'Nama Penerima', 'Tgl Permintaan', 'Status'];
        $tableData = [];
        $no = 1;
        foreach ($requests as $item) {
            $tableData[] = [
                $no++,
                $item['nama_rs'] ?? '-',
                $item['nama_produsen'] ?? '-',
                $item['jumlah'] ?? '-',
                $item['gol_dar'] ?? '-',
                $item['jenis'] ?? '-',
                $item['keterangan'] ?? '-',
                $item['nama_penerima'] ?? '-',
                isset($item['created_at']) ? date('d-m-Y', strtotime($item['created_at'])) : '-',
                ucfirst($item['status'] ?? '-'),
            ];
        }

        $columnWidths = [8, 30, 30, 18, 18, 20, 40, 35, 24, 22];
        $pdf->addTable($headers, $tableData, $columnWidths);
        $pdf->Output('Laporan_Permintaan_' . date('d-m-Y') . '.pdf', 'D');
    }

    public function create()
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');

        $selected_rs = null;
        if ($role === 'bdrs') {
            $prod = $this->produsenModel->getProdusenByUser($userId);
            $produsen = $prod ? [$prod] : [];
            $rumahSakit = $this->rsModel->findAll();
        } elseif ($role === 'rs') {
            // try multiple fallbacks to detect RS mapping
            $rs = $this->rsModel->getRumahSakitByUser($userId);
            if (!$rs && session()->has('id_rs')) {
                $rs = $this->rsModel->find(session()->get('id_rs'));
            }
            if (!$rs && session()->has('nama')) {
                $rs = $this->rsModel->where('nama_rs', session()->get('nama'))->first();
            }
            $rumahSakit = $rs ? [$rs] : [];
            $produsen = $this->produsenModel->findAll();
            $selected_rs = $rs['id_rs'] ?? null;
        } else {
            $produsen = $this->produsenModel->findAll();
            $rumahSakit = $this->rsModel->findAll();
        }

        // get dropdown lists for golongan and jenis
        $golongan = $this->stokModel->getDistinctGolongan();
        $jenis = $this->stokModel->getDistinctJenis();
        $produsenStockGolongan = [];
        foreach ($produsen as $p) {
            $produsenStockGolongan[$p['id_produsen']] = $this->stokModel->getDistinctGolonganByProdusen($p['id_produsen']);
        }

        return view('permintaan/create', [
            'title' => 'Buat Permintaan',
            'page_title' => 'Buat Permintaan Darah',
            'produsen' => $produsen,
            'rumah_sakit' => $rumahSakit,
            'validation' =>
                \Config\Services::validation(),
            'golongan_list' => $golongan,
            'jenis_list' => $jenis,
            'produsen_stock_golongan' => $produsenStockGolongan
            , 'selected_rs' => $selected_rs
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();
        $userId = session()->get('id_user');

        $data = [
            'id_rs' => $post['id_rs'] ?? null,
            'id_produsen' => $post['id_produsen'] ?? null,
            'jumlah' => (int) ($post['jumlah'] ?? 0),
            'gol_dar' => $post['gol_dar'] ?? null,
            'jenis' => $post['jenis'] ?? null,
            'keterangan' => ($post['diagnosa'] === 'Lain-lain' ? trim($post['diagnosa_lain'] ?? '') : $post['diagnosa']) ?: null,
            'nama_penerima' => $post['nama_penerima'] ?? null,
            'status' => 'pending',
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insertId = $this->permintaanModel->insert($data);
        if ($insertId) {
            $this->notifyBdrsNewRequest($data['id_produsen'], $insertId, $data['no_permintaan'] ?? null);
            session()->setFlashdata('success', 'Permintaan berhasil diajukan. Menunggu persetujuan BDRS.');
        } else {
            session()->setFlashdata('error', 'Gagal mengajukan permintaan. Silakan coba lagi.');
        }

        return redirect()->to('/permintaan');
    }

    protected function notifyBdrsNewRequest($produsenId, $permintaanId = null, $noPermintaan = null)
    {
        $produsen = $this->produsenModel->find($produsenId);
        if (!$produsen || empty($produsen['id_user'])) {
            return;
        }

        $this->notificationModel->insert([
            'user_id' => $produsen['id_user'],
            'title' => 'Permintaan Baru Dari RS',
            'message' => 'Permintaan baru telah diajukan untuk BDRS Anda' . ($noPermintaan ? ' (No: ' . $noPermintaan . ')' : '') . '. Cek halaman Permintaan untuk melihat detail.',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function approve($id)
    {
        $this->logApproveDebug('enter', [
            'id' => $id,
            'method' => $this->request->getMethod(),
            'session_role' => session()->get('role'),
            'session_user_id' => session()->get('id_user'),
            'post' => $this->request->getPost()
        ]);
        $role = session()->get('role');
        $userId = session()->get('id_user');

        if ($role !== 'bdrs') {
            $this->logApproveDebug('access_denied_role', ['role' => $role, 'user' => $userId]);
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $perm = $this->permintaanModel->find($id);
        if (!$perm) {
            $this->logApproveDebug('not_found', ['id' => $id]);
        }
        if (!$perm) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan');
        }

        if ($perm['status'] !== 'pending') {
            $this->logApproveDebug('already_processed', ['id' => $id, 'status' => $perm['status']]);
            return redirect()->back()->with('error', 'Permintaan sudah diproses');
        }

        $produsen = $this->produsenModel->where('id_user', $userId)->first();
        if (!$produsen && session()->has('id_produsen')) {
            $produsen = $this->produsenModel->find(session()->get('id_produsen'));
        }
        if (!$produsen && session()->has('nama')) {
            $produsen = $this->produsenModel->where('nama', session()->get('nama'))->first();
        }

        $this->logApproveDebug('produsen_resolved', ['produsen' => $produsen]);

        if (!$produsen || $produsen['id_produsen'] != $perm['id_produsen']) {
            $this->logApproveDebug('produsen_mismatch', ['session_produsen' => $produsen, 'perm_id_produsen' => $perm['id_produsen']]);
            return redirect()->back()->with('error', 'Akses BDRS tidak valid untuk permintaan ini');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $this->logApproveDebug('post_handler_enter', ['post' => $this->request->getPost()]);
            $validation = \Config\Services::validation();
            $validation->setRules([
                'id_bag' => 'required',
                'approval_note' => 'required'
            ]);

            $this->logApproveDebug('before_validation', ['post' => $this->request->getPost()]);
            try {
                $valid = $validation->withRequest($this->request)->run();
            } catch (\Exception $e) {
                $this->logApproveDebug('validation_exception', ['exception' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Validation error');
            }

            $this->logApproveDebug('validation_result', ['valid' => $valid, 'errors' => $validation->getErrors()]);
            if (!$valid) {
                $this->logApproveDebug('validation_failed', ['errors' => $validation->getErrors(), 'post' => $this->request->getPost()]);
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $id_bag = $this->request->getPost('id_bag');
            try {
                $bag = $this->stokModel->find($id_bag);
            } catch (\Exception $e) {
                $this->logApproveDebug('stok_find_exception', ['id_bag' => $id_bag, 'exception' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Error mencari kantong darah');
            }

            $this->logApproveDebug('bag_fetched', ['id_bag' => $id_bag, 'bag' => $bag]);

            if (!$bag || $bag['id_produsen'] != $perm['id_produsen'] || $bag['status'] !== 'tersedia' || $bag['tanggal_expired'] < date('Y-m-d')) {
                $this->logApproveDebug('invalid_bag', ['id_bag' => $id_bag, 'bag' => $bag ?? null, 'perm' => $perm]);
                return redirect()->back()->with('error', 'No kantong tidak valid atau tidak tersedia.');
            }

            $this->logApproveDebug('about_to_update', ['perm_id' => $id, 'id_bag' => $id_bag, 'post' => $this->request->getPost()]);
            $this->permintaanModel->update($id, [
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => date('Y-m-d H:i:s'),
                'id_bag' => $id_bag,
                'approval_note' => $this->request->getPost('approval_note')
            ]);

            $this->stokModel->update($id_bag, ['status' => 'terdistribusi']);

            $this->logApproveDebug('updated', ['perm_id' => $id, 'id_bag' => $id_bag, 'approved_by' => $userId]);

            $thresholds = [
                'A' => 10,
                'B' => 10,
                'O' => 15,
                'AB' => 5,
            ];

            $minimalBroken = false;
            if (isset($thresholds[$bag['gol_dar']])) {
                $remaining = $this->stokModel->countAvailableByProdusenGolonganJenis($perm['id_produsen'], $bag['gol_dar'], $bag['jenis_darah']);
                if ($remaining < $thresholds[$bag['gol_dar']]) {
                    $minimalBroken = true;
                }
            }

            if ($minimalBroken) {
                $this->notifyAdminLowStock($perm['id_produsen'], $bag['gol_dar'], $bag['jenis_darah'], $remaining);
                session()->setFlashdata('warning', 'Permintaan disetujui tetapi stok golongan darah sekarang berada di bawah batas minimal. Admin telah diberitahu.');
            } else {
                session()->setFlashdata('success', 'Permintaan disetujui dan no kantong darah telah dipilih.');
            }

            return redirect()->to('/permintaan');
        }

        $availableStock = $this->stokModel->getAvailableStockForProdusen($perm['id_produsen'], $perm['gol_dar'], $perm['jenis']);
        $availableStockCount = count($availableStock);
        $alternativeStock = [];

        if ($availableStockCount === 0) {
            $alternativeStock = $this->stokModel->getAvailableStockForProdusen($perm['id_produsen']);
            if (!empty($alternativeStock)) {
                $availableStock = $alternativeStock;
            }
        }

        return view('permintaan/approve', [
            'title' => 'Setujui Permintaan',
            'page_title' => 'Setujui Permintaan Darah',
            'permintaan' => $perm,
            'available_stock' => $availableStock,
            'available_stock_count' => $availableStockCount,
            'alternative_stock' => $alternativeStock,
            'validation' => \Config\Services::validation()
        ]);
    }

    protected function logApproveDebug($message, $data = [])
    {
        $path = WRITEPATH . 'logs/approve-debug.log';
        $entry = date('c') . ' ' . $message . ' ' . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        try {
            file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            // swallow — logging should not break app flow
        }
    }

    protected function notifyAdminLowStock($produsenId, $golongan, $jenis, $remaining)
    {
        // Simple stub for low stock alert. Adjust to actual admin notification flow.
        $produsen = $this->produsenModel->find($produsenId);
        $produsenName = $produsen['nama'] ?? ('BDRS #' . $produsenId);
        $adminUsers = $this->userModel->where('role', 'admin')->findAll();

        foreach ($adminUsers as $admin) {
            $this->notificationModel->insert([
                'user_id' => $admin['id_user'],
                'title' => 'Stok Rendah: ' . $golongan . ' ' . $jenis,
                'message' => 'Stok kantong darah ' . $golongan . ' ' . $jenis . ' untuk BDRS ' . $produsenName . ' tersisa ' . $remaining . ' dan di bawah batas minimal.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function reject($id)
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');

        $perm = $this->permintaanModel->find($id);
        if (!$perm) return redirect()->back()->with('error', 'Permintaan tidak ditemukan');

        $this->permintaanModel->update($id, [
            'status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Permintaan ditolak');
        return redirect()->to('/permintaan');
    }
}
