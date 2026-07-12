<?php

namespace App\Models;

use App\Config\IPSRS;

class AsetModel extends BaseModel
{
    protected string $table = 'aset';

    public function getAll(string $orderBy = 'nama'): array
    {
        return parent::getAll($orderBy);
    }

    public function getRiwayatLokasi(string $idAset): array
    {
        return $this->qb('riwayat_lokasi_aset')
            ->where('id_aset', $idAset)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function insertRiwayatLokasi(array $data): void
    {
        $this->qb('riwayat_lokasi_aset')->insert($data);
    }

    public function nextId(string $prefix = IPSRS::PREFIX_ASET, int $padLen = IPSRS::PAD_ASET): string
    {
        return parent::nextId($prefix, $padLen);
    }
}
