<?php

namespace App\Models;

use App\Config\IPSRS;

class LKModel extends BaseModel
{
    protected string $table = 'laporan_kerusakan';

    public function getAll(string $orderBy = 'tanggal'): array
    {
        $rows = $this->qb($this->table)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam_laporan', 'DESC')
            ->get()
            ->getResultArray();
        return $rows;
    }

    public function getByAset(string $idAset): array
    {
        return $this->qb($this->table)
            ->where('id_aset', $idAset)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Suku cadang linked to this LK

    public function getSukuCadang(string $lkId): array
    {
        return $this->qb('detail_suku_cadang_lk')
            ->where('id_lk', $lkId)
            ->get()
            ->getResultArray();
    }

    public function addSukuCadang(array $data): array
    {
        $this->qb('detail_suku_cadang_lk')->insert($data);
        return $data;
    }

    // Vendor / Proses III linked to this LK

    public function getVendor(string $lkId): array
    {
        return $this->qb('detail_vendor_lk')
            ->where('id_lk', $lkId)
            ->get()
            ->getResultArray();
    }

    public function addVendor(array $data): array
    {
        $this->qb('detail_vendor_lk')->insert($data);
        return $data;
    }

    public function nextNoOrder(string $column = 'no_order', string $prefix = '', int $padLen = IPSRS::PAD_LK): string
    {
        return parent::nextNoOrder($column, $prefix ?: IPSRS::PREFIX_LK . date('Ym') . '-', $padLen);
    }
}
