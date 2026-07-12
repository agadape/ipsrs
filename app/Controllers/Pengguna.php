<?php

namespace App\Controllers;

use App\Models\PenggunaModel;

class Pengguna extends BaseController
{
    private PenggunaModel $penggunaModel;

    public function __construct()
    {
        $this->penggunaModel = new PenggunaModel();
    }

    public function index()
    {
        $search   = trim($this->request->getGet('q') ?? '');
        $allUsers = $this->penggunaModel->getAll();

        if ($search !== '') {
            $q = strtolower($search);
            $allUsers = array_values(array_filter($allUsers, fn($u) =>
                str_contains(strtolower($u['nama_lengkap'] ?? ''), $q) ||
                str_contains(strtolower($u['email'] ?? ''), $q) ||
                str_contains(strtolower($u['role'] ?? ''), $q)
            ));
        }

        return $this->render('pages/pengguna/index', [
            'pengguna' => $allUsers,
            'search'   => $search,
        ]);
    }

    public function tambah()
    {
        $v = $this->validateOrFail([
            'email'        => 'required|valid_email',
            'nama_lengkap' => 'required|max_length[100]',
            'role'         => 'required',
            'unit'         => 'required|max_length[100]',
        ]);
        if ($v !== true) return $v;
        
        $data = $this->whitelist(['email', 'nama_lengkap', 'role', 'unit']);

        $payload = [
            'email'        => $data['email'],
            'nama_lengkap' => $data['nama_lengkap'],
            'role'         => $data['role'],
            'unit'         => $data['unit'],
            'aktif'        => true,
            'created_at'   => date('c'),
        ];

        try {
            $this->penggunaModel->create($payload);
        } catch (\Throwable $e) {
            log_message('error', '[Pengguna::tambah] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengguna: ' . $e->getMessage());
        }

        return redirect()->to('/ipsrs/pengguna')->with('success', "Pengguna \"{$data['nama_lengkap']}\" berhasil ditambahkan.");
    }

    public function edit(string $id)
    {
        $v = $this->validateOrFail([
            'nama_lengkap' => 'required|max_length[100]',
            'role'         => 'required',
            'unit'         => 'required|max_length[100]',
            'aktif'        => 'required',
        ]);
        if ($v !== true) return $v;

        $data = $this->whitelist(['nama_lengkap', 'role', 'unit', 'aktif']);

        $payload = [
            'nama_lengkap' => $data['nama_lengkap'],
            'role'         => $data['role'],
            'unit'         => $data['unit'],
            'aktif'        => $data['aktif'] === '1' || $data['aktif'] === 'true',
        ];

        try {
            $this->penggunaModel->update($id, $payload);
        } catch (\Throwable $e) {
            log_message('error', '[Pengguna::edit] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengguna: ' . $e->getMessage());
        }

        return redirect()->to('/ipsrs/pengguna')->with('success', 'Data pengguna berhasil diperbarui.');
    }
}
