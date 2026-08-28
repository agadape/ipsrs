<?php
namespace App\Models;

class PenghapusanAsetModel extends BaseModel
{
    protected string $table = "penghapusan_aset";

    public function getByAset(string $idSeries)
    {
        return $this->qb($this->table)
            ->where("id_aset_series", $idSeries)
            ->get()
            ->getRowArray();
    }
}

