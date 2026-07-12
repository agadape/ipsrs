<?php

namespace App\Models;

class KategoriAsetModel extends BaseModel
{
    protected string $table = 'kategori_aset';

    public function getAll(string $orderBy = 'nama_kategori'): array
    {
        return parent::getAll($orderBy);
    }
}
