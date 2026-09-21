<?php

namespace App\Controllers;

use App\Config\IPSRS;
use App\Libraries\AccessPolicy;
use App\Libraries\AsetLifecycle;
use App\Libraries\SignatureEvidence;
use App\Models\LKModel;
use App\Models\AsetModel;
use App\Models\StokModel;

class LK extends BaseController
{
    private LKModel $model;

    public function __construct()
    {
        $this->model = new LKModel();
    }

    public function index(): string
    {
        $lk     = $this->model->getAll();
        $search = $this->request->getGet('q') ?? '';
        $status = $this->request->getGet('status') ?? '';
        $kode   = $this->request->getGet('kode') ?? '';

        if ($search) {
            $lk = array_filter($lk, fn($l) =>
                stripos($l['no_order'], $search) !== false ||
                stripos($l['keluhan'], $search) !== false ||
                stripos($l['pelapor'], $search) !== false);
        }
        if ($status) { $lk = array_filter($lk, fn($l) => $l['status'] === $status); }
        if ($kode)   { $lk = array_filter($lk, fn($l) => $l['kode'] === $kode); }

        $role = AccessPolicy::role(session('user_role'));
        if ($role !== 'admin') {
            $lk = array_filter($lk, fn($row) => AccessPolicy::canViewLk($row, $role, session('user_name')));
        }

        return $this->render('pages/lk/index', [
            'lk'            => array_values($lk),
            'search'        => $search,
            'status'        => $status,
            'kode'          => $kode,
            'kodeKerusakan' => (new \App\Models\KodeKerusakanModel())->getAll(),
        ]);
    }

    public function show(string $id)
    {
        $lk = $this->model->getById($id);
        if (!$lk) { return redirect()->to('/ipsrs/lk'); }
        if (!AccessPolicy::canViewLk($lk, session('user_role'), session('user_name'))) {
            return redirect()->to('/ipsrs/lk')->with('error', 'LK tidak ditemukan.');
        }

        return $this->render('pages/lk/show', compact('lk') + [
            'sukuCadang'   => $this->model->getSukuCadang($id),
            'stokTersedia' => (new StokModel())->getAll(),
            'vendorDetail' => $this->model->getVendor($id),
            'vendorList'   => (new \App\Models\VendorModel())->getAll(),
            'aset' => (new \App\Models\AsetSeriesModel())->getAllWithParent(),
            'kodeKerusakan'=> (new \App\Models\KodeKerusakanModel())->getAll(),
            'teknisiList'  => (new \App\Models\PenggunaModel())->getByRole('Teknisi'),
        ]);
    }

    public function create(): string
    {
        $view = session('user_role') === 'pelapor' ? 'pages/lk/form_pelapor' : 'pages/lk/form';
        return $this->render($view, [
            'aset' => (new \App\Models\AsetSeriesModel())->getAllWithParent(),
            'kodeKerusakan'  => (new \App\Models\KodeKerusakanModel())->getAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'tanggal'      => 'required',
            'jam_laporan'  => 'required',
            'pelapor'      => 'required',
            'unit_pelapor' => 'required',
            'keluhan'      => 'required',
            'lokasi'       => 'required',
        ];
        
        if (session('user_role') !== 'pelapor') {
            $rules['kode'] = 'required|max_length[10]';
        }

        $v = $this->validateOrFail($rules, 'Mohon lengkapi seluruh data laporan yang wajib diisi.');
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'tanggal', 'jam_laporan', 'pelapor', 'unit_pelapor',
                'keluhan', 'kode', 'lokasi', 'nama_aset',
                'id_aset', 'update_lokasi_aset',
            ]);
            $data['status'] = IPSRS::STATUS_LK[0]; // Laporan Masuk
            $data['kode']   = $data['kode'] ?? 'PR';

            $updateLokasi = !empty($data['update_lokasi_aset']);
            unset($data['update_lokasi_aset']);
            $idAset = trim((string) ($data['id_aset'] ?? ''));
            unset($data['id_aset']);

            if ($idAset !== '') {
                $series = (new \App\Models\AsetSeriesModel())->getById($idAset);
                if (!$series) {
                    return redirect()->back()->withInput()->with('error', 'Aset yang dipilih tidak ditemukan.');
                }
                $data['id_aset_series'] = $idAset;
            }

            if (session('user_role') === 'pelapor') {
                // Not recording id_pengguna_pelapor as it doesn't exist in schema
            }

