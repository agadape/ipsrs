<?php

namespace App\Models;

use App\Config\IPSRS;

class StokModel extends BaseModel
{
    protected string $table = 'barang_persediaan';

    public function nextBarangId(): string
    {
        return parent::nextId(IPSRS::PREFIX_BARANG, IPSRS::PAD_BARANG);
    }

    public function catatTransaksi(array $txData): array
    {
        // Insert riwayat transaksi
        if (!isset($txData['id'])) {
            $txData['id'] = $this->generateUUID();
        }
        $this->qb('riwayat_transaksi_stok')->insert($txData);
        $this->throwIfError();
        $row = $txData;

        // Optimistic locking: read current stock, update with WHERE stok = old
        $idBarang = $txData['id_barang'];
        $jumlah   = (int) ($txData['jumlah'] ?? 0);
        $jenis    = $txData['jenis'] ?? '';
        $maxRetries = 5;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            // Baca stok saat ini
            $barang = $this->qb($this->table)
                ->select('stok_tersedia')
                ->where('id', $idBarang)
                ->get()
                ->getRowArray();

            if (!$barang) {
                throw new \RuntimeException('Gagal membaca stok barang');
            }

            $current = (int) ($barang['stok_tersedia'] ?? 0);

            $newStok = match ($jenis) {
                'Masuk'  => $current + $jumlah,
                'Keluar' => max(0, $current - $jumlah),
                default  => $current,
            };

            // Conditional update: WHERE id = X AND stok_tersedia = current
            $builder = $this->qb($this->table);
            $builder->where('id', $idBarang);
            $builder->where('stok_tersedia', $current);
            $builder->update(['stok_tersedia' => $newStok]);
            $affected = $this->conn->affectedRows();

            if ($affected > 0) {
                return $row; // Success
            }

            // 0 rows affected → stock changed by another request → retry
            usleep(30000); // 30ms
        }

        throw new \RuntimeException("Gagal update stok setelah {$maxRetries} percobaan (konkurensi tinggi)");
    }

    public function getRiwayat(string $idBarang = ''): array
    {
        $builder = $this->qb('riwayat_transaksi_stok');

        if ($idBarang !== '') {
            $builder->where('id_barang', $idBarang);
        }

        $rows = $builder
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $rows;
    }
}
