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

    public function getByAset(string $idAsetSeries): array
    {
        return $this->qb($this->table)
            ->where('id_aset_series', $idAsetSeries)
            ->orderBy('nama_komponen', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function deleteByAset(string $idAsetSeries): void
    {
        $this->qb($this->table)
            ->where('id_aset_series', $idAsetSeries)
            ->delete();
    }
}

