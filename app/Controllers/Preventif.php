<?php

namespace App\Controllers;

use App\Config\IPSRS;
use App\Libraries\AccessPolicy;
use App\Libraries\LkpChecklist;
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
        $role    = AccessPolicy::role(session('user_role'));
        if ($role === 'pelapor') {
            return redirect()->to('/ipsrs')->with('error', 'Akses ditolak.');
        }
        if ($role === 'teknisi') {
            $jadwal = array_filter($jadwal, fn($row) => AccessPolicy::canAccessPreventive($row, $role, session('user_name')));
        }
        $aset    = (new \App\Models\AsetSeriesModel())->getAllWithParent();
        $users   = (new \App\Models\PenggunaModel())->getByRole('teknisi');
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
            'users'  => $users,
            'filter' => $filter,
            'today'  => $today,
        ]);
    }

    public function store()
    {
        if (!AccessPolicy::hasRole(session('user_role'), ['admin'])) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Akses ditolak.');
        }
        $v = $this->validateOrFail([
            'teknisi' => 'required',
            'tanggal' => 'required',
            'jam'     => 'required',
        ], 'Mohon lengkapi teknisi, tanggal, dan jam jadwal.');
        if ($v !== true) return $v;
        
        $tanggalJam = $this->request->getPost('tanggal') . ' ' . $this->request->getPost('jam');
        if (strtotime($tanggalJam) < time()) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Tanggal dan jam jadwal tidak boleh di masa lalu.');
        }

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
        if (!AccessPolicy::hasRole(session('user_role'), ['admin'])) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Akses ditolak.');
        }
        $jadwal = $this->model->getById($id);
        if (!$jadwal) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Jadwal tidak ditemukan.');
        }
        if (!(new \App\Models\LkpModel())->getLatestByJadwal($id)) {
            return redirect()->to('/ipsrs/preventif/lkp/' . $id)->with('error', 'Jadwal hanya dapat diselesaikan melalui LKP.');
        }

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
        if (!AccessPolicy::hasRole(session('user_role'), ['admin'])) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Akses ditolak.');
        }
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
        if (!AccessPolicy::canAccessPreventive($jadwal, session('user_role'), session('user_name'))) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Akses ditolak.');
        }

        if ((new \App\Models\LkpModel())->getLatestByJadwal($id)) {
            return redirect()->to('/ipsrs/preventif/lkp-hasil/' . $id)
                ->with('success', 'LKP untuk jadwal ini sudah tersimpan.');
        }

        $templateModel = new \App\Models\TemplateChecklistModel();
        $allTemplate   = $templateModel->getAll();
        $kategoriList  = array_values(array_unique(array_map(fn($t) => $t['kategori'] ?? '', $allTemplate)));

        return $this->render('pages/preventif/lkp', compact('jadwal', 'allTemplate', 'kategoriList'));
    }

    public function simpanLkp(string $id)
    {
        $jadwal = $this->model->getById($id);
        if (!$jadwal) { return redirect()->to('/ipsrs/preventif'); }
        if (!AccessPolicy::canAccessPreventive($jadwal, session('user_role'), session('user_name'))) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Akses ditolak.');
        }

        $v = $this->validateOrFail([
            'kategori'          => 'required',
            'hasil_pemeriksaan' => 'required|in_list[' . implode(',', IPSRS::HASIL_PEMERIKSAAN) . ']',
            'lokasi_sesuai'     => 'required|in_list[Sesuai,Tidak Sesuai]',
            'nama_user_ttd'     => 'required|max_length[100]',
        ], 'Lengkapi kategori alat dan hasil pemeriksaan.');
        if ($v !== true) return $v;

        $post = $this->whitelist([
            'kategori', 'hasil_pemeriksaan', 'lokasi_sesuai', 'nama_user_ttd', 'catatan', 'items',
        ]);
        $items = $post['items'] ?? [];
        $checklistError = is_array($items) ? LkpChecklist::validate($items) : 'Format checklist tidak valid.';
        if ($checklistError) {
            return redirect()->back()->withInput()->with('error', $checklistError);
        }

        $lkpModel = new \App\Models\LkpModel();
        if ($lkpModel->getLatestByJadwal($id)) {
            return redirect()->to('/ipsrs/preventif/lkp-hasil/' . $id)
                ->with('error', 'LKP untuk jadwal ini sudah tersimpan; submit ulang tidak membuat data baru.');
        }

        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            if (!$this->model->claimForCompletion($id)) {
                $db->transRollback();
                return redirect()->to('/ipsrs/preventif/lkp-hasil/' . $id)
                    ->with('error', 'Jadwal sedang atau sudah diproses. Tidak ada LKP tambahan yang dibuat.');
            }

            $idAsetSeries = !empty($jadwal['id_aset']) ? $jadwal['id_aset'] : null;
            if ($idAsetSeries) {
                // Pastikan id ini benar-benar ada di tabel aset_series (bukan sisa data lama dari tabel aset parent)
                $asetSeriesModel = new \App\Models\AsetSeriesModel();
                if (!$asetSeriesModel->getById($idAsetSeries)) {
                    $idAsetSeries = null;
                }
            }

            $header = $lkpModel->createWithRetry([
                'id_jadwal'           => $id,
                'id_aset_series'      => $idAsetSeries,
                'kategori'            => $post['kategori'],
                'tanggal_pemeriksaan' => date('Y-m-d'),
                'teknisi'             => $jadwal['teknisi'] ?? session('user_name') ?? 'Teknisi',
                'nama_user_ttd'       => trim((string) $post['nama_user_ttd']),
                'hasil_pemeriksaan'   => $post['hasil_pemeriksaan'],
                'catatan'             => $post['catatan'] ?? '',
            ], fn() => $lkpModel->nextNoOrder(), 'no_order');

            $idLkp = $header['id'] ?? null;
            if (!$idLkp) {
                throw new \RuntimeException('Header LKP gagal dibuat.');
            }

            $detailItems = $items;
            $detailItems[] = [
                'no_item' => count($items) + 1,
                'jenis' => 'Teks',
                'komponen' => 'Kesesuaian Lokasi Aset',
                'hasil' => $post['lokasi_sesuai'],
                'ket' => '',
            ];
            $lkpModel->addDetail(LkpChecklist::toRows(
                $idLkp,
                $detailItems,
                fn(): string => $lkpModel->generateUUID()
            ));

            // Auto-save kategori baru atau item baru ke template.
            $templateModel = new \App\Models\TemplateChecklistModel();
            $existingTemplates = $templateModel->getByKategori($post['kategori']);
            $existingItems = array_map(function($t) {
                return strtolower(trim($t['nama_komponen'] ?? '') . '|' . ($t['jenis_item'] ?? ''));
            }, $existingTemplates);

            foreach ($items as $it) {
                $jenis = $it['jenis'] ?? '';
                $komp  = $it['komponen'] ?? '';
                $key   = strtolower(trim($komp) . '|' . $jenis);

                if (!empty($komp) && !in_array($key, $existingItems, true)) {
                    $templateModel->create([
                        'kategori'      => $post['kategori'],
                        'no_item'       => (int) ($it['no_item'] ?? 0),
                        'jenis_item'    => $jenis,
                        'nama_komponen' => $komp,
                        'satuan'        => $jenis === 'Pengukuran' ? ($it['satuan'] ?? null) : null,
                    ]);
                    $existingItems[] = $key;
                }
            }

            $this->model->markSelesai($id);

            if ($post['hasil_pemeriksaan'] === IPSRS::HASIL_PEMERIKSAAN[1]) { // Perlu Perbaikan
                $lkModel = new LKModel();
                $sourceLkp = $header['no_order'] ?? $idLkp;
                $newLK = $lkModel->createWithRetry([
                    'tanggal'     => date('Y-m-d'),
                    'jam_laporan' => date('H:i'),
                    'keluhan'     => 'Temuan PM [' . $sourceLkp . ']: ' . (!empty($post['catatan']) ? $post['catatan'] : ($jadwal['aset'] ?? 'aset')),
                    'kode'        => 'PR',
                    'pelapor'     => $jadwal['teknisi'] ?? session('user_name') ?? 'Teknisi',
                    'unit_pelapor'=> 'IPSRS',
                    'lokasi'      => $jadwal['lokasi'] ?? '',
                    'id_aset_series' => $idAsetSeries,
                    'nama_aset'   => $jadwal['aset'] ?? null,
                    'teknisi'     => $jadwal['teknisi'] ?? null,
                    'status'      => IPSRS::STATUS_LK[0],
                ], fn() => $lkModel->nextNoOrder(), 'no_order');
                $newId = $newLK['id'] ?? null;
                if (!$newId) {
                    throw new \RuntimeException('LK kuratif dari temuan PM gagal dibuat.');
                }
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi LKP tidak dapat diselesaikan.');
            }
            $db->transCommit();

            if (!empty($newId)) {
                return redirect()->to('/ipsrs/lk/' . $newId)->with('success', 'LKP disimpan. LK kuratif baru dibuat dari temuan PM.');
            }

            return redirect()->to('/ipsrs/preventif')->with('success', 'LKP berhasil disimpan');
        } catch (\Throwable $e) {
            $db->transRollback();
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
        if (!AccessPolicy::canAccessPreventive($jadwal, session('user_role'), session('user_name'))) {
            return redirect()->to('/ipsrs/preventif')->with('error', 'Akses ditolak.');
        }

        $lkpModel = new \App\Models\LkpModel();
        $lkps     = $lkpModel->getByJadwal($jadwalId);
        $lkp      = $lkps[0] ?? null; // LKP terbaru untuk jadwal ini
        $detail   = $lkp ? $lkpModel->getDetail((string) $lkp['id']) : [];

        return $this->render('pages/preventif/lkp_hasil', compact('jadwal', 'lkp', 'detail'));
    }
}





