<?php

namespace Tests\Unit;

use App\Libraries\LkpChecklist;
use CodeIgniter\Test\CIUnitTestCase;

final class LkpChecklistTest extends CIUnitTestCase
{
    public function testTextResultAndNoteAreBothPreserved(): void
    {
        $items = [[
            'no_item' => 4,
            'jenis' => 'Teks',
            'komponen' => 'Kondisi panel',
            'hasil' => 'Indikator normal',
            'ket' => 'Diperiksa pagi hari',
        ]];

        self::assertNull(LkpChecklist::validate($items));
        $rows = LkpChecklist::toRows('lkp-1', $items, static fn(): string => 'detail-1');
        $payload = LkpChecklist::textPayload($rows[0]['keterangan']);

        self::assertSame('Indikator normal', $payload['hasil']);
        self::assertSame('Diperiksa pagi hari', $payload['catatan']);
    }

    public function testZeroMeasurementIsAccepted(): void
    {
        self::assertNull(LkpChecklist::validate([[
            'jenis' => 'Pengukuran',
            'komponen' => 'Tekanan',
            'hasil' => '0',
            'satuan' => 'bar',
        ]]));
    }

    public function testInvalidChecklistTypeAndValueAreRejected(): void
    {
        self::assertNotNull(LkpChecklist::validate([[
            'jenis' => 'Tidak dikenal',
            'komponen' => 'X',
            'hasil' => 'Y',
        ]]));
        self::assertNotNull(LkpChecklist::validate([[
            'jenis' => 'Inspeksi',
            'komponen' => 'X',
            'hasil' => 'Mungkin',
        ]]));
    }
}
