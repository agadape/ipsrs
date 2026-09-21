<?php

namespace App\Controllers;

use App\Config\IPSRS;
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
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function doRegister()
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function doLogin()
    {
        $email    = trim($this->request->getPost('email') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (!$email || !$password) {
            return redirect()->to('/login')->with('error', 'Email dan kata sandi wajib diisi.');
        }

        $ip = $this->request->getIPAddress();
        $throttleBuckets = [
            [hash('sha256', 'account|' . strtolower($email)), IPSRS::LOGIN_MAX_ATTEMPTS],
            [hash('sha256', 'ip|' . $ip), IPSRS::LOGIN_IP_MAX_ATTEMPTS],
        ];
        $throttler = service('throttler');
        $allowed = true;
        $retryAfter = 0;
        foreach ($throttleBuckets as [$throttleKey, $capacity]) {
            if (!$throttler->check($throttleKey, $capacity, IPSRS::LOGIN_WINDOW_SECONDS)) {
                $allowed = false;
                $retryAfter = max($retryAfter, $throttler->getTokenTime());
            }
        }
        if (!$allowed) {
            log_message('warning', '[Auth] Login rate limit tercapai untuk IP ' . $ip);

            return redirect()->to('/login')
                ->setHeader('Retry-After', (string) $retryAfter)
                ->with('error', 'Terlalu banyak percobaan login. Coba lagi beberapa saat.');
        }

        $user = $this->penggunaModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->to('/login')->with('error', 'Email atau kata sandi salah.');
        }

        $role = strtolower(trim((string) ($user['role'] ?? '')));
        if (!in_array($role, ['admin', 'teknisi', 'pelapor'], true)) {
            return redirect()->to('/login')->with('error', 'Role akun tidak valid. Hubungi administrator.');
        }

        $initial = strtoupper(mb_substr($user['nama_lengkap'], 0, 1));

        session()->regenerate();
        session()->set([
            'user_id'     => $user['id'],
            'user_email'  => $user['email'],
            'user_name'   => $user['nama_lengkap'],
            'user_role'   => $role,
            'user_unit'   => $user['unit'] ?? '',
            'user_initial'=> $initial,
        ]);

        // A successful authentication resets the bucket for this account/IP.
        // Throttler::remove() is available on the framework service instance.
        foreach ($throttleBuckets as [$throttleKey]) {
            $throttler->remove($throttleKey);
        }

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
