<?php

namespace App\Libraries;

/** Server-side contract for LKP checklist rows. */
final class LkpChecklist
{
    /** @var list<string> */
    public const TYPES = ['Inspeksi', 'Service', 'Pengukuran', 'Teks'];

    /** @param array<int|string, mixed> $items */
    public static function validate(array $items): ?string
    {
        if ($items === []) {
            return 'Minimal satu item checklist wajib diisi.';
        }

        foreach ($items as $item) {
            if (!is_array($item)) {
                return 'Format item checklist tidak valid.';
            }

            $type = (string) ($item['jenis'] ?? '');
            $component = trim((string) ($item['komponen'] ?? ''));
            $hasResult = array_key_exists('hasil', $item) && trim((string) $item['hasil']) !== '';

            if (!in_array($type, self::TYPES, true) || $component === '' || !$hasResult) {
                return 'Setiap item checklist harus memiliki jenis, komponen, dan hasil.';
            }
            if ($type === 'Inspeksi' && !in_array($item['hasil'], ['Baik', 'Tidak'], true)) {
                return 'Hasil inspeksi harus Baik atau Tidak.';
            }
            if ($type === 'Service' && !in_array($item['hasil'], ['Ya', 'Tidak'], true)) {
                return 'Hasil service harus Ya atau Tidak.';
            }
            if ($type === 'Pengukuran' && !is_numeric($item['hasil'])) {
                return 'Hasil pengukuran harus berupa angka.';
            }
        }

        return null;
    }

    /**
     * @param array<int|string, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    public static function toRows(string $idLkp, array $items, callable $newId): array
    {
        $rows = [];
        foreach (array_values($items) as $index => $item) {
            $type = (string) $item['jenis'];
            $textPayload = $type === 'Teks'
                ? json_encode([
                    'hasil' => (string) $item['hasil'],
                    'catatan' => (string) ($item['ket'] ?? ''),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null;

            $rows[] = [
                'id' => $newId(),
                'id_lkp' => $idLkp,
                'no_item' => (int) ($item['no_item'] ?? ($index + 1)),
                'jenis_item' => $type,
                'nama_komponen' => trim((string) $item['komponen']),
                'hasil_inspeksi' => $type === 'Inspeksi' ? (string) $item['hasil'] : null,
                'hasil_service' => $type === 'Service' ? (string) $item['hasil'] : null,
                'nilai_pengukuran' => $type === 'Pengukuran' ? (string) $item['hasil'] : null,
                'satuan' => $type === 'Pengukuran' ? trim((string) ($item['satuan'] ?? '')) : null,
                'keterangan' => $textPayload ?? (($item['ket'] ?? '') !== '' ? (string) $item['ket'] : null),
            ];
        }

        return $rows;
    }

    /** @return array{hasil: string, catatan: string}|null */
    public static function textPayload(?string $value): ?array
    {
        if (!$value) {
            return null;
        }
        $decoded = json_decode($value, true);
        if (!is_array($decoded) || !isset($decoded['hasil'])) {
            return null;
        }

        return [
            'hasil' => (string) $decoded['hasil'],
            'catatan' => (string) ($decoded['catatan'] ?? ''),
        ];
    }
}
