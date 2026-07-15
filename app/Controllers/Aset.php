<?php

namespace App\Controllers;

use App\Config\IPSRS;
use App\Models\AsetModel;
use App\Models\LKModel;

class Aset extends BaseController
{
    private AsetModel $model;

    public function __construct()
    {
        $this->model = new AsetModel();
    }

    public function index(): string
    {
        $aset   = $this->model->getAll();
        $search = $this->request->getGet('q') ?? '';
        $jenis  = $this->request->getGet('jenis') ?? '';
        $status = $this->request->getGet('status') ?? '';

        if ($search) {
            $aset = array_filter($aset, fn($a) =>
                stripos($a['nama'], $search) !== false || stripos($a['id'], $search) !== false);
        }
        if ($jenis)  { $aset = array_filter($aset, fn($a) => $a['jenis'] === $jenis); }
        if ($status) { $aset = array_filter($aset, fn($a) => $a['status'] === $status); }

        return $this->render('pages/aset/index', [
            'aset'   => array_values($aset),
            'search' => $search,
            'jenis'  => $jenis,
            'status' => $status,
        ]);
    }

    public function show(string $id)
    {
        $aset    = $this->model->getById($id);
        if (!$aset) {
            return redirect()->to('/ipsrs/aset')->with('error', 'Aset tidak ditemukan');
        }

        if ($this->request->getGet('via') === 'qr') {
            return view('pages/aset/scan', compact('aset'));
        }

        $riwayat   = $this->model->getRiwayatLokasi($id);
        $riwayatLK = (new LKModel())->getByAset($id);
        $komponen  = (new \App\Models\KomponenAsetModel())->getByAset($id);
        $riwayatKanibal = (new \App\Models\RiwayatKanibalModel())->getByAset($id);

        return $this->render('pages/aset/show', compact('aset', 'riwayat', 'riwayatLK', 'komponen', 'riwayatKanibal'));
    }

    public function create(): string
    {
        return $this->render('pages/aset/form', [
            'aset'        => null,
            'isEdit'      => false,
            'kategoriAset'=> (new \App\Models\KategoriAsetModel())->getAll(),
        ]);
    }

