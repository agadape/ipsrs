<?php

namespace App\Models;

class TemplateChecklistModel extends BaseModel
{
    protected string $table = 'template_checklist';

    public function getAll(string $orderBy = 'kategori'): array
    {
        return $this->qb($this->table)
            ->orderBy('kategori', 'ASC')
            ->orderBy('no_item', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getByKategori(string $kategori): array
    {
        return $this->qb($this->table)
            ->where('kategori', $kategori)
            ->orderBy('no_item', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function kategoriList(): array
    {
        $rows = $this->getAll();
        return array_values(array_unique(array_map(fn($t) => $t['kategori'] ?? '', $rows)));
    }
}