            $lk = $this->model->createWithRetry(
                $data,
                fn() => $this->model->nextNoOrder(),
                'no_order'
            );

            if (!empty($data['id_aset_series'])) {
                $seriesModel = new \App\Models\AsetSeriesModel();
                $linkedSeries = $seriesModel->getById($data['id_aset_series']);
                if ($linkedSeries && AsetLifecycle::canSyncFromLk($linkedSeries['status'] ?? null)) {
                    $asetUpdate = ['status' => IPSRS::LK_TO_ASET_STATUS['Survei']];
                    if ($updateLokasi && !empty($data['lokasi'])) {
                        $asetUpdate['lokasi'] = $data['lokasi'];
                    }
                    $seriesModel->update($data['id_aset_series'], $asetUpdate);
                }
            }

            // Trigger WhatsApp Broadcast Mock/Placeholder
            $lkId = $lk['id'] ?? null;
            if ($lkId) {
                $claimLink = base_url('/ipsrs/lk/' . $lkId);
                $waMessage = "🚨 *Laporan Kerusakan Baru!*\n\n"
                           . "Unit: {$data['unit_pelapor']}\n"
                           . "Lokasi: {$data['lokasi']}\n"
                           . "Keluhan: {$data['keluhan']}\n\n"
                           . "Buka detail tiket untuk meninjau dan mengambil pekerjaan:\n"
                           . $claimLink;
                           
                $wa = new \App\Libraries\WhatsAppAPI();
                $wa->sendBroadcast($waMessage);
            }

