<?php

namespace App\Controllers;

use App\Models\KodeKerusakanModel;

class KodeKerusakan extends BaseController
{
    private KodeKerusakanModel $kodeModel;

    public function __construct()
    {
        $this->kodeModel = new KodeKerusakanModel();
    }

    public function index()
    {
        $search = trim($this->request->getGet('q') ?? '');
        $all    = $this->kodeModel->getAll();

        if ($search !== '') {
            $q = strtolower($search);
            $all = array_values(array_filter($all, fn($k) =>
                str_contains(strtolower($k['kode'] ?? ''), $q) ||
                str_contains(strtolower($k['nama'] ?? ''), $q)
            ));
        }

        return $this->render('pages/kode_kerusakan/index', [
            'kodeKerusakan' => $all,
            'search'        => $search,
        ]);
    }

    public function tambah()
    {
        $data = $this->validateOrFail([
            'kode' => 'required|max_length[10]',
            'nama' => 'required|max_length[100]',
        ]);

        $payload = [
            'kode'       => strtoupper(trim($data['kode'])),
            'nama'       => $data['nama'],
            'created_at' => date('c'),
        ];

        try {
            $this->kodeModel->create($payload);
        } catch (\Throwable $e) {
            log_message('error', '[KodeKerusakan::tambah] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan kode kerusakan: ' . $e->getMessage());
        }

        return redirect()->to('/ipsrs/kode-kerusakan')->with('success', "Kode \"{$payload['kode']}\" berhasil ditambahkan.");
    }

    public function edit(string $id)
    {
        $data = $this->validateOrFail([
            'kode' => 'required|max_length[10]',
            'nama' => 'required|max_length[100]',
        ]);

        $payload = [
            'kode' => strtoupper(trim($data['kode'])),
            'nama' => $data['nama'],
        ];

        try {
            $this->kodeModel->update($id, $payload);
        } catch (\Throwable $e) {
            log_message('error', '[KodeKerusakan::edit] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kode kerusakan: ' . $e->getMessage());
        }

        return redirect()->to('/ipsrs/kode-kerusakan')->with('success', 'Kode kerusakan berhasil diperbarui.');
    }
}
