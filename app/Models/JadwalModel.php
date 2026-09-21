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

    /** Atomically reserve an unfinished schedule for one LKP submission. */
    public function claimForCompletion(string $id): bool
    {
        $this->qb($this->table)
            ->where('id', $id)
            ->where('status', IPSRS::STATUS_JADWAL[0])
            ->update(['status' => 'Diproses']);
        $this->throwIfError();

        return $this->conn->affectedRows() === 1;
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

