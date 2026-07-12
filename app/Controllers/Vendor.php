<?php

namespace App\Controllers;

use App\Models\VendorModel;

class Vendor extends BaseController
{
    private VendorModel $model;

    public function __construct()
    {
        $this->model = new VendorModel();
    }

    public function index(): string
    {
        $vendor = $this->model->getAll();
        $search = $this->request->getGet('q') ?? '';
        if ($search) {
            $vendor = array_filter($vendor, fn($v) =>
                stripos($v['nama_vendor'] ?? '', $search) !== false);
        }
        return $this->render('pages/vendor/index', [
            'vendor' => array_values($vendor),
            'search' => $search,
        ]);
    }

    public function store()
    {
        $v = $this->validateOrFail(['nama_vendor' => 'required']);
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist(['nama_vendor', 'kontak', 'alamat']);
            $this->model->create($data);
            return redirect()->to('/ipsrs/vendor')->with('success', 'Vendor berhasil ditambahkan');
        } catch (\Throwable $e) {
            log_message('error', '[Vendor::store] ' . $e->getMessage());
            return redirect()->to('/ipsrs/vendor')->with('error', 'Gagal menambah vendor: ' . $e->getMessage());
        }
    }

    public function update(string $id)
    {
        $v = $this->validateOrFail(['nama_vendor' => 'required']);
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist(['nama_vendor', 'kontak', 'alamat']);
            $this->model->update($id, $data);
            return redirect()->to('/ipsrs/vendor')->with('success', 'Vendor berhasil diperbarui');
        } catch (\Throwable $e) {
            log_message('error', '[Vendor::update] ' . $e->getMessage());
            return redirect()->to('/ipsrs/vendor')->with('error', 'Gagal memperbarui vendor: ' . $e->getMessage());
        }
    }
}
