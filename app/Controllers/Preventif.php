<?php

namespace App\Controllers;

use App\Config\IPSRS;
use App\Models\JadwalModel;
use App\Models\AsetModel;
use App\Models\LKModel;

class Preventif extends BaseController
{
    private JadwalModel $model;

    public function __construct()
    {
        $this->model = new JadwalModel();
    }

    public function index(): string
    {
        $jadwal  = $this->model->getAll();
        $aset    = (new AsetModel())->getAll();
        $filter  = $this->request->getGet('status') ?? '';
        $today   = date('Y-m-d');

        if ($filter) {
            if ($filter === 'Terlambat') {
                $jadwal = array_filter($jadwal, fn($j) => $j['tanggal'] < $today && $j['status'] !== IPSRS::STATUS_JADWAL[1]);
            } else {
                $jadwal = array_filter($jadwal, fn($j) => $j['status'] === $filter);
            }
        }

        return $this->render('pages/preventif/index', [
            'jadwal' => array_values($jadwal),
            'aset'   => $aset,
            'filter' => $filter,
            'today'  => $today,
        ]);
    }

    public function store()
    {
        $v = $this->validateOrFail([
            'teknisi' => 'required',
            'tanggal' => 'required',
            'jam'     => 'required',
        ], 'Mohon lengkapi teknisi, tanggal, dan jam jadwal.');
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'id_aset', 'aset', 'lokasi', 'teknisi', 'tanggal', 'jam',
            ]);
            $data['status'] = IPSRS::STATUS_JADWAL[0]; // Belum
            $this->model->create($data);
            return redirect()->to('/ipsrs/preventif')->with('success', 'Jadwal berhasil ditambahkan');
        } catch (\Throwable $e) {
            log_message('error', '[Preventif::store] ' . $e->getMessage());
            return redirect()->to('/ipsrs/preventif')->with('error', 'Gagal menambah jadwal: ' . $e->getMessage());
        }
    }

    public function selesai(string $id)
    {
        try {
            $this->model->markSelesai($id);
            return redirect()->to('/ipsrs/preventif')->with('success', 'Jadwal ditandai selesai');
        } catch (\Throwable $e) {
            log_message('error', '[Preventif::selesai] ' . $e->getMessage());
            return redirect()->to('/ipsrs/preventif')->with('error', 'Gagal menandai selesai: ' . $e->getMessage());
        }
    }

    public function delete(string $id)
    {
        try {
            $this->model->delete($id);
            return redirect()->to('/ipsrs/preventif')->with('success', 'Jadwal dihapus');
        } catch (\Throwable $e) {
            log_message('error', '[Preventif::delete] ' . $e->getMessage());
            return redirect()->to('/ipsrs/preventif')->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function lkp(string $id)
    {
        $jadwal = $this->model->getById($id);
        if (!$jadwal) { return redirect()->to('/ipsrs/preventif'); }

        $templateModel = new \App\Models\TemplateChecklistModel();
        $allTemplate   = $templateModel->getAll();
        $kategoriList  = array_values(array_unique(array_map(fn($t) => $t['kategori'] ?? '', $allTemplate)));

        return $this->render('pages/preventif/lkp', compact('jadwal', 'allTemplate', 'kategoriList'));
    }

    public function simpanLkp(string $id)
    {
        $jadwal = $this->model->getById($id);
        if (!$jadwal) { return redirect()->to('/ipsrs/preventif'); }

        $v = $this->validateOrFail([
            'kategori'          => 'required',
            'hasil_pemeriksaan' => 'required|in_list[' . implode(',', IPSRS::HASIL_PEMERIKSAAN) . ']',
            'nama_user_ttd'     => 'required',
        ], 'Lengkapi kategori alat, hasil pemeriksaan, dan nama pengguna.');
        if ($v !== true) return $v;

        try {
            $post     = $this->whitelist([
                'kategori', 'hasil_pemeriksaan', 'nama_user_ttd', 'catatan', 'items',
            ]);
            $lkpModel = new \App\Models\LkpModel();

            $header = $lkpModel->createWithRetry([
                'id_jadwal'           => $id,
                'id_aset'             => $jadwal['id_aset'] ?? null,
                'kategori'            => $post['kategori'],
                'tanggal_pemeriksaan' => date('Y-m-d'),
                'teknisi'             => $jadwal['teknisi'] ?? session('user_name') ?? 'Teknisi',
                'nama_user_ttd'       => $post['nama_user_ttd'],
                'hasil_pemeriksaan'   => $post['hasil_pemeriksaan'],
                'catatan'             => $post['catatan'] ?? null,
            ], fn() => $lkpModel->nextNoOrder(), 'no_order');

            $idLkp = $header['id'] ?? null;
            $items = $post['items'] ?? [];
            if ($idLkp && is_array($items)) {
                $rows = [];
                foreach ($items as $it) {
                    $jenis = $it['jenis'] ?? '';
                    $rows[] = [
                        'id_lkp'           => $idLkp,
                        'no_item'          => (int) ($it['no_item'] ?? 0),
                        'jenis_item'       => $jenis,
                        'nama_komponen'    => $it['komponen'] ?? null,
                        'hasil_inspeksi'   => $jenis === 'Inspeksi'   ? ($it['hasil'] ?? null) : null,
                        'hasil_service'    => $jenis === 'Service'    ? ($it['hasil'] ?? null) : null,
                        'nilai_pengukuran' => $jenis === 'Pengukuran' ? ($it['hasil'] ?? null) : null,
                        'satuan'           => $it['satuan'] ?? null,
                        'keterangan'       => $it['ket'] ?? null,
                    ];
                }
                $lkpModel->addDetail($rows);
            }

            $this->model->markSelesai($id);

            if ($post['hasil_pemeriksaan'] === IPSRS::HASIL_PEMERIKSAAN[1]) { // Perlu Perbaikan
                $lkModel = new LKModel();
                $newLK = $lkModel->createWithRetry([
                    'tanggal'     => date('Y-m-d'),
                    'jam_laporan' => date('H:i'),
                    'keluhan'     => !empty($post['catatan']) ? $post['catatan'] : ('Temuan PM: ' . ($jadwal['aset'] ?? 'aset')),
                    'kode'        => 'PR',
                    'pelapor'     => $jadwal['teknisi'] ?? session('user_name') ?? 'Teknisi',
                    'unit_pelapor'=> 'IPSRS',
                    'lokasi'      => $jadwal['lokasi'] ?? '',
                    'id_aset'     => $jadwal['id_aset'] ?? null,
                    'nama_aset'   => $jadwal['aset'] ?? null,
                    'teknisi'     => $jadwal['teknisi'] ?? null,
                    'status'      => IPSRS::STATUS_LK[0],
                ], fn() => $lkModel->nextNoOrder(), 'no_order');
                $newId = $newLK['id'] ?? null;
                if ($newId) {
                    return redirect()->to('/ipsrs/lk/' . $newId)->with('success', 'LKP disimpan. LK kuratif baru dibuat dari temuan PM.');
                }
            }

            return redirect()->to('/ipsrs/preventif')->with('success', 'LKP berhasil disimpan');
        } catch (\Throwable $e) {
            log_message('error', '[Preventif::simpanLkp] ' . $e->getMessage());
            return redirect()->to('/ipsrs/preventif')->with('error', 'Gagal menyimpan LKP: ' . $e->getMessage());
        }
    }

    public function lihatLkp(string $jadwalId)
    {
        $jadwal = $this->model->getById($jadwalId);
        if (!$jadwal) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Jadwal tidak ditemukan');
        }

        $lkpModel = new \App\Models\LkpModel();
        $lkps     = $lkpModel->getByJadwal($jadwalId);
        $lkp      = $lkps[0] ?? null; // LKP terbaru untuk jadwal ini
        $detail   = $lkp ? $lkpModel->getDetail((string) $lkp['id']) : [];

        return $this->render('pages/preventif/lkp_hasil', compact('jadwal', 'lkp', 'detail'));
    }
}
