<?php

namespace App\Controllers;

use App\Models\RiwayatKanibalModel;
use App\Models\KomponenAsetModel;
use App\Models\AsetModel;
use App\Models\LKModel;

class Kanibal extends BaseController
{
    public function riwayat(): string
    {
        $model = new RiwayatKanibalModel();
        return $this->render('pages/kanibal/riwayat', [
            'riwayat' => $model->getAll(),
            'aset'    => (new AsetModel())->getAll(),
        ]);
    }

    public function store()
    {
        $post = $this->whitelist([
            'id_lk', 'no_order_lk', 'id_aset_donor', 'id_aset_penerima',
            'nama_komponen', 'kondisi_komponen', 'disetujui_oleh', 'keterangan',
        ]);

        if (empty($post['id_lk']) || empty($post['id_aset_donor']) || empty($post['id_aset_penerima']) || empty($post['nama_komponen'])) {
            return redirect()->back()->with('error', 'Data kanibal tidak lengkap');
        }

        $donor      = $post['id_aset_donor'];
        $penerima   = $post['id_aset_penerima'];
        $idLk       = $post['id_lk'];

        if ($donor === $penerima) {
            return redirect()->back()->with('error', 'Aset donor dan penerima tidak boleh sama');
        }

        try {
            $asetModel = new AsetModel();
            $donorData = $asetModel->getById($donor);

            if (!$donorData) {
                return redirect()->back()->with('error', 'Aset donor tidak ditemukan');
            }

            // 1. Create riwayat kanibal
            $kanibalModel = new RiwayatKanibalModel();
            $kanibalModel->create([
                'id'                => $this->generateUUID(),
                'no_order_lk'       => $post['no_order_lk'] ?? '',
                'id_aset_donor'     => $donor,
                'id_aset_penerima'  => $penerima,
                'nama_komponen'     => $post['nama_komponen'],
                'kondisi_komponen'  => $post['kondisi_komponen'] ?? 'Baik',
                'tanggal'           => date('Y-m-d'),
                'petugas'           => session('user_name') ?? 'Teknisi',
                'disetujui_oleh'    => $post['disetujui_oleh'] ?? null,
                'keterangan'        => $post['keterangan'] ?? null,
            ]);

            // 2. Add to detail_suku_cadang_lk with sumber = 'Kanibal'
            $lkModel = new LKModel();
            $lkModel->addSukuCadang([
                'id'            => $this->generateUUID(),
                'id_lk'         => $idLk,
                'id_barang'     => null,
                'sumber'        => 'Kanibal',
                'nama_barang'   => $post['nama_komponen'],
                'jumlah'        => 1,
                'satuan'        => 'pcs',
                'keterangan'    => 'Dari aset: ' . ($donorData['nama'] ?? $donorData['nomor_aset'] ?? $donor),
            ]);

            // 3. Update donor keterangan to show kanibal source
            $currentKet = $donorData['keterangan'] ?? '';
            $newKet     = trim($currentKet . "\n[Kanibal] Komponen \"{$post['nama_komponen']}\" dipindahkan ke penerima pada " . date('d M Y'));
            $asetModel->update($donor, ['keterangan' => $newKet]);

            // 4. Update komponen_aset donor — set kondisi to 'Tidak Ada'
            $komponenModel = new KomponenAsetModel();
            $komponenDonor = $komponenModel->getByAset($donor);
            foreach ($komponenDonor as $k) {
                if (stripos($k['nama_komponen'] ?? '', $post['nama_komponen']) !== false) {
                    $komponenModel->update($k['id'], ['kondisi' => 'Tidak Ada']);
                    break;
                }
            }

            return redirect()->to('/ipsrs/lk/' . $idLk)->with('success', 'Kanibal komponen berhasil dicatat');
        } catch (\Throwable $e) {
            log_message('error', '[Kanibal::store] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencatat kanibal: ' . $e->getMessage());
        }
    }

    private function generateUUID(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
