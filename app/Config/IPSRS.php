<?php

namespace App\Config;

/**
 * Central configuration for IPSRS application.
 * All magic strings, status values, prefixes, and business rules live here.
 */
class IPSRS
{
    // ── SLA ───────────────────────────────────────────────────────────────

    /** Target response time dalam menit (SPO: ≤ 15 menit). */
    public const SLA_RESPONSE_TIME = 15;

    // ── ID Prefixes ───────────────────────────────────────────────────────

    public const PREFIX_ASET  = 'A-';
    public const PREFIX_BARANG = 'B-';
    public const PREFIX_LK    = 'LK-';
    public const PREFIX_LKP   = 'LKP-';

    public const PAD_ASET   = 5;  // A-00001
    public const PAD_BARANG = 3;  // B-001
    public const PAD_LK     = 4;  // LK-202607-0001
    public const PAD_LKP    = 4;  // LKP-202607-0001

    // ── Aset ──────────────────────────────────────────────────────────────
    // Jenis & kondisi adalah enum tetap (config). Kategori dikelola via tabel 'kategori_aset' (master data).

    public const JENIS_ASET = [
        'Sarana',
        'Prasarana',
        'Alat Non Medis',
    ];

    public const KONDISI_ASET = [
        'Baik',
        'Kurang Baik',
        'Rusak Ringan',
        'Rusak Berat',
    ];

    public const STATUS_ASET = [
        'Aktif',
        'Dalam Perbaikan',
        'Menunggu Suku Cadang',
        'Menunggu Vendor',
        'Tidak Aktif',
        'Di Gudang',
        'Kanibal',
        'Usulan Penghapusan',
        'Dibuang',
        'Dihapuskan',
    ];

    // ── Laporan Kerusakan ────────────────────────────────────────────────

    // Kode kerusakan sekarang dikelola via tabel 'kode_kerusakan' (master data)
    // Lihat: /ipsrs/kode-kerusakan

    public const STATUS_LK = [
        'Laporan Masuk',
        'Didisposisi',
        'Survei',
        'Dalam Perbaikan',
        'Menunggu Suku Cadang',
        'Menunggu Vendor',
        'Selesai',
    ];

    /** Status LK yang menandakan perlu respons teknisi. */
    public const STATUS_LK_BELUM_DISURVEI = ['Laporan Masuk', 'Didisposisi'];

    /** Status LK yang menunggu suku cadang atau vendor. */
    public const STATUS_LK_MENUNGGU = ['Menunggu Suku Cadang', 'Menunggu Vendor'];

    // ── Preventif ─────────────────────────────────────────────────────────

    public const STATUS_JADWAL = [
        'Belum',
        'Selesai',
    ];

    public const HASIL_PEMERIKSAAN = [
        'Siap Pakai',
        'Perlu Perbaikan',
    ];

    // ── Status Sinkronisasi (LK → Aset) ──────────────────────────────────

    /**
     * Map status LK ke status aset yang sesuai.
     * digunakan di LK::syncAsetStatus().
     */
    public const LK_TO_ASET_STATUS = [
        'Didisposisi'          => 'Dalam Perbaikan',
        'Survei'               => 'Dalam Perbaikan',
        'Dalam Perbaikan'      => 'Dalam Perbaikan',
        'Menunggu Suku Cadang' => 'Menunggu Suku Cadang',
        'Menunggu Vendor'      => 'Tidak Aktif',
        'Selesai'              => 'Aktif',
    ];
}
