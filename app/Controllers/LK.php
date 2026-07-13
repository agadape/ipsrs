<?php

namespace App\Controllers;

use App\Config\IPSRS;
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

        return $this->render('pages/lk/show', compact('lk') + [
            'sukuCadang'   => $this->model->getSukuCadang($id),
            'stokTersedia' => (new StokModel())->getAll(),
            'vendorDetail' => $this->model->getVendor($id),
            'vendorList'   => (new \App\Models\VendorModel())->getAll(),
            'aset'         => (new AsetModel())->getAll(),
            'teknisiList'  => (new \App\Models\PenggunaModel())->getByRole('Teknisi'),
        ]);
    }

    public function create(): string
    {
        return $this->render('pages/lk/form', [
            'aset'           => (new AsetModel())->getAll(),
            'kodeKerusakan'  => (new \App\Models\KodeKerusakanModel())->getAll(),
        ]);
    }

    public function store()
    {
        $v = $this->validateOrFail([
            'tanggal'      => 'required',
            'jam_laporan'  => 'required',
            'pelapor'      => 'required',
            'unit_pelapor' => 'required',
            'keluhan'      => 'required',
            'kode'         => 'required|max_length[10]',
            'lokasi'       => 'required',
        ], 'Mohon lengkapi seluruh data laporan yang wajib diisi.');
        if ($v !== true) return $v;

        try {
            $data = $this->whitelist([
                'tanggal', 'jam_laporan', 'pelapor', 'unit_pelapor',
                'keluhan', 'kode', 'lokasi', 'id_aset', 'nama_aset',
                'update_lokasi_aset',
            ]);
            $data['status'] = IPSRS::STATUS_LK[0]; // Laporan Masuk
            $data['kode']   = $data['kode'] ?? 'PR';

            $updateLokasi = !empty($data['update_lokasi_aset']);
            unset($data['update_lokasi_aset']);

            if (empty($data['id_aset'])) {
                $data['id_aset'] = null;
            }

            $lk = $this->model->createWithRetry(
                $data,
                fn() => $this->model->nextNoOrder(),
                'no_order'
            );

            if (!empty($data['id_aset'])) {
                $asetUpdate = ['status' => IPSRS::LK_TO_ASET_STATUS['Survei']];
                if ($updateLokasi && !empty($data['lokasi'])) {
                    $asetUpdate['lokasi'] = $data['lokasi'];
                }
                (new AsetModel())->update($data['id_aset'], $asetUpdate);
            }

            // Trigger WhatsApp Broadcast Mock/Placeholder
            $lkId = $lk['id'] ?? null;
            if ($lkId) {
                $claimLink = base_url('/ipsrs/lk/claim/' . $lkId);
                $waMessage = "🚨 *Laporan Kerusakan Baru!*\n\n"
                           . "Unit: {$data['unit_pelapor']}\n"
                           . "Lokasi: {$data['lokasi']}\n"
                           . "Keluhan: {$data['keluhan']}\n\n"
                           . "Klik link di bawah untuk otomatis mengambil tiket:\n"
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
        try {
            $this->model->delete($id);
            return redirect()->to('/ipsrs/lk')->with('success', 'Laporan kerusakan berhasil dihapus');
        } catch (\Throwable $e) {
            log_message('error', '[LK::delete] ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk')->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }

    public function claim(string $id)
    {
        $lk = $this->model->getById($id);
        if (!$lk) {
            return redirect()->to('/ipsrs/lk')->with('error', 'LK tidak ditemukan');
        }

        // Check if already claimed
        if (!empty($lk['teknisi'])) {
            $msg = ($lk['teknisi'] === session('user_name')) 
                ? 'Anda sudah ditugaskan pada pekerjaan ini.' 
                : 'Maaf, pekerjaan ini sudah diambil oleh teknisi: ' . $lk['teknisi'];
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', $msg);
        }

        // Claim it
        try {
            $this->model->update($id, [
                'teknisi' => session('user_name'),
                'status'  => IPSRS::STATUS_LK[1], // Didisposisi / Survei
            ]);
            
            return redirect()->to('/ipsrs/lk/' . $id)->with('success', 'Berhasil! Anda telah mengambil tiket perbaikan ini.');
        } catch (\Throwable $e) {
            log_message('error', '[LK::claim] ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Gagal klaim pekerjaan: ' . $e->getMessage());
        }
    }

    public function updateStatus(string $id)
    {
        $lk   = $this->model->getById($id);
        $post = $this->whitelist([
            'status_baru', 'teknisi', 'tindakan', 'proses',
            'tanggal_cek', 'jam_cek', 'tanggal_selesai', 'jam_selesai',
        ]);
        $next = $post['status_baru'] ?? null;

        if (!$next || !$lk) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Status tidak valid');
        }

        try {
            $data = array_filter([
                'status'          => $next,
                'teknisi'         => $post['teknisi']         ?? null,
                'tindakan'        => $post['tindakan']        ?? null,
                'proses'          => $post['proses']          ?? null,
                'tanggal_cek'     => $post['tanggal_cek']     ?? null,
                'jam_cek'         => $post['jam_cek']         ?? null,
                'tanggal_selesai' => $post['tanggal_selesai'] ?? null,
                'jam_selesai'     => $post['jam_selesai']     ?? null,
            ], fn($v) => $v !== null && $v !== '');

            $this->calcResponseTime($lk, $next, $data);
            $this->calcDownTime($lk, $next, $data);
            $this->syncAsetStatus($lk, $next);

            $this->model->update($id, $data);
            return redirect()->to('/ipsrs/lk/' . $id)->with('success', 'Status LK diperbarui');
        } catch (\Throwable $e) {
            log_message('error', '[LK::updateStatus] ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function addSukuCadang(string $id)
    {
        $lk      = $this->model->getById($id);
        $post    = $this->whitelist(['id_barang', 'jumlah', 'keterangan']);
        $idBarang = $post['id_barang'] ?? null;
        $jumlah   = (int)($post['jumlah'] ?? 0);

        if (!$lk || !$idBarang || $jumlah <= 0) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Data suku cadang tidak valid');
        }

        $stokModel = new StokModel();
        $barang    = $stokModel->getById($idBarang);

        if (!$barang) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Barang tidak ditemukan');
        }
        if ((int)$barang['stok_tersedia'] < $jumlah) {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Stok tidak mencukupi (' . $barang['stok_tersedia'] . ' tersedia)');
        }

        try {
            $this->model->addSukuCadang([
                'id_lk'       => $id,
                'id_barang'   => $idBarang,
                'nama_barang' => $barang['nama'],
                'jumlah'      => $jumlah,
                'satuan'      => $barang['satuan'] ?? 'pcs',
                'keterangan'  => $post['keterangan'] ?? null,
            ]);

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

            return redirect()->to('/ipsrs/lk/' . $id)->with('success', 'Suku cadang berhasil dicatat & stok dikurangi');
        } catch (\Throwable $e) {
            log_message('error', '[LK::addSukuCadang] ' . $e->getMessage());
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Gagal mencatat suku cadang: ' . $e->getMessage());
        }
    }

    public function storeVendor(string $id)
    {
        $lk = $this->model->getById($id);
        if (!$lk) {
            return redirect()->to('/ipsrs/lk')->with('error', 'LK tidak ditemukan');
        }

        $post        = $this->whitelist([
            'id_vendor', 'nama_vendor_baru', 'kontak',
            'tanggal_kirim', 'tanggal_kembali', 'estimasi_selesai', 'keterangan',
        ]);
        $vendorModel = new \App\Models\VendorModel();
        $idVendor    = $post['id_vendor'] ?? '';
        $namaBaru    = trim($post['nama_vendor_baru'] ?? '');

        if ($namaBaru !== '' && !$idVendor) {
            // Create new vendor + link
            try {
                $created   = $vendorModel->create([
                    'nama_vendor' => $namaBaru,
                    'kontak'      => $post['kontak'] ?? null,
                ]);
                $idVendor  = $created['id'] ?? null;
                $namaFinal = $namaBaru;
            } catch (\Throwable $e) {
                log_message('error', '[LK::storeVendor] create vendor: ' . $e->getMessage());
                return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Gagal menambah vendor baru: ' . $e->getMessage());
            }
        } elseif ($idVendor) {
            $vendor    = $vendorModel->getById($idVendor);
            $namaFinal = $vendor['nama_vendor'] ?? '-';
        } else {
            return redirect()->to('/ipsrs/lk/' . $id)->with('error', 'Pilih vendor atau isi nama vendor baru');
        }

        try {
            $this->model->addVendor([
                'id_lk'            => $id,
                'id_vendor'        => $idVendor ?: null,
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

    /** Hitung response time (laporan → survei/posisi). */
    private function calcResponseTime(array $lk, string $next, array &$data): void
    {
        if (!in_array($next, ['Didisposisi', 'Survei']) || empty($data['jam_cek']) || !empty($lk['response_time'])) {
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

        $tanggalSelesai = $data['tanggal_selesai'] ?? date('Y-m-d');
        $jamSelesai     = $data['jam_selesai'] ?? date('H:i');
        $data['tanggal_selesai'] = $tanggalSelesai;
        $data['jam_selesai']     = $jamSelesai;

        $dt = \App\Libraries\Metrics::selisihMenit(
            $lk['tanggal'] ?? date('Y-m-d'), $lk['jam_laporan'] ?? '00:00',
            $tanggalSelesai, $jamSelesai
        );
        if ($dt !== null) {
            $data['down_time'] = $dt;
        }
        if (empty($lk['response_time']) && !empty($data['down_time'])) {
            $data['response_time'] = $data['down_time'];
        }
    }

    /** Sinkronkan status aset mengikuti status LK. */
    private function syncAsetStatus(array $lk, string $next): void
    {
        if (empty($lk['id_aset'])) return;

        $asetStatus = IPSRS::LK_TO_ASET_STATUS[$next] ?? null;
        if ($asetStatus !== null) {
            (new AsetModel())->update($lk['id_aset'], ['status' => $asetStatus]);
        }
    }
}
