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

    /** LK status must never automatically revive protected lifecycle states. */
    public static function canSyncFromLk(?string $status): bool
    {
        return !in_array($status, ['Dihapuskan', 'Rusak Berat'], true);
    }

    public static function isCanonical(?string $status): bool
    {
        return in_array($status, IPSRS::STATUS_ASET, true);
    }
}
