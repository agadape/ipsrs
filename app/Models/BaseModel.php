<?php

namespace App\Models;


/**
 * Parent class for all IPSRS models.
 * Uses CI4 QueryBuilder (MySQL) instead of Supabase REST.
 * Does NOT extend CI4\Model to avoid signature conflicts.
 */
abstract class BaseModel
{
    protected string $table = '';

    /** CI4 DB connection — exposed for cross-table queries in child models. */
    protected $conn;

    public function __construct()
    {
        $this->conn = \Config\Database::connect();
    }

    /** Shorthand: get QueryBuilder for a specific table. */
    protected function qb(string $table): \CodeIgniter\Database\BaseBuilder
    {
        return $this->conn->table($table);
    }

    // ── Common CRUD ───────────────────────────────────────────────────────

    /** Ambil semua baris, diurutkan berdasarkan kolom $orderBy (asc). */
    public function getAll(string $orderBy = 'id'): array
    {
        return $this->qb($this->table)
            ->orderBy($orderBy, 'ASC')
            ->get()
            ->getResultArray();
    }

    /** Ambil satu baris berdasarkan id (null bila tidak ada). */
    public function find(string $id): ?array
    {
        $row = $this->qb($this->table)
            ->where('id', $id)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    /** Alias find(). */
    public function getById(string $id): ?array
    {
        return $this->find($id);
    }

    /** Insert baris baru. Returns the inserted row. Throws on error. */
    public function create(array $data): array
    {
        if (!isset($data['id'])) {
            $data['id'] = $this->generateUUID();
        }
        $this->qb($this->table)->insert($data);
        $this->throwIfError();
        return $this->find($data['id']) ?? $data;
    }

    /**
     * Insert dengan retry jika ID duplicate (race condition pada nextId/nextNoOrder).
     * Callback $genId harus mengembalikan ID baru setiap kali dipanggil.
     * Max 3 percobaan untuk mencegah infinite loop.
     */
    public function createWithRetry(array $data, callable $genId, string $idKey = 'id', int $maxRetries = 3): array
    {
        $lastException = null;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $data[$idKey] = $genId();
                if (!isset($data['id']) && $idKey !== 'id') {
                    $data['id'] = $this->generateUUID();
                }
                $this->qb($this->table)->insert($data);
                $this->throwIfError();
                return $this->find($data['id'] ?? $data[$idKey]) ?? $data;
            } catch (\RuntimeException $e) {
                $lastException = $e;
                usleep(50000); // 50ms
                continue;
            }
        }
        throw $lastException ?? new \RuntimeException("createWithRetry failed after {$maxRetries} attempts");
    }

    /** Update baris berdasarkan id. Returns the updated row. Throws on error. */
    public function update(string $id, array $data): array
    {
        $this->qb($this->table)->where('id', $id)->update($data);
        $this->throwIfError();
        return $this->find($id) ?? $data;
    }

    /** Hapus baris berdasarkan id. Throws on error. */
    public function delete(string $id): void
    {
        $this->qb($this->table)->where('id', $id)->delete();
        $this->throwIfError();
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /** Cek apakah terjadi error pada query terakhir. */
    protected function isError(mixed $response = null): bool
    {
        $error = $this->conn->error();
        return !empty($error['code']);
    }

    /** Throw RuntimeException jika ada error pada query terakhir. */
    protected function throwIfError(): void
    {
        $error = $this->conn->error();
        if (!empty($error['code'])) {
            $table = $this->table;
            throw new \RuntimeException(
                "MySQL error on [{$table}]: ({$error['code']}) {$error['message']}"
            );
        }
    }

    /** No-op for backward compat (Supabase had extractRow). */
    protected function extractRow(mixed $response): array
    {
        if (is_array($response)) {
            return isset($response[0]) && is_array($response[0]) ? $response[0] : $response;
        }
        return [];
    }

    /**
     * Generate next sequential ID with a given prefix, e.g. A-00001.
     * Uses display column (nomor_aset, no_barang, etc.) to find the last number.
     */
    protected function nextId(string $prefix = '', int $padLen = 5): string
    {
        // Determine which display column to use based on table
        $displayCol = match ($this->table) {
            'aset'             => 'nomor_aset',
            'barang_persediaan' => 'no_barang',
            default            => 'id',
        };

        $last = $this->qb($this->table)
            ->select($displayCol)
            ->like($displayCol, $prefix . '%', 'right')
            ->orderBy($displayCol, 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $lastVal = $last[$displayCol] ?? null;
        return \App\Libraries\Metrics::nextSequential($lastVal, $prefix, $padLen);
    }

    /**
     * Generate nomor urut berbasis tanggal, e.g. LK-202607-0001.
     */
    protected function nextNoOrder(string $column = 'no_order', string $prefix = '', int $padLen = 4): string
    {
        if ($prefix === '') {
            $prefix = strtoupper(substr($this->table, 0, 2)) . '-' . date('Ym') . '-';
        }
        $last = $this->qb($this->table)
            ->select($column)
            ->like($column, $prefix . '%', 'right')
            ->orderBy($column, 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $lastVal = $last[$column] ?? null;
        return \App\Libraries\Metrics::nextSequential($lastVal, $prefix, $padLen);
    }

    /** Helper to generate UUID v4 */
    public function generateUUID(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
