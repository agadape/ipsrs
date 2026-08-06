<?php

namespace App\Models;

class AsetSeriesModel extends BaseModel
{
    protected string $table = 'aset_series';

    public function getAllWithParent(): array
    {
        return $this->qb($this->table)
            ->select('aset_series.*, aset.nama, aset.jenis, aset.kategori')
            ->join('aset', 'aset.id = aset_series.id_aset')
            ->orderBy('aset.nama', 'ASC')
            ->orderBy('aset_series.nomor_aset', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getByParent(string $idAset): array
    {
        return $this->qb($this->table)
            ->where('id_aset', $idAset)
            ->orderBy('nomor_aset', 'ASC')
            ->get()
            ->getResultArray();
    }
}

