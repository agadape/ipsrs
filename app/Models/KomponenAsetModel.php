<?php

namespace App\Models;

class KomponenAsetModel extends BaseModel
{
    protected string $table = 'komponen_aset';

    public function getAll(string $orderBy = 'nama_komponen'): array
    {
        return $this->qb($this->table)
            ->orderBy('nama_komponen', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getByAset(string $idAset): array
    {
        return $this->qb($this->table)
            ->where('id_aset', $idAset)
            ->orderBy('nama_komponen', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function deleteByAset(string $idAset): void
    {
        $this->qb($this->table)
            ->where('id_aset', $idAset)
            ->delete();
    }
}
