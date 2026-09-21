<?php

namespace Tests\Unit;

use App\Libraries\SpreadsheetText;
use CodeIgniter\Test\CIUnitTestCase;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class SpreadsheetTextTest extends CIUnitTestCase
{
    public function testWritesFormulaLikeInputAsLiteralString(): void
    {
        $sheet = (new Spreadsheet())->getActiveSheet();

        foreach (['=1+1', '+CMD()', '-10+5', '@SUM(A1:A2)'] as $index => $value) {
            $coordinate = 'A' . ($index + 1);
            SpreadsheetText::set($sheet, $coordinate, $value);

            self::assertSame($value, $sheet->getCell($coordinate)->getValue());
            self::assertSame(DataType::TYPE_STRING, $sheet->getCell($coordinate)->getDataType());
        }
    }

    public function testConvertsNullToAnEmptyLiteralString(): void
    {
        $sheet = (new Spreadsheet())->getActiveSheet();

        SpreadsheetText::set($sheet, 'A1', null);

        self::assertSame('', $sheet->getCell('A1')->getValue());
        self::assertSame(DataType::TYPE_STRING, $sheet->getCell('A1')->getDataType());
    }
}
