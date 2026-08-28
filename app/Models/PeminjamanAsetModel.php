<?php
namespace App\Models;

class PeminjamanAsetModel extends BaseModel
{
    protected string $table = "peminjaman_aset";

    public function getActiveByAset(string $idSeries)
    {
        return $this->qb($this->table)
            ->where("id_aset_series", $idSeries)
            ->where("status", "Dipinjam")
            ->orderBy("created_at", "DESC")
            ->get()
            ->getRowArray();
    }
}

