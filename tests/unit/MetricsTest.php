<?php

use App\Libraries\Metrics;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class MetricsTest extends CIUnitTestCase
{
    // ── statusStok ─────────────────────────────────────────────
    public function testStatusStokHabisSaatNol(): void
    {
        $this->assertSame('Habis', Metrics::statusStok(0, 5));
    }

    public function testStatusStokHabisSaatNegatif(): void
    {
        $this->assertSame('Habis', Metrics::statusStok(-2, 5));
    }

    public function testStatusStokMenipisSaatSamaDenganMinimum(): void
    {
        $this->assertSame('Menipis', Metrics::statusStok(5, 5));
    }

    public function testStatusStokMenipisSaatDiBawahMinimum(): void
    {
        $this->assertSame('Menipis', Metrics::statusStok(3, 5));
    }

    public function testStatusStokAmanSaatDiAtasMinimum(): void
    {
        $this->assertSame('Aman', Metrics::statusStok(10, 5));
    }

    // ── selisihMenit ───────────────────────────────────────────
    public function testSelisihMenitNormal(): void
    {
        $this->assertSame(12, Metrics::selisihMenit('2026-05-19', '08:35', '2026-05-19', '08:47'));
    }

    public function testSelisihMenitNolSaatWaktuSama(): void
    {
        $this->assertSame(0, Metrics::selisihMenit('2026-05-19', '08:35', '2026-05-19', '08:35'));
    }

    public function testSelisihMenitLintasHari(): void
    {
        // 23:50 → besok 00:10 = 20 menit
        $this->assertSame(20, Metrics::selisihMenit('2026-05-19', '23:50', '2026-05-20', '00:10'));
    }

    public function testSelisihMenitNullSaatAkhirMendahuluiMulai(): void
    {
        $this->assertNull(Metrics::selisihMenit('2026-05-19', '09:00', '2026-05-19', '08:00'));
    }

    public function testSelisihMenitNullSaatWaktuTidakValid(): void
    {
        $this->assertNull(Metrics::selisihMenit('bukan-tanggal', '08:00', '2026-05-19', '09:00'));
    }

    // ── nextSequential ─────────────────────────────────────────
    public function testNextSequentialDariNull(): void
    {
        $this->assertSame('B-001', Metrics::nextSequential(null, 'B-', 3));
    }

    public function testNextSequentialDariKosong(): void
    {
        $this->assertSame('A-00001', Metrics::nextSequential('', 'A-', 5));
    }

    public function testNextSequentialIncrementBiasa(): void
    {
        $this->assertSame('A-00013', Metrics::nextSequential('A-00012', 'A-', 5));
    }

    public function testNextSequentialRolloverDigit(): void
    {
        $this->assertSame('A-00100', Metrics::nextSequential('A-00099', 'A-', 5));
    }

    public function testNextSequentialNoOrderLk(): void
    {
        $this->assertSame('LK-202605-0043', Metrics::nextSequential('LK-202605-0042', 'LK-202605-', 4));
    }

    // ── memenuhiSla ────────────────────────────────────────────
    public function testMemenuhiSlaTepatBatas(): void
    {
        $this->assertTrue(Metrics::memenuhiSla(15));
    }

    public function testMemenuhiSlaDiBawahBatas(): void
    {
        $this->assertTrue(Metrics::memenuhiSla(12));
    }

    public function testTidakMemenuhiSlaDiAtasBatas(): void
    {
        $this->assertFalse(Metrics::memenuhiSla(37));
    }

    public function testTidakMemenuhiSlaSaatNull(): void
    {
        $this->assertFalse(Metrics::memenuhiSla(null));
    }
}
