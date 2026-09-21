<?php

namespace App\Libraries;

final class AccessPolicy
{
    public static function role(?string $role): string
    {
        return strtolower(trim((string) $role));
    }

    public static function hasRole(?string $role, array $allowed): bool
    {
        return in_array(self::role($role), $allowed, true);
    }

    /** Legacy records use display names until actor foreign keys are migrated. */
    public static function canViewLk(array $lk, ?string $role, ?string $userName): bool
    {
        $role = self::role($role);
        if ($role === 'admin') {
            return true;
        }

        $userName = trim((string) $userName);
        if ($userName === '') {
            return false;
        }

        if ($role === 'pelapor') {
            return hash_equals($userName, (string) ($lk['pelapor'] ?? ''));
        }

        return $role === 'teknisi' && (
            ($lk['status'] ?? '') === 'Laporan Masuk'
            || hash_equals($userName, (string) ($lk['teknisi'] ?? ''))
        );
    }

    public static function canManageLk(array $lk, ?string $role, ?string $userName): bool
    {
        return self::role($role) === 'admin'
            || (self::role($role) === 'teknisi'
                && hash_equals(trim((string) $userName), (string) ($lk['teknisi'] ?? '')));
    }

    public static function canAccessPreventive(array $jadwal, ?string $role, ?string $userName): bool
    {
        return self::role($role) === 'admin'
            || (self::role($role) === 'teknisi'
                && hash_equals(trim((string) $userName), (string) ($jadwal['teknisi'] ?? '')));
    }
}
