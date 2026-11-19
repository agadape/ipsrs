<?php

namespace App\Controllers;

use App\Models\LKModel;
use App\Models\AsetSeriesModel;
use App\Config\IPSRS;
use App\Libraries\AsetLifecycle;

class Portal extends BaseController
{
    public function lapor()
    {
        $aset = array_values(array_filter(
            (new AsetSeriesModel())->getAllWithParent(),
            static fn(array $row): bool => AsetLifecycle::canReceiveLk($row['status'] ?? null)
        ));

        return view('pages/portal/lapor', [
            'aset' => $aset,
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

        $v = $this->validateOrFail($rules, 'Laporan belum lengkap.');
        if ($v !== true) {
            return $v;
        }

        try {
            $data = $this->whitelist(['pelapor', 'unit_pelapor', 'keluhan', 'lokasi', 'id_aset_series']);
            $data['tanggal'] = date('Y-m-d');
            $data['jam_laporan'] = date('H:i');
            $data['status'] = IPSRS::STATUS_LK[0];
            $data['kode'] = 'PR';

            $asetModel = new AsetSeriesModel();
            $asset = null;
            if (empty($data['id_aset_series'])) {
                $data['id_aset_series'] = null;
            } else {
                $asset = $asetModel->getById($data['id_aset_series']);
                if (!$asset || !AsetLifecycle::canReceiveLk($asset['status'] ?? null)) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Aset yang dipilih tidak ditemukan atau sudah dihapuskan. Kosongkan pilihan aset bila ragu.');
                }
                $data['nama_aset'] = $asset['nama_aset'];
            }

            $db = \Config\Database::connect();
            $db->transBegin();
            $model = new LKModel();
            $lk = $model->createWithRetry($data, fn() => $model->nextNoOrder(), 'no_order');

            // Pelapor publik hanya menandai unit sedang ditangani; lokasi master aset tidak diubah dari teks bebas.
            if ($asset && AsetLifecycle::canSyncFromLk($asset['status'] ?? null)) {
                $asetModel->update($asset['id'], ['status' => IPSRS::LK_TO_ASET_STATUS['Survei']]);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                throw new \RuntimeException('Laporan tidak dapat disimpan.');
            }
            $db->transCommit();

            return redirect()->to('/lapor/sukses?order=' . $lk['no_order']);
        } catch (\Throwable $e) {
            if (isset($db) && $db->transDepth > 0) {
                $db->transRollback();
            }
            log_message('error', '[Portal::storeLapor] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Laporan gagal disimpan. Silakan coba lagi atau hubungi IPSRS.');
        }
    }

    public function sukses()
    {
        return view('pages/portal/sukses', [
            'order' => $this->request->getGet('order')
        ]);
    }
}
