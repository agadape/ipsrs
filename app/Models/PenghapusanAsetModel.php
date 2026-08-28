<?php
namespace App\Models;

class PenghapusanAsetModel extends BaseModel
{
    protected string \ = 'penghapusan_aset';

    public function getByAset(string \)
    {
        return \->qb(\->table)
            ->where('id_aset_series', \)
            ->get()
            ->getRowArray();
    }
}

