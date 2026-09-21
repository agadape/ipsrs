<?php

namespace App\Controllers;

use App\Config\IPSRS;
use App\Libraries\AsetLifecycle;
use App\Libraries\BaDocumentStorage;
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
        if ($status) { 
            $db = \Config\Database::connect();
            
            $seriesWithStatus = $db->table('aset_series')
                                   ->select('id_aset')
                                   ->where('status', $status)
                                   ->groupBy('id_aset')
                                   ->get()
                                   ->getResultArray();
            $allowedAsetIds = array_column($seriesWithStatus, 'id_aset');
            
            $aset = array_filter($aset, fn($a) => in_array($a['id'], $allowedAsetIds)); 
        }

        return $this->render('pages/aset/index', [
            'aset'   => array_values($aset),
            'search' => $search,
            'jenis'  => $jenis,
            'status' => $status,
        ]);
    }

    public function show(string $id)
    {
        $aset = $this->model->getById($id);
        if (!$aset) {
            return redirect()->to('/ipsrs/aset')->with('error', 'Aset tidak ditemukan');
        }

        $seriesModel = new \App\Models\AsetSeriesModel();
        $series = $seriesModel->getByParent($id);

        return $this->render('pages/aset/show', compact('aset', 'series'));
    }

    public function createSeries(string $idParent)
    {
        $aset = $this->model->getById($idParent);
        if (!$aset) return redirect()->to('/ipsrs/aset');
        
        $masterLokasi = (new \App\Models\MasterLokasiModel())->getAll('nama_ruangan');

        return $this->render('pages/aset/form_series', [
            'aset'         => $aset,
            'masterLokasi' => $masterLokasi,
            'series'       => [],
            'isEdit'       => false
        ]);
    }

    public function storeSeries(string $idParent)
    {
        $v = $this->validateOrFail([
            'nomor_aset' => 'required|is_unique[aset_series.nomor_aset]',
            'id_lokasi'  => 'required',
            'kondisi'    => 'required|in_list[' . implode(',', IPSRS::KONDISI_ASET) . ']',
        ]);
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'nomor_aset', 'no_seri', 'id_lokasi', 'kondisi',
                'merk', 'model', 'kapasitas', 'tahun_perolehan'
            ]);
            $data['status']  = 'Tersedia';
            $data['id_aset'] = $idParent;
            $data['lokasi']  = '-'; // Stub for old column if it's still NOT NULL
            $data['gedung']  = '-'; // Stub
            $data['ruangan'] = '-'; // Stub
            $data['unit']    = '-'; // Stub
            $data['id'] = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            
            (new \App\Models\AsetSeriesModel())->create($data);
            return redirect()->to('/ipsrs/aset/' . $idParent)->with('success', 'Unit/Series berhasil ditambahkan');
        } catch (\Throwable $e) {
            log_message('error', '[Aset::storeSeries] ' . $e->getMessage());
            return redirect()->to('/ipsrs/aset/' . $idParent)->with('error', 'Gagal menyimpan unit: ' . $e->getMessage());
        }
    }

    public function scan(string $idSeries)
    {
        $seriesModel = new \App\Models\AsetSeriesModel();
        $series = $seriesModel->getById($idSeries);
        if (!$series) return redirect()->to('/ipsrs/aset')->with('error', 'Series tidak ditemukan');
        
        $aset = $this->model->getById($series['id_aset']);
        return view('pages/aset/scan', compact('aset', 'series'));
    }

    public function showSeries(string $idSeries)
    {
        $seriesModel = new \App\Models\AsetSeriesModel();
        $series = $seriesModel->getById($idSeries);
        if (!$series) return redirect()->to('/ipsrs/aset')->with('error', 'Series tidak ditemukan');
        
        $aset = $this->model->getById($series['id_aset']);

        $riwayat   = (new \App\Models\MutasiModel())->getByAset($idSeries);
        $riwayatLK = (new \App\Models\LKModel())->getByAset($idSeries);
        $komponen  = (new \App\Models\KomponenAsetModel())->getByAset($idSeries);
        $riwayatKanibal = (new \App\Models\RiwayatKanibalModel())->getByAset($idSeries);
        
        $peminjamanAktif = null;
        if ($series['status'] === 'Dipinjam') {
            $peminjamanAktif = (new \App\Models\PeminjamanAsetModel())->getActiveByAset($idSeries);
        }

        $dataPenghapusan = null;
        if ($series['status'] === 'Dihapuskan') {
            $dataPenghapusan = (new \App\Models\PenghapusanAsetModel())->getByAset($idSeries);
        }

        return $this->render('pages/aset/show_series', compact('aset', 'series', 'riwayat', 'riwayatLK', 'komponen', 'riwayatKanibal', 'peminjamanAktif', 'dataPenghapusan'));
    }

    /** Structured component list for the authenticated kanibal form. */
    public function components(string $idSeries)
    {
        $series = (new \App\Models\AsetSeriesModel())->getById($idSeries);
        if (!$series) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Aset tidak ditemukan.']);
        }

        $components = (new \App\Models\KomponenAsetModel())->getByAset($idSeries);
        return $this->response->setJSON(array_map(static fn(array $component): array => [
            'nama_komponen' => $component['nama_komponen'] ?? '',
            'kondisi'       => $component['kondisi'] ?? '',
        ], $components));
    }

    public function editSeries(string $idSeries)
    {
        $seriesModel = new \App\Models\AsetSeriesModel();
        $series = $seriesModel->getById($idSeries);
        if (!$series) return redirect()->to('/ipsrs/aset');
        if (!AsetLifecycle::canRelocate($series['status'] ?? null)) {
            return redirect()->to('/ipsrs/aset/series/' . $idSeries)->with('error', 'Unit hanya dapat diedit saat berstatus Tersedia.');
        }
        
        $aset = $this->model->getById($series['id_aset']);
        $masterLokasi = (new \App\Models\MasterLokasiModel())->getAll('nama_ruangan');

        return $this->render('pages/aset/form_series', [
            'aset'         => $aset,
            'series'       => $series,
            'masterLokasi' => $masterLokasi,
            'isEdit'       => true
        ]);
    }

    public function updateSeries(string $idSeries)
    {
        $series = (new \App\Models\AsetSeriesModel())->getById($idSeries);
        if (!$series || !AsetLifecycle::canRelocate($series['status'] ?? null)) {
            return redirect()->to('/ipsrs/aset/series/' . $idSeries)->with('error', 'Unit hanya dapat diedit atau dipindahkan saat berstatus Tersedia.');
        }
        $v = $this->validateOrFail([
            'nomor_aset' => "required|is_unique[aset_series.nomor_aset,id,{$idSeries}]",
            'id_lokasi'  => 'required',
            'kondisi'    => 'required|in_list[' . implode(',', IPSRS::KONDISI_ASET) . ']',
        ]);
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'nomor_aset', 'no_seri', 'id_lokasi', 'kondisi',
                'merk', 'model', 'kapasitas', 'tahun_perolehan'
            ]);
            (new \App\Models\AsetSeriesModel())->update($idSeries, $data);
            return redirect()->to('/ipsrs/aset/series/' . $idSeries)->with('success', 'Unit berhasil diperbarui');
        } catch (\Throwable $e) {
            return redirect()->to('/ipsrs/aset/series/' . $idSeries)->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function create(): string
    {
        return $this->render('pages/aset/form', [
            'aset'        => null,
            'series'       => [],
            'isEdit'       => false,
            'kategoriAset'=> (new \App\Models\KategoriAsetModel())->getAll(),
        ]);
    }

    public function store()
    {
        $v = $this->validateOrFail([
            
            'nama'     => 'required',
            'jenis'    => 'required|in_list[' . implode(',', IPSRS::JENIS_ASET) . ']',
            'kategori' => 'required',
            
        ], 'Mohon lengkapi seluruh data aset yang wajib diisi.');
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'nama', 'jenis', 'kategori', 'keterangan',
            ]);

            $data['id'] = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $aset = $this->model->create($data);

            return redirect()->to('/ipsrs/aset')->with('success', 'Katalog Aset berhasil ditambahkan');
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
            'nama'     => 'required',
            'jenis'    => 'required|in_list[' . implode(',', IPSRS::JENIS_ASET) . ']',
            'kategori' => 'required',
            
        ], 'Mohon lengkapi data aset yang wajib diisi.');
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'nama', 'jenis', 'kategori', 'keterangan',
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
        $aset    = (new \App\Models\AsetSeriesModel())->getAllWithParent();
        $riwayat = $mutasiModel->getAll();
        $users   = (new \App\Models\PenggunaModel())->getAll();
        $masterLokasi = (new \App\Models\MasterLokasiModel())->getAll('nama_ruangan');
        
        $alasan  = $this->request->getGet('alasan') ?? '';
        if ($alasan) {
            $riwayat = array_filter($riwayat, fn($r) => $r['alasan'] === $alasan);
        }
        return $this->render('pages/aset/mutasi', [
            'aset'         => $aset,
            'masterLokasi' => $masterLokasi,
            'riwayat'      => array_values($riwayat),
            'alasan'       => $alasan,
            'users'        => array_filter($users, fn($u) => in_array($u['role'], ['Admin', 'Teknisi']))
        ]);
    }

    public function storeMutasi()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/ipsrs/aset/mutasi')->with('error', 'Hanya Admin yang dapat memutasi aset.');
        }
        $v = $this->validateOrFail([
            'id_aset'       => 'required',
            'jenis_mutasi'  => 'required|in_list[Pindah Ruangan]',
            'lokasi_tujuan' => 'required',
            'petugas'       => 'required',
            'tanggal'       => 'required',
        ], 'Mohon lengkapi data mutasi yang wajib diisi.');
        if ($v !== true) return $v;

        $db = \Config\Database::connect();
        try {
            $post = $this->request->getPost();
            $jenisMutasi = $post['jenis_mutasi'];
            $lokasiTujuan_id = $post['lokasi_tujuan'] ?? null;
            $mutasiModel = new \App\Models\MutasiModel();
            $seriesModel = new \App\Models\AsetSeriesModel();
            $data = $this->whitelist(['petugas', 'tanggal', 'catatan']);
            $idSeries = $this->request->getPost('id_aset');
            $aset = $seriesModel->getById($idSeries);
            if (!$aset || !AsetLifecycle::canRelocate($aset['status'] ?? null)) {
                return redirect()->to('/ipsrs/aset/mutasi')->with('error', 'Hanya unit berstatus Tersedia yang dapat dipindahkan.');
            }
            $lokasi = (new \App\Models\MasterLokasiModel())->find($lokasiTujuan_id);
            if (!$lokasi) {
                return redirect()->back()->withInput()->with('error', 'Lokasi tujuan tidak ditemukan.');
            }

            $lokasiTujuan_str = $lokasi['nama_ruangan'] ?? $lokasi['nama_unit'] ?? '';
            $data['id_aset_series'] = $idSeries;
            $data['nama_aset']   = $aset['nama_aset'] ?? ($aset['nomor_aset'] ?? '');
            $data['lokasi_asal'] = $aset['lokasi'] ?? null;
            $data['alasan'] = $jenisMutasi;
            $data['lokasi_tujuan'] = $lokasiTujuan_str;

            $db->transBegin();
            $mutasiModel->create($data);
            $seriesModel->update($idSeries, ['id_lokasi' => $lokasiTujuan_id]);
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Mutasi aset tidak dapat diselesaikan.');
            }
            $db->transCommit();

            return redirect()->to('/ipsrs/aset/mutasi')->with('success', 'Mutasi aset berhasil dicatat');
        } catch (\Throwable $e) {
            $db->transRollback();
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
                'msg' => 'Data koordinat GPS tidak valid atau tidak lengkap',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        // Validasi jangkauan GPS (-90 sd 90 untuk Latitude, -180 sd 180 untuk Longitude)
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false, 
                'msg' => 'Kordinat GPS di luar jangkauan logika pemetaan bumi',
                'csrfHash' => csrf_hash(),
            ]);
        }

        try {
            // 3. Validasi Keberadaan Aset di Database
            $seriesModel = new \App\Models\AsetSeriesModel();
            $aset = $seriesModel->getById($id);
            if (!$aset) {
                return $this->response->setStatusCode(404)->setJSON([
                    'ok' => false, 
                    'msg' => 'Aset tidak ditemukan di sistem, ID mungkin tidak valid',
                    'csrfHash' => csrf_hash(),
                ]);
            }

            // 4. Rate Limiting Sederhana (Cegah spam ping berturut-turut dalam 10 detik)
            if (!empty($aset['last_seen_at'])) {
                $lastSeen = strtotime($aset['last_seen_at']);
                if (time() - $lastSeen < 10) {
                    return $this->response->setJSON([
                        'ok' => true,
                        'updated' => false,
                        'msg' => 'Lokasi baru tidak disimpan karena aset baru saja diperbarui.',
                        'csrfHash' => csrf_hash(),
                    ]);
                }
            }

            // 5. Eksekusi Update ke Database
            $seriesModel->update($id, [
                'last_seen_at'  => date('Y-m-d H:i:s'), // Format MySQL Timestamp yang presisi
                'last_seen_lat' => $lat,
                'last_seen_lng' => $lng,
                'last_seen_by'  => session('user_name') ?? 'Guest (QR Scan)'
            ]);

            return $this->response->setJSON([
                'ok' => true,
                'updated' => true,
                'msg' => 'Lokasi aset berhasil diperbarui ke server',
                'csrfHash' => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            log_message('critical', '[Aset::ping] Gagal memperbarui lokasi aset: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false, 
                'msg' => 'Terjadi kendala pada server saat menyimpan koordinat',
                'csrfHash' => csrf_hash(),
            ]);
        }
    }

    public function qr(string $id)
    {
        $seriesModel = new \App\Models\AsetSeriesModel();
        $series = $seriesModel->getById($id);
        if (!$series) { return redirect()->to('/ipsrs/aset'); }

        $aset = $this->model->getById($series['id_aset']);
        if (!$aset) { return redirect()->to('/ipsrs/aset'); }

        return view('pages/aset/qr', compact('aset', 'series'));
    }

    public function pinjam()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/ipsrs')->with('error', 'Hanya Admin yang dapat meminjamkan aset.');
        }
        $validation = $this->validateOrFail([
            'id_aset_series'      => 'required',
            'nama_peminjam'       => 'required|max_length[100]',
            'unit_peminjam'       => 'required|max_length[100]',
            'tgl_kembali_rencana' => 'required|valid_date[Y-m-d]',
        ], 'Lengkapi data peminjaman aset.');
        if ($validation !== true) {
            return $validation;
        }
        $id = (string) $this->request->getPost('id_aset_series');
        $series = (new \App\Models\AsetSeriesModel())->getById($id);
        if (!$series || !AsetLifecycle::canBorrow($series['status'] ?? null)) {
            return redirect()->to('/ipsrs/aset/series/' . $id)->with('error', 'Hanya aset Tersedia tanpa lifecycle aktif yang dapat dipinjamkan.');
        }
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $db->table('aset_series')->where('id', $id)->where('status', 'Tersedia')->update(['status' => 'Dipinjam']);
            if ($db->affectedRows() !== 1) {
                throw new \RuntimeException('Status aset sudah berubah; peminjaman dibatalkan.');
            }
            $db->table('peminjaman_aset')->insert([
                'id' => sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
                'id_aset_series' => $id,
                'nama_peminjam' => $this->request->getPost('nama_peminjam'),
                'unit_peminjam' => $this->request->getPost('unit_peminjam'),
                'tgl_pinjam' => date('Y-m-d'),
                'tgl_kembali_rencana' => $this->request->getPost('tgl_kembali_rencana'),
                'status' => 'Dipinjam',
                'keterangan' => $this->request->getPost('keterangan'),
                'id_admin' => session('user_id'),
            ]);
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Peminjaman aset tidak dapat diselesaikan.');
            }
            $db->transCommit();
            return redirect()->to('/ipsrs/aset/series/' . $id)->with('success', 'Aset berhasil dipinjamkan.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/ipsrs/aset/series/' . $id)->with('error', $e->getMessage());
        }
    }

    public function kembali($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/ipsrs')->with('error', 'Hanya Admin yang dapat menerima pengembalian aset.');
        }
        $series = (new \App\Models\AsetSeriesModel())->getById($id);
        if (!$series || !AsetLifecycle::canReturn($series['status'] ?? null)) {
            return redirect()->to('/ipsrs/aset/series/' . $id)->with('error', 'Aset ini tidak memiliki peminjaman aktif yang dapat dikembalikan.');
        }
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $db->table('peminjaman_aset')->where('id_aset_series', $id)->where('status', 'Dipinjam')->update([
                'tgl_kembali_aktual' => date('Y-m-d'),
                'status' => 'Selesai',
            ]);
            if ($db->affectedRows() !== 1) {
                throw new \RuntimeException('Peminjaman aktif tidak ditemukan atau data ganda terdeteksi.');
            }
            $db->table('aset_series')->where('id', $id)->where('status', 'Dipinjam')->update(['status' => 'Tersedia']);
            if ($db->affectedRows() !== 1) {
                throw new \RuntimeException('Status aset sudah berubah; pengembalian dibatalkan.');
            }
            $db->transCommit();
            return redirect()->to('/ipsrs/aset/series/' . $id)->with('success', 'Aset berhasil dikembalikan.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/ipsrs/aset/series/' . $id)->with('error', $e->getMessage());
        }
    }


    public function tandaiRusakBerat($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/ipsrs')->with('error', 'Hanya Admin yang dapat mengubah lifecycle aset.');
        }
        $db = \Config\Database::connect();
        $db->table('aset_series')->where('id', $id)->whereIn('status', ['Tersedia', 'Dalam Perbaikan'])->update(['status' => 'Rusak Berat']);
        if ($db->affectedRows() !== 1) {
            return redirect()->to('/ipsrs/aset/series/' . $id)->with('error', 'Lifecycle aset tidak mengizinkan penandaan Rusak Berat.');
        }
        return redirect()->to('/ipsrs/aset/series/' . $id)->with('success', 'Aset telah ditandai sebagai Rusak Berat. Opsi Kanibalisasi & Penghapusan kini tersedia.');
    }

    public function hapus()
    {
        if (! $this->isAdmin()) {
            return redirect()->to('/ipsrs')->with('error', 'Hanya Admin yang dapat menghapuskan aset.');
        }

        $id = $this->request->getPost('id_aset_series');
        $db = \Config\Database::connect();
        $series = (new \App\Models\AsetSeriesModel())->getById((string) $id);
        if (!$series || !AsetLifecycle::canDispose($series['status'] ?? null)) {
            return redirect()->back()->withInput()->with('error', 'Penghapusan hanya dapat dilakukan untuk aset berstatus Rusak Berat.');
        }
        $documentStorage = new BaDocumentStorage();

        $file = $this->request->getFile('file_dokumen_ba');
        $fileName = null;
        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $fileError = $documentStorage->validate($file);
            if ($fileError !== null) {
                return redirect()->back()->withInput()->with('error', $fileError);
            }

            try {
                $fileName = $documentStorage->store($file);
            } catch (\RuntimeException $exception) {
                log_message('error', 'BA upload failed for asset series {series}: {message}', [
                    'series'  => $id,
                    'message' => $exception->getMessage(),
                ]);

                return redirect()->back()->withInput()->with('error', 'Dokumen BA tidak dapat disimpan. Penghapusan aset dibatalkan.');
            }
        }

        try {
            $db->transBegin();
            $db->table('penghapusan_aset')->insert([
                'id' => sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
                'id_aset_series' => $id,
                'no_ba' => $this->request->getPost('no_ba'),
                'tgl_ba' => $this->request->getPost('tgl_ba'),
                'tindak_lanjut' => $this->request->getPost('tindak_lanjut'),
                'file_dokumen_ba' => $fileName,
                'keterangan' => $this->request->getPost('keterangan'),
                'id_admin' => session('user_id'),
            ]);
            $db->table('aset_series')->where('id', $id)->where('status', 'Rusak Berat')->update(['status' => 'Dihapuskan']);
            if ($db->affectedRows() !== 1 || $db->transStatus() === false) {
                throw new \RuntimeException('Lifecycle aset sudah berubah atau penghapusan tidak dapat disimpan.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($fileName !== null) {
                $documentStorage->delete($fileName);
            }

            log_message('error', 'Asset disposal transaction failed for asset series {series}: {message}', ['series' => $id, 'message' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Penghapusan aset gagal disimpan. Dokumen BA tidak disimpan.');
        }

        return redirect()->to('/ipsrs/aset/series/' . $id)->with('success', 'Aset berhasil dihapuskan beserta Berita Acara.');
    }

    public function downloadBa(string $id)
    {
        if (! $this->isAdmin()) {
            return redirect()->to('/ipsrs')->with('error', 'Akses dokumen BA ditolak.');
        }

        $record = \Config\Database::connect()
            ->table('penghapusan_aset')
            ->select('id, no_ba, file_dokumen_ba')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (! $record || empty($record['file_dokumen_ba'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $documentStorage = new BaDocumentStorage();
        $path = $documentStorage->resolve($record['file_dokumen_ba']);
        if ($path === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $documentNumber = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $record['no_ba']) ?: 'dokumen';
        $downloadName = 'BA-' . trim($documentNumber, '-') . '.' . $documentStorage->downloadExtension($record['file_dokumen_ba']);

        return $this->response
            ->download($path, null)
            ->setFileName($downloadName)
            ->setHeader('X-Content-Type-Options', 'nosniff');
    }

    private function isAdmin(): bool
    {
        return strtolower((string) session('user_role')) === 'admin';
    }
}











