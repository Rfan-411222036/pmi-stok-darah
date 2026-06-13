<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login'
        ];
        return view('auth/login', $data);
    }

    public function processLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->getUserByEmail($email);

        if ($user && $password === $user['password']) {
            $sessionData = [
                'id_user' => $user['id_user'],
                'email' => $user['email'],
                'nama' => $user['nama'],
                'role' => $user['role'],
                'isLoggedIn' => true
            ];

            // Attach linked rumah sakit or produsen id to session when available
            try {
                if ($user['role'] === 'rs') {
                    $rsModel = new \App\Models\RumahSakitModel();
                    $rs = $rsModel->where('id_user', $user['id_user'])->first();
                    if (!$rs) {
                        // fallback: try matching by email on rumah_sakit record
                        $rs = $rsModel->where('email', $user['email'])->first();
                    }
                    if (!$rs) {
                        // fallback: try matching by name (login.nama == rumah_sakit.nama_rs)
                        $rs = $rsModel->where('nama_rs', $user['nama'])->first();
                    }
                    if ($rs) {
                        $sessionData['id_rs'] = $rs['id_rs'];
                    }
                } elseif ($user['role'] === 'bdrs') {
                    $prodModel = new \App\Models\ProdusenModel();
                    $prod = $prodModel->where('id_user', $user['id_user'])->first();
                    if ($prod) {
                        $sessionData['id_produsen'] = $prod['id_produsen'];
                    }
                }
            } catch (\Exception $e) {
                // ignore mapping errors
            }

            session()->set($sessionData);
            return redirect()->to('/dashboard');
        } else {
            session()->setFlashdata('error', 'Email atau password salah');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
