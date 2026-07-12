<?php

namespace App\Controllers;

use App\Models\PenggunaModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session('user_id')) {
            return redirect()->to('/ipsrs');
        }
        return view('pages/auth/login', ['error' => session()->getFlashdata('error')]);
    }

    public function doLogin()
    {
        $email    = trim($this->request->getPost('email') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (!$email || !$password) {
            return redirect()->to('/login')->with('error', 'Email dan kata sandi wajib diisi.');
        }

        $model  = new PenggunaModel();
        $user   = $model->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->to('/login')->with('error', 'Email atau kata sandi salah.');
        }

        $initial = strtoupper(mb_substr($user['nama_lengkap'], 0, 1));

        session()->regenerate();
        session()->set([
            'user_id'     => $user['id'],
            'user_email'  => $user['email'],
            'user_name'   => $user['nama_lengkap'],
            'user_role'   => $user['role'],
            'user_unit'   => $user['unit'],
            'user_initial'=> $initial,
        ]);

        $redirect = session('redirect_url') ?? '/ipsrs';
        session()->remove('redirect_url');
        
        return redirect()->to($redirect);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
