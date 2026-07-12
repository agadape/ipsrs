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
        $data = $this->validateOrFail([
            'nama_kategori' => 'required|max_length[100]',
            'deskripsi'     => 'max_length[255]',
        ]);

        $payload = [
            'nama_kategori' => $data['nama_kategori'],
            'deskripsi'     => $data['deskripsi'] ?? '',
            'created_at'    => date('c'),
        ];

        try {
            $this->kategoriModel->create($payload);
        } catch (\Throwable $e) {
            log_message('error', '[KategoriAset::tambah] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan kategori: ' . $e->getMessage());
        }

        return redirect()->to('/ipsrs/kategori-aset')->with('success', "Kategori \"{$data['nama_kategori']}\" berhasil ditambahkan.");
    }

    public function edit(string $id)
    {
        $data = $this->validateOrFail([
            'nama_kategori' => 'required|max_length[100]',
            'deskripsi'     => 'max_length[255]',
        ]);

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
}
