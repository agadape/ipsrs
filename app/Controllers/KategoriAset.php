<?php

namespace App\Controllers;

use App\Models\KategoriAsetModel;

class KategoriAset extends BaseController
{
    private KategoriAsetModel $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriAsetModel();
    }

    public function index()
    {
        $search = trim($this->request->getGet('q') ?? '');
        $all    = $this->kategoriModel->getAll();

        if ($search !== '') {
            $q = strtolower($search);
            $all = array_values(array_filter($all, fn($k) =>
                str_contains(strtolower($k['nama_kategori'] ?? ''), $q) ||
                str_contains(strtolower($k['deskripsi'] ?? ''), $q)
            ));
        }

        return $this->render('pages/kategori_aset/index', [
            'kategori' => $all,
            'search'   => $search,
        ]);
    }

    public function tambah()
    {
        $res = $this->validateOrFail([
            'nama_kategori' => 'required|max_length[100]',
            'deskripsi'     => 'max_length[255]',
        ]);
        if ($res !== true) return $res;

        $data = $this->whitelist(['nama_kategori', 'deskripsi']);

        $payload = [
            'nama_kategori' => $data['nama_kategori'],
            'deskripsi'     => $data['deskripsi'] ?? '',
            'created_at'    => date('c'),
        ];

        try {
            $this->kategoriModel->create($payload);
        } catch (\Throwable $e) {
            log_message('error', '[KategoriAset::tambah] ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan kategori: ' . $e->getMessage());
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'kategori' => $data['nama_kategori']]);
        }

        return redirect()->to('/ipsrs/kategori-aset')->with('success', "Kategori \"{$data['nama_kategori']}\" berhasil ditambahkan.");
    }

    public function edit(string $id)
    {
        $res = $this->validateOrFail([
            'nama_kategori' => 'required|max_length[100]',
            'deskripsi'     => 'max_length[255]',
        ]);
        if ($res !== true) return $res;

        $data = $this->whitelist(['nama_kategori', 'deskripsi']);

        $payload = [
            'nama_kategori' => $data['nama_kategori'],
            'deskripsi'     => $data['deskripsi'] ?? '',
        ];

        try {
            $this->kategoriModel->update($id, $payload);
        } catch (\Throwable $e) {
            log_message('error', '[KategoriAset::edit] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }

        return redirect()->to('/ipsrs/kategori-aset')->with('success', 'Kategori berhasil diperbarui.');
    }
    public function delete(string $id)
    {
        try {
            $this->kategoriModel->delete($id);
        } catch (\Throwable $e) {
            log_message('error', '[KategoriAset::delete] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus kategori. Pastikan kategori tidak digunakan di data aset. Detail: ' . $e->getMessage());
        }

        return redirect()->to('/ipsrs/kategori-aset')->with('success', 'Kategori berhasil dihapus.');
    }
}
