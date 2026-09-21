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

    /**
     * Applies one stock movement. The caller owns the surrounding database
     * transaction, so balance and ledger either persist together or roll back.
     */
    public function catatTransaksi(array $txData): array
    {
        $idBarang = (string) ($txData['id_barang'] ?? '');
        $jumlah   = (int) ($txData['jumlah'] ?? 0);
        $jenis    = (string) ($txData['jenis'] ?? '');
        if ($idBarang === '' || $jumlah <= 0 || !in_array($jenis, ['Masuk', 'Keluar'], true)) {
            throw new \InvalidArgumentException('Transaksi stok tidak valid.');
        }

        // The shortage check is part of the conditional write, not a stale
        // PHP read. Therefore a concurrent debit cannot take the balance below 0.
        $builder = $this->qb($this->table)->where('id', $idBarang);
        if ($jenis === 'Keluar') {
            $builder->where('stok_tersedia >=', $jumlah)
                ->set('stok_tersedia', 'stok_tersedia - ' . $jumlah, false)
                ->update();
        } else {
            $builder->set('stok_tersedia', 'stok_tersedia + ' . $jumlah, false)
                ->update();
        }
        $this->throwIfError();

        if ($this->conn->affectedRows() !== 1) {
            throw new \RuntimeException(
                $jenis === 'Keluar'
                    ? 'Stok tidak mencukupi atau barang tidak ditemukan.'
                    : 'Barang tidak ditemukan.'
            );
        }

        $txData['id'] ??= $this->generateUUID();
        $this->qb('riwayat_transaksi_stok')->insert($txData);
        $this->throwIfError();

        return $txData;
    }

    public function getRiwayat(string $idBarang = ''): array
    {
        $builder = $this->qb('riwayat_transaksi_stok');

        if ($idBarang !== '') {
            $builder->where('id_barang', $idBarang);
        }

        return $builder
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
