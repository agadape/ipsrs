<?php

namespace App\Libraries;

use DateTimeImmutable;

/**
 * Canonical inclusive date ranges for IPSRS operational reports.
 *
 * LK reports are filtered by report date, while preventive reports are
 * filtered by the LKP examination date. Callers must state that source in
 * their output rather than treating the ranges as interchangeable.
 */
final class ReportPeriod
{
    public const DEFAULT = 'bulan';

    /** @var list<string> */
    public const ALLOWED = ['minggu', 'bulan', 'tahun'];

    public static function normalize(?string $period): string
    {
        return in_array($period, self::ALLOWED, true) ? $period : self::DEFAULT;
    }

    /**
     * @return array{key: string, start: string, end: string, label: string}
     */
    public static function describe(?string $period, ?DateTimeImmutable $anchor = null): array
    {
        $key = self::normalize($period);
        $anchor ??= new DateTimeImmutable('now');
        $anchor = $anchor->setTime(0, 0);

        if ($key === 'minggu') {
            $start = $anchor->modify('-' . ((int) $anchor->format('N') - 1) . ' days');
            $end = $start->modify('+6 days');
            $label = 'Minggu ini (' . $start->format('Y-m-d') . ' s.d. ' . $end->format('Y-m-d') . ')';
        } elseif ($key === 'tahun') {
            $start = $anchor->setDate((int) $anchor->format('Y'), 1, 1);
            $end = $start->setDate((int) $anchor->format('Y'), 12, 31);
            $label = 'Tahun ' . $anchor->format('Y');
        } else {
            $start = $anchor->modify('first day of this month');
            $end = $anchor->modify('last day of this month');
            $label = 'Bulan ' . $anchor->format('Y-m');
        }

        return [
            'key' => $key,
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'label' => $label,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array{start: string, end: string} $range
     * @return array<int, array<string, mixed>>
     */
    public static function filterRows(array $rows, string $dateColumn, array $range): array
    {
        return array_values(array_filter($rows, static function (array $row) use ($dateColumn, $range): bool {
            $date = $row[$dateColumn] ?? null;
            return is_string($date) && $date >= $range['start'] && $date <= $range['end'];
        }));
    }
}