    public function store()
    {
        $v = $this->validateOrFail([
            'nomor_aset' => 'required|is_unique[aset.nomor_aset]',
            'nama'     => 'required',
            'jenis'    => 'required|in_list[' . implode(',', IPSRS::JENIS_ASET) . ']',
            'kategori' => 'required',
            'lokasi'   => 'required',
            'gedung'   => 'required',
            'ruangan'  => 'required',
            'unit'     => 'required',
            'kondisi'  => 'required|in_list[' . implode(',', IPSRS::KONDISI_ASET) . ']',
        ], 'Mohon lengkapi seluruh data aset yang wajib diisi.');
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'nomor_aset', 'nama', 'jenis', 'kategori', 'lokasi', 'gedung', 'lantai', 'ruangan',
                'unit', 'kondisi', 'merk', 'model', 'no_seri', 'tahun',
                'kapasitas', 'keterangan', 'status',
            ]);

            $data['id'] = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $aset = $this->model->create($data);

            $this->model->insertRiwayatLokasi([
                'id_aset'       => $aset['id'],
                'nama_aset'     => $data['nama'],
                'lokasi_asal'   => null,
                'lokasi_tujuan' => $data['lokasi'],
                'tanggal'       => date('Y-m-d'),
                'alasan'        => 'Input awal aset',
                'petugas'       => session('user_name') ?? 'Admin',
                'catatan'       => null,
            ]);

            return redirect()->to('/ipsrs/aset')->with('success', 'Aset berhasil ditambahkan');
        } catch (\Throwable $e) {
            log_message('error', '[Aset::store] ' . $e->getMessage());
            return redirect()->to('/ipsrs/aset')->with('error', 'Gagal menyimpan aset: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $aset = $this->model->getById($id);
        if (!$aset) { return redirect()->to('/ipsrs/aset'); }
        return $this->render('pages/aset/form', [
            'aset'         => $aset,
            'isEdit'       => true,
            'kategoriAset' => (new \App\Models\KategoriAsetModel())->getAll(),
        ]);
    }

    public function update(string $id)
    {
        $v = $this->validateOrFail([
            'nomor_aset' => "required|is_unique[aset.nomor_aset,id,{$id}]",
            'nama'     => 'required',
            'jenis'    => 'required|in_list[' . implode(',', IPSRS::JENIS_ASET) . ']',
            'kategori' => 'required',
            'lokasi'   => 'required',
            'kondisi'  => 'required|in_list[' . implode(',', IPSRS::KONDISI_ASET) . ']',
        ], 'Mohon lengkapi data aset yang wajib diisi.');
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'nomor_aset', 'nama', 'jenis', 'kategori', 'lokasi', 'gedung', 'lantai', 'ruangan',
                'unit', 'kondisi', 'merk', 'model', 'no_seri', 'tahun',
                'kapasitas', 'keterangan', 'status',
            ]);
            $this->model->update($id, $data);
            return redirect()->to('/ipsrs/aset/' . $id)->with('success', 'Aset berhasil diperbarui');
        } catch (\Throwable $e) {
            log_message('error', '[Aset::update] ' . $e->getMessage());
            return redirect()->to('/ipsrs/aset/' . $id)->with('error', 'Gagal memperbarui aset: ' . $e->getMessage());
        }
    }

    public function mutasi(): string
    {
        $mutasiModel = new \App\Models\MutasiModel();
        $aset    = $this->model->getAll();
        $riwayat = $mutasiModel->getAll();
        $alasan  = $this->request->getGet('alasan') ?? '';
        if ($alasan) {
            $riwayat = array_filter($riwayat, fn($r) => $r['alasan'] === $alasan);
        }
        return $this->render('pages/aset/mutasi', [
            'aset'   => $aset,
            'riwayat'=> array_values($riwayat),
            'alasan' => $alasan,
        ]);
    }

    public function storeMutasi()
    {
        $v = $this->validateOrFail([
            'id_aset'       => 'required',
            'lokasi_tujuan' => 'required',
            'alasan'        => 'required',
            'petugas'       => 'required',
            'tanggal'       => 'required',
        ], 'Mohon lengkapi data mutasi yang wajib diisi.');
        if ($v !== true) return $v;

        try {
            $mutasiModel = new \App\Models\MutasiModel();
            $data = $this->whitelist(['id_aset', 'lokasi_tujuan', 'alasan', 'petugas', 'tanggal']);
            $aset = $this->model->getById($data['id_aset']);
            $data['nama_aset']   = $aset['nama'] ?? '';
            $data['lokasi_asal'] = $aset['lokasi'] ?? null;
            $mutasiModel->create($data);

            $post = $this->request->getPost();
            if (!empty($post['status_baru'])) {
                $this->model->update($data['id_aset'], ['status' => $post['status_baru']]);
            }

            return redirect()->to('/ipsrs/aset/mutasi')->with('success', 'Mutasi aset berhasil dicatat');
        } catch (\Throwable $e) {
            log_message('error', '[Aset::storeMutasi] ' . $e->getMessage());
            return redirect()->to('/ipsrs/aset/mutasi')->with('error', 'Gagal mencatat mutasi: ' . $e->getMessage());
        }
    }

    public function ping(string $id)
    {
        // 1. Parse JSON body securely
        $body = $this->request->getJSON(true) ?? [];
        $lat  = $body['lat'] ?? null;
        $lng  = $body['lng'] ?? null;

        // 2. Strict Input Validation for GPS Coordinates
        if ($lat === null || $lng === null || !is_numeric($lat) || !is_numeric($lng)) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false, 
                'msg' => 'Data koordinat GPS tidak valid atau tidak lengkap'
            ]);
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        // Validasi jangkauan GPS (-90 sd 90 untuk Latitude, -180 sd 180 untuk Longitude)
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false, 
                'msg' => 'Kordinat GPS di luar jangkauan logika pemetaan bumi'
            ]);
        }

        try {
            // 3. Validasi Keberadaan Aset di Database
            $aset = $this->model->getById($id);
            if (!$aset) {
                return $this->response->setStatusCode(404)->setJSON([
                    'ok' => false, 
                    'msg' => 'Aset tidak ditemukan di sistem, ID mungkin tidak valid'
                ]);
            }

            // 4. Rate Limiting Sederhana (Cegah spam ping berturut-turut dalam 10 detik)
            if (!empty($aset['last_seen_at'])) {
                $lastSeen = strtotime($aset['last_seen_at']);
                if (time() - $lastSeen < 10) {
                    return $this->response->setJSON([
                        'ok' => true, 
                        'msg' => 'Lokasi sudah diperbarui beberapa detik yang lalu (Spam Protection)'
                    ]);
                }
            }

            // 5. Eksekusi Update ke Database
            $this->model->update($id, [
                'last_seen_at'  => date('Y-m-d H:i:s'), // Format MySQL Timestamp yang presisi
                'last_seen_lat' => $lat,
                'last_seen_lng' => $lng,
                'last_seen_by'  => session('user_name') ?? 'Guest System (QR Scan)',
            ]);

            return $this->response->setJSON([
                'ok' => true,
                'msg' => 'Titik lokasi berhasil diamankan'
            ]);

        } catch (\Throwable $e) {
            log_message('critical', '[Aset::ping] Gagal memperbarui lokasi aset: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false, 
                'msg' => 'Terjadi kendala pada server saat menyimpan koordinat'
            ]);
        }
    }

    public function qr(string $id)
    {
        $aset = $this->model->getById($id);
        if (!$aset) { return redirect()->to('/ipsrs/aset'); }
        return view('pages/aset/qr', compact('aset'));
    }
}
