<?php

namespace App\Filters;

use App\Libraries\AccessPolicy;
use App\Models\PenggunaModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session('user_id')) {
            // Save the URL they were trying to access so we can redirect them back after login
            if ($request->getMethod() === 'get' && !str_contains(current_url(), '/login') && !str_contains(current_url(), '/logout')) {
                session()->set('redirect_url', current_url());
            }
            return redirect()->to('/login');
        }

        try {
            $user = (new PenggunaModel())->getById((string) session('user_id'));
        } catch (\Throwable $e) {
            log_message('error', '[AuthFilter] Gagal memverifikasi session user: ' . $e->getMessage());
            return redirect()->to('/login')->with('error', 'Sesi tidak dapat diverifikasi. Silakan login kembali.');
        }

        $role = AccessPolicy::role($user['role'] ?? null);
        if (!$user || empty($user['aktif']) || !in_array($role, ['admin', 'teknisi', 'pelapor'], true)) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Sesi tidak lagi aktif. Silakan login kembali.');
        }

        session()->set([
            'user_role' => $role,
            'user_name' => $user['nama_lengkap'] ?? session('user_name'),
            'user_unit' => $user['unit'] ?? session('user_unit'),
        ]);

        if (!$this->canAccessInternalRoute($request, $role)) {
            return redirect()->to('/ipsrs')->with('error', 'Akses ditolak.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}

    private function canAccessInternalRoute(RequestInterface $request, string $role): bool
    {
        $segments = $request->getUri()->getSegments();
        $resource = $segments[1] ?? '';
        $method   = strtoupper($request->getMethod());

        if ($role === 'admin' || $resource === '') {
            return true;
        }
        if ($role === 'pelapor') {
            return $resource === 'lk';
        }
        if ($role !== 'teknisi') {
            return false;
        }
        if (in_array($resource, ['pengguna', 'vendor', 'kategori-aset', 'kode-kerusakan', 'kanibal', 'penghapusan', 'laporan', 'peminjaman'], true)) {
            return false;
        }
        if (in_array($resource, ['aset', 'stok'], true)) {
            return $method === 'GET';
        }
        if ($resource === 'preventif' && $method !== 'GET') {
            return ($segments[2] ?? '') === 'lkp';
        }

        return in_array($resource, ['lk', 'preventif'], true);
    }
}