            return redirect()->to('/ipsrs/lk')->with('success', 'LK berhasil dibuat');
        } catch (\Throwable $e) {
            log_message('error', '[LK::store] ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk')->with('error', 'Gagal membuat LK: ' . $e->getMessage());
        }
    }

    public function delete(string $id)
    {
        if (!AccessPolicy::hasRole(session('user_role'), ['admin'])) {
            return redirect()->to('/ipsrs/lk')->with('error', 'Akses ditolak.');
        }
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $lk = $this->model->getById($id);
            if (!$lk) {
                $db->transRollback();
                return redirect()->to('/ipsrs/lk')->with('error', 'LK tidak ditemukan.');
            }
            $stokModel = new StokModel();
            foreach ($this->model->getGudangSukuCadang($id) as $sc) {
                $stokModel->catatTransaksi([
                    'id_barang'   => $sc['id_barang'],
                    'nama_barang' => $sc['nama_barang'],
                    'jenis'       => 'Masuk',
                    'jumlah'      => (int) $sc['jumlah'],
                    'tanggal'     => date('Y-m-d'),
                    'no_dokumen'  => 'Rollback Hapus LK ' . ($lk['no_order'] ?? $id),
                    'keterangan'  => 'Pengembalian stok dari penghapusan LK',
                    'petugas'     => session('user_name') ?? 'Sistem',
                ]);
            }
            $this->model->delete($id);
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Penghapusan LK tidak dapat diselesaikan.');
            }
            $db->transCommit();
            return redirect()->to('/ipsrs/lk')->with('success', 'Laporan kerusakan berhasil dihapus dan stok terkait dikembalikan');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[LK::delete] ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk')->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }

    public function claim(string $id)
    {
        $role = strtolower((string) session('user_role'));
        if (!in_array($role, ['admin', 'teknisi'], true)) {
            return redirect()->to('/ipsrs/lk')->with('error', 'Akses ditolak.');
        }
        $lk = $this->model->getById($id);
        if (!$lk) {
            return redirect()->to('/ipsrs/lk')->with('error', 'LK tidak ditemukan');
        }

        if (($lk['status'] ?? '') !== IPSRS::STATUS_LK[0]) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Tiket ini tidak lagi dapat diklaim.');
        }

        try {
            if (!$this->model->claimAvailable($id, (string) session('user_name'))) {
                $latest = $this->model->getById($id);
                $teknisi = $latest['teknisi'] ?? '';
                $message = $teknisi === (string) session('user_name')
                    ? 'Anda sudah ditugaskan pada pekerjaan ini.'
                    : 'Maaf, pekerjaan ini baru saja diambil oleh teknisi lain.';
                return redirect()->to('/ipsrs/lk/' . $id)->with('error', $message);
            }
            
            return redirect()->to('/ipsrs/lk/' . $id)->with('success', 'Berhasil! Anda telah mengambil tiket perbaikan ini.');
        } catch (\Throwable $e) {
            log_message('error', '[LK::claim] ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Gagal klaim pekerjaan: ' . $e->getMessage());
        }
    }

    public function updateDetail(string $id)
    {
        $lk = $this->model->getById($id);
        if (!$lk || !AccessPolicy::canManageLk($lk, session('user_role'), session('user_name'))) {
            return redirect()->to('/ipsrs/lk')->with('error', 'Akses ditolak.');
        }
        if (($lk['status'] ?? '') === IPSRS::STATUS_LK[6]) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'LK yang sudah selesai tidak dapat diubah.');
        }
        
        $post = $this->request->getPost();
        
        $data = [];
        if (!empty($post['kode'])) $data['kode'] = $post['kode'];
        if (!empty($post['id_aset'])) $data['id_aset_series'] = $post['id_aset'];
        if (!empty($post['lokasi'])) $data['lokasi'] = $post['lokasi'];
        if (!empty($post['keluhan'])) $data['keluhan'] = $post['keluhan'];
        if (!empty($post['nama_aset'])) $data['nama_aset'] = $post['nama_aset'];
        
        if (!empty($data)) {
            $this->model->update($id, $data);
            
            // If aset is changed, maybe update aset location
            if (!empty($post['id_aset']) && !empty($post['update_lokasi_aset']) && !empty($post['lokasi'])) {
                $seriesModel = new \App\Models\AsetSeriesModel();
                $series = $seriesModel->getById($post['id_aset']);
                if ($series && AsetLifecycle::canRelocate($series['status'] ?? null)) {
                    $seriesModel->update($post['id_aset'], ['lokasi' => $post['lokasi']]);
                }
            }
        }
        
        return redirect()->to('/ipsrs/lk/' . $id)->with('success', 'Detail Laporan berhasil dilengkapi.');
    }

    public function updateStatus(string $id)
    {
        $role = strtolower((string) session('user_role'));
        if (!in_array($role, ['admin', 'teknisi'], true)) {
            return redirect()->to('/ipsrs/lk')->with('error', 'Akses ditolak.');
        }
        $lk   = $this->model->getById($id);
        $post = $this->whitelist([
            'status_baru', 'teknisi', 'tindakan',
            'tanggal_cek', 'jam_cek', 'ttd_pelapor'
        ]);
        $next = $post['status_baru'] ?? null;

        if (!$next || !$lk || !in_array($next, IPSRS::STATUS_LK, true)) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Status tidak valid');
        }

        $current = $lk['status'] ?? '';
        $allowedTransitions = IPSRS::LK_STATUS_TRANSITIONS[$current] ?? [];
        if (!in_array($next, $allowedTransitions, true)) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Perubahan status tidak diizinkan dari status saat ini.');
        }

        if (!AccessPolicy::canManageLk($lk, $role, session('user_name'))) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Tiket ini bukan penugasan Anda.');
        }

        if ($next === IPSRS::STATUS_LK[6]) {
            $post['ttd_pelapor'] = SignatureEvidence::normalize($post['ttd_pelapor'] ?? null);
            if (empty($post['tindakan']) || $post['ttd_pelapor'] === null) {
                return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Tindakan dan bukti serah-terima pelapor berupa tanda tangan PNG yang valid wajib untuk menyelesaikan LK.');
            }
        }

        if ($timingError = $this->validateTransitionTiming($lk, $next, $post)) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', $timingError);
        }

        $sc = $this->model->getSukuCadang($id);
        $vd = $this->model->getVendor($id);
        
        $computedProses = 'I';
        if ($next === 'Menunggu Vendor' || !empty($vd)) {
            $computedProses = 'III';
        } elseif ($next === 'Menunggu Suku Cadang' || !empty($sc)) {
            $computedProses = 'II';
        }

        try {
            $data = array_filter([
                'status'          => $next,
                'teknisi'         => $role === 'teknisi' ? (string) session('user_name') : ($post['teknisi'] ?? null),
                'tindakan'        => $post['tindakan']        ?? null,
                'proses'          => $computedProses,
                'tanggal_cek'     => $next === 'Survei' ? ($post['tanggal_cek'] ?? null) : null,
                'jam_cek'         => $next === 'Survei' ? ($post['jam_cek'] ?? null) : null,
                'ttd_pelapor'     => $post['ttd_pelapor']     ?? null,
            ], fn($v) => $v !== null && $v !== '');

            // Logika menyimpan id_pengguna_pelapor jika pelapor login dan update status ke selesai
            if (session('user_role') === 'pelapor' && $next === 'Selesai') {
                $data['id_pengguna_pelapor'] = session('user_id');
            }

            $this->calcResponseTime($lk, $next, $data);
            $this->calcDownTime($lk, $next, $data);
            $this->syncAsetStatus($lk, $next);

            $this->model->update($id, $data);

            // WhatsApp Notification to Technician
            $newTeknisi = $data['teknisi'] ?? null;
            $oldTeknisi = $lk['teknisi'] ?? null;
            if ($newTeknisi && $newTeknisi !== $oldTeknisi && in_array($next, ['Didisposisi', 'Survei'])) {
                // In a real scenario, you'd fetch the technician's phone number from Pengguna table.
                // For now, we broadcast to the default group / number.
                $waMessage = "🛠️ *Tugas Perbaikan Baru!*\n\n"
                           . "No Order: " . ($lk['no_order'] ?? $id) . "\n"
                           . "Teknisi: {$newTeknisi}\n"
                           . "Unit: {$lk['unit_pelapor']}\n"
                           . "Keluhan: {$lk['keluhan']}\n\n"
                           . "Silakan segera menuju lokasi.";
                
                $wa = new \App\Libraries\WhatsAppAPI();
                // To send to a specific number: $wa->sendBroadcast($waMessage, $technicianPhoneNumber);
                $wa->sendBroadcast($waMessage);
            }

            return redirect()->to('/ipsrs/lk/' . $id)->with('success', 'Status LK diperbarui');
        } catch (\Throwable $e) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    public function addSukuCadang(string $id)
    {
        $lk      = $this->model->getById($id);
        if (!$lk || !AccessPolicy::canManageLk($lk, session('user_role'), session('user_name'))) {
            return redirect()->to('/ipsrs/lk')->with('error', 'Akses ditolak.');
        }
        $post    = $this->whitelist(['id_barang', 'jumlah', 'keterangan']);
        $idBarang = $post['id_barang'] ?? null;
        $jumlah   = (int)($post['jumlah'] ?? 0);

        if (!$idBarang || $jumlah <= 0) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Data suku cadang tidak valid');
        }
        if (($lk['status'] ?? '') === 'Selesai') {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Suku cadang tidak dapat ditambah pada LK yang sudah selesai.');
        }
        if ($this->model->hasGudangSukuCadang($id, $idBarang)) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Barang gudang tersebut sudah tercatat pada LK ini.');
        }

        $stokModel = new StokModel();
        $barang    = $stokModel->getById($idBarang);

        if (!$barang) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Barang tidak ditemukan');
        }
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $stokModel->catatTransaksi([
                'id_barang'   => $idBarang,
                'nama_barang' => $barang['nama'],
                'jenis'       => 'Keluar',
                'jumlah'      => $jumlah,
                'tanggal'     => date('Y-m-d'),
                'no_dokumen'  => $lk['no_order'] ?? null,
                'keterangan'  => 'Digunakan untuk ' . ($lk['no_order'] ?? $id),
                'petugas'     => session('user_name') ?? 'Teknisi',
            ]);
            $this->model->addSukuCadang([
                'id_lk'       => $id,
                'id_barang'   => $idBarang,
                'sumber'      => 'Gudang',
                'nama_barang' => $barang['nama'],
                'jumlah'      => $jumlah,
                'satuan'      => $barang['satuan'] ?? 'pcs',
                'keterangan'  => $post['keterangan'] ?? null,
            ]);
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Penggunaan suku cadang tidak dapat diselesaikan.');
            }
            $db->transCommit();

            return redirect()->to('/ipsrs/lk/' . $id)->with('success', 'Suku cadang berhasil dicatat & stok dikurangi');
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[LK::addSukuCadang] ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Gagal mencatat suku cadang: ' . $e->getMessage());
        }
    }

    public function storeVendor(string $id)
    {
        $lk = $this->model->getById($id);
        if (!$lk || !AccessPolicy::canManageLk($lk, session('user_role'), session('user_name'))) {
            return redirect()->to('/ipsrs/lk')->with('error', 'Akses ditolak.');
        }
        if (($lk['status'] ?? '') === IPSRS::STATUS_LK[6]) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Vendor tidak dapat ditambah pada LK yang sudah selesai.');
        }

        $post        = $this->whitelist([
            'id_vendor',
            'tanggal_kirim', 'tanggal_kembali', 'estimasi_selesai', 'keterangan',
        ]);
        $vendorModel = new \App\Models\VendorModel();
        $idVendor    = $post['id_vendor'] ?? '';

        if ($idVendor) {
            $vendor    = $vendorModel->getById($idVendor);
            $namaFinal = $vendor['nama_vendor'] ?? '-';
        } else {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Pilih vendor terlebih dahulu');
        }

        try {
            $this->model->addVendor([
                'id_lk'            => $id,
                'id_vendor'        => $idVendor,
                'nama_vendor'      => $namaFinal,
                'tanggal_kirim'    => ($post['tanggal_kirim'] ?? '')    ?: null,
                'tanggal_kembali'  => ($post['tanggal_kembali'] ?? '')  ?: null,
                'estimasi_selesai' => ($post['estimasi_selesai'] ?? '') ?: null,
                'keterangan'       => $post['keterangan'] ?? null,
            ]);

            return redirect()->to('/ipsrs/lk/' . $id)->with('success', 'Data vendor (Proses III) berhasil dicatat');
        } catch (\Throwable $e) {
            log_message('error', '[LK::storeVendor] add vendor: ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Gagal mencatat vendor: ' . $e->getMessage());
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────

    /** Pastikan waktu survei/penutupan tidak mendahului laporan. */
    private function validateTransitionTiming(array $lk, string $next, array $post): ?string
    {
        if ($next === 'Survei') {
            $tanggalCek = (string) ($post['tanggal_cek'] ?? '');
            $jamCek     = (string) ($post['jam_cek'] ?? '');
            $responseTime = \App\Libraries\Metrics::selisihMenit(
                $lk['tanggal'] ?? date('Y-m-d'), $lk['jam_laporan'] ?? '00:00',
                $tanggalCek, $jamCek
            );

            if ($tanggalCek === '' || $jamCek === '' || $responseTime === null) {
                return 'Tanggal dan jam survei wajib valid dan tidak boleh mendahului waktu laporan.';
            }
        }

        if ($next === IPSRS::STATUS_LK[array_key_last(IPSRS::STATUS_LK)]) {
            $downTime = \App\Libraries\Metrics::selisihMenit(
                $lk['tanggal'] ?? date('Y-m-d'), $lk['jam_laporan'] ?? '00:00',
                date('Y-m-d'), date('H:i')
            );

            if ($downTime === null) {
                return 'Laporan memiliki waktu yang tidak valid atau lebih baru dari waktu server; LK tidak dapat diselesaikan.';
            }
        }

        return null;
    }

    /** Hitung response time dari laporan sampai survei pertama. */
    private function calcResponseTime(array $lk, string $next, array &$data): void
    {
        if (
            $next !== 'Survei'
            || empty($data['tanggal_cek'])
            || empty($data['jam_cek'])
            || (array_key_exists('response_time', $lk) && $lk['response_time'] !== null)
        ) {
            return;
        }
        $rt = \App\Libraries\Metrics::selisihMenit(
            $lk['tanggal'] ?? date('Y-m-d'), $lk['jam_laporan'] ?? '00:00',
            $data['tanggal_cek'] ?? date('Y-m-d'), $data['jam_cek']
        );
        if ($rt !== null) {
            $data['response_time'] = $rt;
        }
    }

    /** Hitung down time (laporan → selesai). */
    private function calcDownTime(array $lk, string $next, array &$data): void
    {
        if ($next !== IPSRS::STATUS_LK[array_key_last(IPSRS::STATUS_LK)]) return;

        $tanggalSelesai = date('Y-m-d');
        $jamSelesai     = date('H:i');
        $data['tanggal_selesai'] = $tanggalSelesai;
        $data['jam_selesai']     = $jamSelesai;

        $dt = \App\Libraries\Metrics::selisihMenit(
            $lk['tanggal'] ?? date('Y-m-d'), $lk['jam_laporan'] ?? '00:00',
            $tanggalSelesai, $jamSelesai
        );
        if ($dt !== null) {
            $data['down_time'] = $dt;
        }
    }

    /** Sinkronkan status aset mengikuti status LK. */
    private function syncAsetStatus(array $lk, string $next): void
    {
        if (empty($lk['id_aset_series'])) return;

        $asetStatus = IPSRS::LK_TO_ASET_STATUS[$next] ?? null;
        if ($asetStatus !== null) {
            $seriesModel = new \App\Models\AsetSeriesModel();
            $series = $seriesModel->getById($lk['id_aset_series']);
            if ($series && AsetLifecycle::canSyncFromLk($series['status'] ?? null)) {
                $seriesModel->update($lk['id_aset_series'], ['status' => $asetStatus]);
            }
        }
    }
}



