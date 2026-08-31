<?php

namespace App\Models;

class MutasiModel extends BaseModel
{
    protected string $table = 'riwayat_lokasi_aset';

    public function getAll(string $orderBy = 'tanggal'): array
    {
        return $this->qb($this->table)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getByAset(string $idAset): array
    {
        return $this->qb($this->table)
            ->where('id_aset_series', $idAset)
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

}

