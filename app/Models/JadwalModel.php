<?php

namespace App\Models;

use App\Config\IPSRS;

class JadwalModel extends BaseModel
{
    protected string $table = 'jadwal_preventif';

    public function getAll(string $orderBy = 'tanggal'): array
    {
        return parent::getAll($orderBy);
    }

    public function markSelesai(string $id): array
    {
        return $this->update($id, ['status' => IPSRS::STATUS_JADWAL[1]]);
    }

    public function getByAset(string $idAset): array
    {
        return $this->qb($this->table)
            ->where('aset', $idAset)
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();
    }
}
