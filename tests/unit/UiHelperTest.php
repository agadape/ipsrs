<?php

use CodeIgniter\Test\CIUnitTestCase;

// Load ui_helper for testing (CI4 autoloader doesn't load custom helpers in tests)
require_once APPPATH . 'Helpers/ui_helper.php';

/**
 * @internal
 */
final class UiHelperTest extends CIUnitTestCase
{
    // ── tgl() ─────────────────────────────────────────────────────

    public function testTglNormalDate(): void
    {
        $this->assertSame('19/05/2026', tgl('2026-05-19'));
    }

    public function testTglWithCustomFormat(): void
    {
        $this->assertSame('19 May 2026', tgl('2026-05-19', 'd M Y'));
    }

    public function testTglNullReturnsDash(): void
    {
        $this->assertSame('-', tgl(null));
    }

    public function testTglEmptyStringReturnsDash(): void
    {
        $this->assertSame('-', tgl(''));
    }

    public function testTglInvalidDateReturnsDash(): void
    {
        $this->assertSame('-', tgl('bukan-tanggal'));
    }

    // ── jam() ─────────────────────────────────────────────────────

    public function testJamNormalTime(): void
    {
        $this->assertSame('14:30', jam('14:30'));
    }

    public function testJamNullReturnsDash(): void
    {
        $this->assertSame('-', jam(null));
    }

    public function testJamEmptyStringReturnsDash(): void
    {
        $this->assertSame('-', jam(''));
    }

    // ── status_lk_badge() ─────────────────────────────────────────

    public function testBadgeSelesai(): void
    {
        $this->assertStringContainsString('emerald', status_lk_badge('Selesai'));
    }

    public function testBadgeLaporanMasuk(): void
    {
        $this->assertStringContainsString('gray', status_lk_badge('Laporan Masuk'));
    }

    public function testBadgeDefault(): void
    {
        $this->assertStringContainsString('gray', status_lk_badge('StatusTidakDikenal'));
    }

    // ── status_aset_badge() ───────────────────────────────────────

    public function testBadgeAsetAktif(): void
    {
        $this->assertStringContainsString('emerald', status_aset_badge('Aktif'));
    }

    public function testBadgeAsetDihapuskan(): void
    {
        $this->assertStringContainsString('red', status_aset_badge('Dihapuskan'));
    }

    // ── status_stok_badge() ───────────────────────────────────────

    public function testBadgeStokAman(): void
    {
        $this->assertStringContainsString('emerald', status_stok_badge('Aman'));
    }

    public function testBadgeStokHabis(): void
    {
        $this->assertStringContainsString('red', status_stok_badge('Habis'));
    }

    // ── kode_badge() ──────────────────────────────────────────────

    public function testBadgeKodeAC(): void
    {
        $this->assertStringContainsString('indigo', kode_badge('AC'));
    }

    public function testBadgeKodePR(): void
    {
        $this->assertStringContainsString('slate', kode_badge('PR'));
    }

    public function testBadgeKodeDefault(): void
    {
        $this->assertStringContainsString('gray', kode_badge('XX'));
    }
}
