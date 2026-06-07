<?php

namespace App\Controllers;

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
                    'created_at' => [ 'type' => 'DATETIME', 'null' => false ]
                ]);
                $forge->addKey('id', true);
                $forge->createTable('notifications', true);
            }
        } catch (\Exception $e) {
            // swallow and allow controller to handle errors gracefully later
        }
    }

    public function index()
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');

        if ($role === 'bdrs') {
            // find produsen for this user; show all their requests, not only pending
            $produsen = $this->produsenModel->where('id_user', $userId)->first();
            if ($produsen) {
                $dataList = $this->permintaanModel->getForProdusen($produsen['id_produsen']);
            } elseif ($userId) {
                $dataList = $this->permintaanModel->getByCreator($userId);
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
                $dataList = $this->permintaanModel->getByRs($rs['id_rs']);
            } elseif ($userId) {
                $dataList = $this->permintaanModel->getByCreator($userId);
            } else {
                $dataList = [];
            }
        } else {
            $dataList = $this->permintaanModel->getAllWithNames();
        }

        return view('permintaan/index', [
            'title' => 'Permintaan Darah',
            'page_title' => 'Permintaan Darah',
            'list' => $dataList
        ]);
    }

    public function create()
    {
        $produsen = $this->produsenModel->findAll();
        $rumahSakit = $this->rsModel->findAll();

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

        $this->permintaanModel->insert($data);
        session()->setFlashdata('success', 'Permintaan berhasil diajukan. Menunggu persetujuan BDRS.');
        return redirect()->to('/permintaan');
    }

    public function approve($id)
    {
        $role = session()->get('role');
        $userId = session()->get('id_user');

        if ($role !== 'bdrs') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $perm = $this->permintaanModel->find($id);
        if (!$perm) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan');
        }

        if ($perm['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan sudah diproses');
        }

        $produsen = $this->produsenModel->where('id_user', $userId)->first();
        if (!$produsen || $produsen['id_produsen'] != $perm['id_produsen']) {
            return redirect()->back()->with('error', 'Akses BDRS tidak valid untuk permintaan ini');
        }

        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'id_bag' => 'required',
                'approval_note' => 'required'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $id_bag = $this->request->getPost('id_bag');
            $bag = $this->stokModel->find($id_bag);

            if (!$bag || $bag['id_produsen'] != $perm['id_produsen'] || $bag['status'] !== 'tersedia' || $bag['tanggal_expired'] < date('Y-m-d')) {
                return redirect()->back()->with('error', 'No kantong tidak valid atau tidak tersedia.');
            }

            $this->permintaanModel->update($id, [
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => date('Y-m-d H:i:s'),
                'id_bag' => $id_bag,
                'approval_note' => $this->request->getPost('approval_note')
            ]);

            $this->stokModel->update($id_bag, ['status' => 'terdistribusi']);

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

        return view('permintaan/approve', [
            'title' => 'Setujui Permintaan',
            'page_title' => 'Setujui Permintaan Darah',
            'permintaan' => $perm,
            'available_stock' => $availableStock,
            'validation' => \Config\Services::validation()
        ]);
    }

    protected function notifyAdminLowStock($produsenId, $golongan, $jenis, $remaining)
    {
        // Simple stub for low stock alert. Adjust to actual admin notification flow.
        $adminUsers = $this->userModel->where('role', 'admin')->findAll();

        foreach ($adminUsers as $admin) {
            $this->notificationModel->insert([
                'user_id' => $admin['id_user'],
                'title' => 'Stok Rendah: ' . $golongan . ' ' . $jenis,
                'message' => 'Stok kantong darah ' . $golongan . ' ' . $jenis . ' untuk BDRS #' . $produsenId . ' tersisa ' . $remaining . ' dan di bawah batas minimal.',
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
