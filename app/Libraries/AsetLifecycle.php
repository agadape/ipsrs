<?php

namespace App\Libraries;

use App\Config\IPSRS;

/** Canonical lifecycle decisions for physical asset series. */
final class AsetLifecycle
{
    public static function canBorrow(?string $status): bool
    {
        return $status === 'Tersedia';
    }

    public static function canReturn(?string $status): bool
    {
        return $status === 'Dipinjam';
    }

    public static function canMarkRusakBerat(?string $status): bool
    {
        return in_array($status, ['Tersedia', 'Dalam Perbaikan'], true);
    }

    public static function canDispose(?string $status): bool
    {
        return $status === 'Rusak Berat';
    }

    public static function canRelocate(?string $status): bool
    {
        return $status === 'Tersedia';
    }

    /**
     * LK status must never automatically revive protected lifecycle states.
     * A borrowed unit keeps its loan state until it is returned.
     */
    public static function canSyncFromLk(?string $status): bool
    {
        return !in_array($status, ['Dihapuskan', 'Rusak Berat', 'Dipinjam'], true);
    }

    /**
     * Status unit yang seharusnya berdasarkan ada/tidaknya LK yang masih terbuka.
     * Mengembalikan null bila status unit dilindungi (Dipinjam, Rusak Berat, Dihapuskan).
     */
    public static function statusFromOpenLk(?string $current, bool $hasOpenLk): ?string
    {
        if (!self::canSyncFromLk($current)) {
            return null;
        }

        return $hasOpenLk ? 'Dalam Perbaikan' : 'Tersedia';
    }

    /** A disposed unit no longer exists operationally, so it cannot receive new LK. */
    public static function canReceiveLk(?string $status): bool
    {
        return $status !== 'Dihapuskan';
    }

    public static function isCanonical(?string $status): bool
    {
        return in_array($status, IPSRS::STATUS_ASET, true);
    }
}
