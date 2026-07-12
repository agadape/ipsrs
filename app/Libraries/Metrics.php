<?php

namespace App\Libraries;

/**
 * Metrics — pure computational logic of the IPSRS system.
 *
 * Fungsi-fungsi di sini bersifat deterministik (tanpa akses basis data),
 * sehingga menjadi inti logika bisnis yang dapat diuji secara unit (unit test).
 */
class Metrics
{
    /**
     * Tentukan status stok suatu barang.
     * Aturan: stok ≤ 0 → "Habis"; stok ≤ minimum → "Menipis"; selain itu "Aman".
     */
    public static function statusStok(int $tersedia, int $minimum): string
    {
        if ($tersedia <= 0)        return 'Habis';
        if ($tersedia <= $minimum) return 'Menipis';
        return 'Aman';
    }

    /**
     * Hitung selisih waktu dalam menit antara dua titik (tanggal + jam).
     * Mengembalikan null bila salah satu waktu tidak valid atau waktu akhir
     * mendahului waktu mulai. Dipakai untuk Response Time dan Down Time LK.
     */
    public static function selisihMenit(
        string $tanggalMulai, string $jamMulai,
        string $tanggalAkhir, string $jamAkhir
    ): ?int {
        $mulai = strtotime("{$tanggalMulai} {$jamMulai}");
        $akhir = strtotime("{$tanggalAkhir} {$jamAkhir}");
        if (!$mulai || !$akhir || $akhir < $mulai) {
            return null;
        }
        return (int) (($akhir - $mulai) / 60);
    }

    /**
     * Hasilkan nomor urut berikutnya dari sebuah nomor ber-prefix.
     * Contoh: ("A-00012", "A-", 5) → "A-00013"; (null, "B-", 3) → "B-001".
     */
    public static function nextSequential(?string $last, string $prefix, int $pad): string
    {
        if ($last === null || $last === '') {
            return $prefix . str_pad('1', $pad, '0', STR_PAD_LEFT);
        }
        $num = (int) substr($last, strlen($prefix));
        return $prefix . str_pad((string) ($num + 1), $pad, '0', STR_PAD_LEFT);
    }

    /**
     * Tentukan apakah Response Time memenuhi SLA (≤ 15 menit secara default).
     */
    public static function memenuhiSla(?int $responseTimeMenit, int $targetMenit = 15): bool
    {
        return $responseTimeMenit !== null && $responseTimeMenit <= $targetMenit;
    }
}
