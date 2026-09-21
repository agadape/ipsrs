<?php

namespace Tests\Unit;

use App\Libraries\ReportPeriod;
use CodeIgniter\Test\CIUnitTestCase;
use DateTimeImmutable;

final class ReportPeriodTest extends CIUnitTestCase
{
    public function testWeekUsesTheCurrentMondayToSundayRange(): void
    {
        $range = ReportPeriod::describe('minggu', new DateTimeImmutable('2026-09-16 10:00:00'));

        self::assertSame('2026-09-14', $range['start']);
        self::assertSame('2026-09-20', $range['end']);
        self::assertSame('minggu', $range['key']);
    }

    public function testRowsUseInclusiveBoundariesWithoutLeakingOtherWeeks(): void
    {
        $range = ReportPeriod::describe('minggu', new DateTimeImmutable('2026-09-16'));
        $rows = [
            ['id' => 'before', 'tanggal' => '2026-09-13'],
            ['id' => 'first', 'tanggal' => '2026-09-14'],
            ['id' => 'last', 'tanggal' => '2026-09-20'],
            ['id' => 'after', 'tanggal' => '2026-09-21'],
        ];

        $filtered = ReportPeriod::filterRows($rows, 'tanggal', $range);

        self::assertSame(['first', 'last'], array_column($filtered, 'id'));
    }

    public function testMonthAndYearAreCalendarBoundaries(): void
    {
        $anchor = new DateTimeImmutable('2026-09-16');

        self::assertSame(['2026-09-01', '2026-09-30'], array_values(array_intersect_key(ReportPeriod::describe('bulan', $anchor), array_flip(['start', 'end']))));
        self::assertSame(['2026-01-01', '2026-12-31'], array_values(array_intersect_key(ReportPeriod::describe('tahun', $anchor), array_flip(['start', 'end']))));
    }

    public function testUnknownPeriodFallsBackToMonth(): void
    {
        self::assertSame('bulan', ReportPeriod::normalize('semua'));
    }
}
