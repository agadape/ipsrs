<?php

namespace App\Controllers;

use App\Models\PenggunaModel;

class Auth extends BaseController
{
    protected PenggunaModel $penggunaModel;

    public function __construct()
    {
        $this->penggunaModel = new PenggunaModel();
    }

    public function login()
    {
        if (session('user_id')) {
            return redirect()->to('/ipsrs');
        }

        return view('pages/auth/login');
    }

    public function register()
    {
        if (session('user_id')) {
            return redirect()->to('/ipsrs');
        }

        return view('pages/auth/register');
    }

    public function doRegister()
    {
        if (session('user_id')) {
            return redirect()->to('/ipsrs');
        }

        $nama  = $this->request->getPost('nama_lengkap');
        $email = $this->request->getPost('email');
        $pass  = $this->request->getPost('password');
        $unit  = $this->request->getPost('unit_kerja');

        if (!$nama || !$email || !$pass || !$unit) {
            return redirect()->back()->with('error', 'Semua field (Nama, Email, Password, Unit) wajib diisi.')->withInput();
        }

        // Cek email eksis
        $existing = $this->penggunaModel->where('email', $email)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Email sudah terdaftar.')->withInput();
        }

        $data = [
            'id'            => $this->penggunaModel->generateUUID(),
            'nama_lengkap'  => $nama,
            'email'         => $email,
            'password_hash' => password_hash($pass, PASSWORD_BCRYPT),
            'role'          => 'pelapor',
            'unit'          => $unit,
            'aktif'         => 1,
        ];

        try {
            $this->penggunaModel->create($data);
            return redirect()->to('/login')->with('success', 'Pendaftaran berhasil. Silakan login.');
        } catch (\Throwable $e) {
            log_message('error', '[Auth::doRegister] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Coba lagi.')->withInput();
        }
    }

    public function doLogin()
    {
        $email    = trim($this->request->getPost('email') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (!$email || !$password) {
            return redirect()->to('/login')->with('error', 'Email dan kata sandi wajib diisi.');
        }

        $user = $this->penggunaModel->findByEmail($email);

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
            'user_unit'   => $user['unit'] ?? '',
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
