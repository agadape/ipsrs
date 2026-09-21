<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/** Records one component transfer as one atomic business event. */
final class KanibalTransfer
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
    }

    /** @param array<string, string|null> $data */
    public function record(array $data): void
    {
        $componentName = trim((string) ($data['nama_komponen'] ?? ''));
        if ($componentName === '' || mb_strlen($componentName) > 200) {
            throw new \RuntimeException('Komponen donor tidak valid.');
        }

        $condition = (string) ($data['kondisi_komponen'] ?? '');
        if (!in_array($condition, ['Baik', 'Kurang Baik', 'Rusak'], true)) {
            throw new \RuntimeException('Kondisi komponen tidak valid.');
        }

        $this->db->transBegin();
        try {
            $lk = $this->db->table('laporan_kerusakan')->where('id', $data['id_lk'] ?? '')->get()->getRowArray();
            if (!$lk || ($lk['id_aset_series'] ?? '') !== ($data['id_series_penerima'] ?? '')) {
                throw new \RuntimeException('LK tidak cocok dengan aset penerima.');
            }
            if (!in_array($lk['status'] ?? '', ['Survei', 'Dalam Perbaikan', 'Menunggu Suku Cadang', 'Menunggu Vendor'], true)) {
                throw new \RuntimeException('Kanibal hanya dapat dicatat pada LK yang sedang diproses.');
            }

            $donor = $this->db->table('aset_series')->where('id', $data['id_series_donor'] ?? '')->get()->getRowArray();
            if (!$donor || ($donor['status'] ?? '') !== 'Rusak Berat') {
                throw new \RuntimeException('Donor harus berupa aset berstatus Rusak Berat.');
            }
            $penerima = $this->db->table('aset_series')->where('id', $data['id_series_penerima'] ?? '')->get()->getRowArray();
            if (!$penerima || in_array($penerima['status'] ?? '', ['Rusak Berat', 'Dihapuskan'], true)) {
                throw new \RuntimeException('Aset penerima tidak layak menerima komponen kanibal.');
            }

            $donorComponent = $this->findComponent(
                $this->db->table('komponen_aset')->where('id_aset_series', $data['id_series_donor'])->get()->getResultArray(),
                $componentName,
                true,
            );
            if (!$donorComponent) {
                throw new \RuntimeException('Komponen tersedia pada aset donor tidak ditemukan.');
            }

            $this->db->table('riwayat_kanibal')->insert([
                'id' => $data['id_riwayat'], 'no_order_lk' => $lk['no_order'],
                'id_series_donor' => $data['id_series_donor'], 'id_series_penerima' => $data['id_series_penerima'],
                'nama_komponen' => $donorComponent['nama_komponen'], 'kondisi_komponen' => $condition,
                'tanggal' => date('Y-m-d'), 'petugas' => $data['petugas'],
                'disetujui_oleh' => $data['disetujui_oleh'], 'keterangan' => $data['keterangan'] ?? null,
            ]);
            $this->db->table('detail_suku_cadang_lk')->insert([
                'id' => $data['id_detail'], 'id_lk' => $lk['id'], 'id_barang' => null,
                'sumber' => 'Kanibal', 'nama_barang' => $donorComponent['nama_komponen'],
                'jumlah' => 1, 'satuan' => 'pcs',
                'keterangan' => 'Dari aset series: ' . $data['id_series_donor'],
            ]);

            // A conditional write prevents a second concurrent request from
            // claiming the same physical donor component.
            $this->db->table('komponen_aset')->where('id', $donorComponent['id'])
                ->where('kondisi !=', 'Tidak Ada')->update(['kondisi' => 'Tidak Ada']);
            if ($this->db->affectedRows() !== 1) {
                throw new \RuntimeException('Komponen donor sudah digunakan oleh permintaan lain.');
            }

            $recipientComponent = $this->findComponent(
                $this->db->table('komponen_aset')->where('id_aset_series', $data['id_series_penerima'])->get()->getResultArray(),
                $componentName,
            );
            $recipientData = [
                'kondisi' => $condition, 'asal' => 'Hasil Kanibal',
                'id_riwayat_kanibal' => $data['id_riwayat'], 'tanggal_dicatat' => date('Y-m-d'),
                'keterangan' => 'Dipasang dari aset series: ' . $data['id_series_donor'],
            ];
            if ($recipientComponent) {
                $this->db->table('komponen_aset')->where('id', $recipientComponent['id'])->update($recipientData);
            } else {
                $this->db->table('komponen_aset')->insert($recipientData + [
                    'id' => $data['id_komponen_penerima'], 'id_aset_series' => $data['id_series_penerima'],
                    'nama_komponen' => $donorComponent['nama_komponen'],
                ]);
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Pencatatan kanibal tidak dapat diselesaikan.');
            }
            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /** @param array<int, array<string, mixed>> $components */
    private function findComponent(array $components, string $name, bool $mustBeAvailable = false): ?array
    {
        $needle = mb_strtolower(trim($name));
        foreach ($components as $component) {
            if (mb_strtolower(trim((string) ($component['nama_komponen'] ?? ''))) !== $needle) {
                continue;
            }
            if ($mustBeAvailable && ($component['kondisi'] ?? '') === 'Tidak Ada') {
                continue;
            }
            return $component;
        }
        return null;
    }
}
