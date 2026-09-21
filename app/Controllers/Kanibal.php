<?php

namespace App\Controllers;

use App\Libraries\AccessPolicy;
use App\Libraries\KanibalTransfer;
use App\Models\RiwayatKanibalModel;

class Kanibal extends BaseController
{
    public function riwayat(): string
    {
        $model = new RiwayatKanibalModel();
        $asetAll = (new \App\Models\AsetSeriesModel())->getAllWithParent();
        
        // New lifecycle uses canonical `Rusak Berat`; retain legacy `Kanibal`
        // only for reading historical rows until schema/data reconciliation.
        $asetKanibal = array_filter($asetAll, fn($a) => in_array($a['status'] ?? '', ['Rusak Berat', 'Kanibal'], true));

        return $this->render('pages/kanibal/riwayat', [
            'riwayat'      => $model->getAll(),
            'aset'         => $asetAll,
            'aset_kanibal' => array_values($asetKanibal),
        ]);
    }

    public function store()
    {
        if (!AccessPolicy::hasRole(session('user_role'), ['admin'])) {
            return redirect()->to('/ipsrs/lk')->with('error', 'Kanibalisasi hanya dapat dicatat oleh Admin.');
        }

        $post = $this->whitelist([
            'id_lk', 'id_aset_donor', 'id_aset_penerima',
            'nama_komponen', 'kondisi_komponen', 'keterangan',
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
            (new KanibalTransfer())->record([
                'id_lk' => $idLk, 'id_series_donor' => $donor, 'id_series_penerima' => $penerima,
                'nama_komponen' => $post['nama_komponen'], 'kondisi_komponen' => $post['kondisi_komponen'] ?? 'Baik',
                'petugas' => session('user_name') ?? 'Administrator',
                'disetujui_oleh' => session('user_name') ?? 'Administrator',
                'keterangan' => $post['keterangan'] ?? null,
                'id_riwayat' => $this->generateUUID(), 'id_detail' => $this->generateUUID(),
                'id_komponen_penerima' => $this->generateUUID(),
            ]);

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



