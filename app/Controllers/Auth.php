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
                'iduser' => $user['iduser'],
                'email' => $user['email'],
                'isLoggedIn' => true
            ];
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