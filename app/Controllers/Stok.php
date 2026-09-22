<?php

namespace App\Controllers;

use App\Models\StokModel;

class Stok extends BaseController
{
    private StokModel $model;

    public function __construct()
    {
        $this->model = new StokModel();
    }

    public function index(): string
    {
        $stok   = $this->model->getAll('nama');
        $search = $this->request->getGet('q') ?? '';
        $filter = $this->request->getGet('status') ?? '';

        if ($search) {
            $stok = array_filter($stok, fn($s) => stripos($s['nama'], $search) !== false);
        }
        if ($filter) {
            $stok = array_filter($stok, fn($s) => $this->stokStatus($s) === $filter);
        }

        $stok = array_map(fn($s) => array_merge($s, ['status' => $this->stokStatus($s)]), array_values($stok));

        return $this->render('pages/stok/index', ['stok' => $stok, 'search' => $search, 'filter' => $filter]);
    }

    public function tambahBarang()
    {
        $v = $this->validateOrFail([
            'nama'         => 'required',
            'minimum_stok' => 'permit_empty|is_natural',
        ], 'Nama barang wajib diisi dan stok minimum harus berupa angka.');
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist(['nama', 'kategori', 'satuan', 'minimum_stok', 'keterangan']);
            $data['stok_tersedia'] = 0;
            $this->model->createWithRetry(
                $data,
                fn() => $this->model->nextBarangId(),
                'no_barang'
            );
            return redirect()->to('/ipsrs/stok')->with('success', 'Barang berhasil ditambahkan');
        } catch (\Throwable $e) {
            log_message('error', '[Stok::tambahBarang] ' . $e->getMessage());
            return redirect()->to('/ipsrs/stok')->with('error', 'Gagal menambah barang: ' . $e->getMessage());
        }
    }

    public function catatMasuk()
    {
        return $this->catatTransaksi('Masuk', 'Barang masuk dicatat');
    }

    public function catatKeluar()
    {
        return $this->catatTransaksi('Keluar', 'Barang keluar dicatat');
    }

    public function riwayat(): string
    {
        $riwayat = $this->model->getRiwayat();
        $filter  = $this->request->getGet('jenis') ?? '';
        if ($filter) {
            $riwayat = array_filter($riwayat, fn($r) => $r['jenis'] === $filter);
        }
        return $this->render('pages/stok/riwayat', ['riwayat' => array_values($riwayat), 'filter' => $filter]);
    }

    // ── Private ───────────────────────────────────────────────────────────

    private function catatTransaksi(string $jenis, string $successMsg)
    {
        $rules = [
            'id_barang' => 'required',
            'jumlah'    => 'required|is_natural_no_zero',
            'tanggal'   => 'permit_empty|valid_date[Y-m-d]',
        ];
        $rules['no_dokumen'] = $jenis === 'Masuk'
            ? 'required|max_length[30]'
            : 'permit_empty|max_length[30]';

        $validation = $this->validateOrFail(
            $rules,
            $jenis === 'Masuk'
                ? 'Barang masuk wajib memiliki nomor dokumen atau faktur.'
                : 'Data transaksi stok tidak valid.'
        );
        if ($validation !== true) return $validation;

        $data   = $this->whitelist(['id_barang', 'jumlah', 'tanggal', 'no_dokumen', 'keterangan']);
        $barang = $this->model->getById($data['id_barang'] ?? '');
        $qty    = (int) ($data['jumlah'] ?? 0);
        $noDokumen = trim((string) ($data['no_dokumen'] ?? ''));

        if (!$barang) {
            return redirect()->to('/ipsrs/stok')->with('error', 'Barang tidak ditemukan');
        }
        if ($qty <= 0) {
            return redirect()->to('/ipsrs/stok')->with('error', 'Jumlah harus lebih dari 0');
        }
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $this->model->catatTransaksi([
                'id_barang'   => $barang['id'],
                'nama_barang' => $barang['nama'],
                'jenis'       => $jenis,
                'jumlah'      => $qty,
                'tanggal'     => $data['tanggal'] ?? date('Y-m-d'),
                'no_dokumen'  => $noDokumen !== '' ? $noDokumen : null,
                'keterangan'  => $data['keterangan'] ?? null,
                'petugas'     => session('user_name') ?? 'Admin',
            ]);
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi stok tidak dapat diselesaikan.');
            }
            $db->transCommit();
            return redirect()->to('/ipsrs/stok')->with('success', $successMsg);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[Stok::catatTransaksi] ' . $e->getMessage());
            return redirect()->to('/ipsrs/stok')->with('error', $e->getMessage());
        }
    }

    private function stokStatus(array $s): string
    {
        return \App\Libraries\Metrics::statusStok(
            (int) ($s['stok_tersedia'] ?? 0),
            (int) ($s['minimum_stok'] ?? 0)
        );
    }
}
