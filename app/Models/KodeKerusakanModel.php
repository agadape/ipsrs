<?php

namespace App\Models;

class KodeKerusakanModel extends BaseModel
{
    protected string $table = 'kode_kerusakan';

    public function getAll(string $orderBy = 'kode'): array
    {
        return parent::getAll($orderBy);
    }
}
