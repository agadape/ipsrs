<?php

/**
 * UI helper — fungsi tampilan bersama (badge status & format tanggal).
 * Memusatkan pemetaan status→kelas Tailwind yang sebelumnya diduplikasi di banyak view.
 */

if (! function_exists('status_lk_badge')) {
    function status_lk_badge(?string $status): string
    {
        return match ($status) {
            'Selesai'              => 'badge bg-emerald-100 text-emerald-700',
            'Dalam Perbaikan'      => 'badge bg-indigo-100 text-indigo-700',
            'Survei'               => 'badge bg-sky-100 text-sky-700',
            'Didisposisi'          => 'badge bg-amber-100 text-amber-700',
            'Menunggu Suku Cadang' => 'badge bg-amber-100 text-amber-700',
            'Menunggu Vendor'      => 'badge bg-orange-100 text-orange-700',
            'Laporan Masuk'        => 'badge bg-gray-100 text-gray-600',
            default                => 'badge bg-gray-100 text-gray-600',
        };
    }
}

if (! function_exists('status_aset_badge')) {
    function status_aset_badge(?string $status): string
    {
        return match ($status) {
            'Aktif'                => 'badge bg-emerald-100 text-emerald-700',
            'Tidak Aktif'          => 'badge bg-gray-100 text-gray-500',
            'Dalam Perbaikan'      => 'badge bg-indigo-100 text-indigo-700',
            'Menunggu Suku Cadang' => 'badge bg-amber-100 text-amber-700',
            'Menunggu Vendor'      => 'badge bg-orange-100 text-orange-700',
            'Usulan Penghapusan'   => 'badge bg-red-100 text-red-600',
            'Dihapuskan'           => 'badge bg-red-200 text-red-700',
            default                => 'badge bg-gray-100 text-gray-500',
        };
    }
}

if (! function_exists('status_stok_badge')) {
    function status_stok_badge(?string $status): string
    {
        return match ($status) {
            'Aman'    => 'badge bg-emerald-100 text-emerald-700',
            'Menipis' => 'badge bg-amber-100 text-amber-700',
            'Habis'   => 'badge bg-red-100 text-red-600',
            default   => 'badge bg-gray-100 text-gray-500',
        };
    }
}

if (! function_exists('kode_badge')) {
    function kode_badge(?string $kode): string
    {
        return match ($kode) {
            'AC'    => 'badge bg-indigo-50 text-indigo-700',
            'PR'    => 'badge bg-slate-100 text-slate-700',
            'NM'    => 'badge bg-yellow-100 text-yellow-700',
            'AL'    => 'badge bg-orange-100 text-orange-700',
            default => 'badge bg-gray-100 text-gray-600',
        };
    }
}

if (! function_exists('tgl')) {
    /** Format tanggal aman (kembalikan '-' bila kosong / tidak valid). */
    function tgl(?string $date, string $format = 'd/m/Y'): string
    {
        if (empty($date)) {
            return '-';
        }
        $ts = strtotime($date);
        return $ts ? date($format, $ts) : '-';
    }
}

if (! function_exists('jam')) {
    /** Format jam aman — kembalikan '-' bila kosong. */
    function jam(?string $time): string
    {
        if (empty($time)) return '-';
        $ts = strtotime($time);
        return $ts ? date('H:i', $ts) : '-';
    }
}
