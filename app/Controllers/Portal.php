<?php

namespace App\Controllers;

use App\Models\LKModel;
use App\Models\AsetSeriesModel;
use App\Config\IPSRS;

class Portal extends BaseController
{
    public function lapor()
    {
        return view('pages/portal/lapor', [
            'aset' => (new AsetSeriesModel())->getAllWithParent(),
            'aset_id' => $this->request->getGet('aset_id')
        ]);
    }

    public function storeLapor()
    {
        $rules = [
            'pelapor'      => 'required',
            'unit_pelapor' => 'required',
            'keluhan'      => 'required',
            'lokasi'       => 'required',
        ];

        $v = $this->validateOrFail($rules, 'Mohon lengkapi seluruh data laporan.');
        if ($v !== true) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal, mohon periksa kembali isian Anda.');
        }

        try {
            $data = $this->request->getPost();
            $data['tanggal'] = date('Y-m-d');
            $data['jam_laporan'] = date('H:i');
            $data['status'] = IPSRS::STATUS_LK[0];
            $data['kode'] = 'PR';

            if (empty($data['id_aset_series'])) {
                $data['id_aset_series'] = null;
            } else {
                $asetModel = new AsetSeriesModel();
                $asset = $asetModel->getById($data['id_aset_series']);
                if ($asset) {
                    $data['nama_aset'] = $asset['nama_aset'];
                    // Update asset status
                    $asetModel->update($asset['id'], [
                        'status' => IPSRS::LK_TO_ASET_STATUS['Survei'],
                        'lokasi' => $data['lokasi']
                    ]);
                }
            }

            $model = new LKModel();
            $lk = $model->createWithRetry($data, fn() => $model->nextNoOrder(), 'no_order');

            return redirect()->to('/lapor/sukses?order=' . $lk['no_order']);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function sukses()
    {
        return view('pages/portal/sukses', [
            'order' => $this->request->getGet('order')
        ]);
    }
}
