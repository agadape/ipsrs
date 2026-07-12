<?php

namespace App\Models;

class VendorModel extends BaseModel
{
    protected string $table = 'vendor';

    public function getAll(string $orderBy = 'nama_vendor'): array
    {
        return parent::getAll($orderBy);
    }
}
