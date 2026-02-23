<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 10;

        $result = $this->userModel->getUsers($search, $perPage);

        $data = [
            'title' => 'User Management',
            'page_title' => 'User Management',
            'users' => $result['users'],
            'pager' => $result['pager'],
            'search' => $search
        ];

        return view('users/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah User',
            'page_title' => 'Tambah User Baru'
        ];
        return view('users/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required',
            'email' => 'required|valid_email|is_unique[login.email]',
            'password' => 'required|min_length[3]',
            'password_confirmation' => 'required|matches[password]',
            'role' => 'required'
        ], [
            'password_confirmation' => [
                'required' => 'Konfirmasi password harus diisi',
                'matches' => 'Password dan konfirmasi password tidak cocok'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role')
        ];

        if ($this->userModel->save($data)) {
            session()->setFlashdata('success', 'User berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan user');
        }

        return redirect()->to('/users');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            session()->setFlashdata('error', 'User tidak ditemukan');
            return redirect()->to('/users');
        }

        $data = [
            'title' => 'Edit User',
            'page_title' => 'Edit User',
            'user' => $user
        ];
        return view('users/edit', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            session()->setFlashdata('error', 'User tidak ditemukan');
            return redirect()->to('/users');
        }

        $emailRules = $user['email'] === $this->request->getPost('email') ?
            'required|valid_email' : 'required|valid_email|is_unique[login.email]';

        $validationRules = [
            'nama' => 'required',
            'email' => $emailRules,
            'role' => 'required'
        ];

        // Jika password diisi, tambahkan validasi password
        if ($this->request->getPost('password')) {
            $validationRules['password'] = 'min_length[3]';
            $validationRules['password_confirmation'] = 'matches[password]';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($validationRules, [
            'password_confirmation' => [
                'matches' => 'Password dan konfirmasi password tidak cocok'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role')
        ];

        // Update password hanya jika diisi
        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        if ($this->userModel->update($id, $data)) {
            session()->setFlashdata('success', 'User berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate user');
        }

        return redirect()->to('/users');
    }

    public function delete($id)
    {
        if ($this->userModel->delete($id)) {
            session()->setFlashdata('success', 'User berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus user');
        }

        return redirect()->to('/users');
    }
}