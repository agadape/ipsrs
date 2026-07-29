<?php

namespace App\Models;

use App\Config\IPSRS;

class AsetModel extends BaseModel
{
    protected string $table = 'aset';

    public function getAll(string $orderBy = 'nama'): array
    {
        return parent::getAll($orderBy);
    }



    public function nextId(string $prefix = IPSRS::PREFIX_ASET, int $padLen = IPSRS::PAD_ASET): string
    {
        return parent::nextId($prefix, $padLen);
    }
}

