<?php

namespace App\Models;

use App\Config\IPSRS;

class AsetModel extends BaseModel
{
    protected string $table = 'aset';

    public function getAll(string $orderBy = 'nama'): array
    {
        return $this->qb($this->table)
            ->select('aset.*, COUNT(aset_series.id) as total_series')
            ->join('aset_series', 'aset_series.id_aset = aset.id', 'left')
            ->groupBy('aset.id')
            ->orderBy('aset.' . $orderBy, 'ASC')
            ->get()
            ->getResultArray();
    }



    public function nextId(string $prefix = IPSRS::PREFIX_ASET, int $padLen = IPSRS::PAD_ASET): string
    {
        return parent::nextId($prefix, $padLen);
    }
}

