<?php

namespace App\Config;

/**
 * Central configuration for IPSRS application.
 * All magic strings, status values, prefixes, and business rules live here.
 */
class IPSRS
{
    /** Brute-force guard for the public login endpoint. */
    public const LOGIN_MAX_ATTEMPTS = 5;
    public const LOGIN_IP_MAX_ATTEMPTS = 20;
    public const LOGIN_WINDOW_SECONDS = 60;

    // ── SLA ───────────────────────────────────────────────────────────────

    /** Target response time dalam menit (SPO: ≤ 15 menit). */
    public const SLA_RESPONSE_TIME = 15;

    // Laporan Configuration
    public const NAMA_KEPALA = 'Nama Kepala IPSRS, ST., MT.';
    public const NIP_KEPALA  = '19800101 200501 1 001';

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
        'Tersedia',
        'Dipinjam',
        'Dalam Perbaikan',
        'Rusak Berat',
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

    /** Allowed forward transitions for the corrective-maintenance workflow. */
    public const LK_STATUS_TRANSITIONS = [
        'Laporan Masuk'          => ['Didisposisi'],
        'Didisposisi'            => ['Survei'],
        'Survei'                 => ['Dalam Perbaikan', 'Menunggu Suku Cadang', 'Menunggu Vendor', 'Selesai'],
        'Dalam Perbaikan'        => ['Menunggu Suku Cadang', 'Menunggu Vendor', 'Selesai'],
        'Menunggu Suku Cadang'   => ['Dalam Perbaikan'],
        'Menunggu Vendor'        => ['Dalam Perbaikan'],
        'Selesai'                => [],
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
        // The deployed schema only permits five lifecycle values. Waiting
        // remains a LK workflow state while the unit stays unavailable.
        'Menunggu Suku Cadang' => 'Dalam Perbaikan',
        'Menunggu Vendor'      => 'Dalam Perbaikan',
        'Selesai'              => 'Tersedia',
    ];
}
