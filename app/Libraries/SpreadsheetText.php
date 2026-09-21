<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class SpreadsheetText
{
    /**
     * Write externally sourced report data as literal text.
     *
     * Spreadsheet applications treat values beginning with =, +, - or @ as
     * formulas when their type is inferred. Explicit string typing preserves
     * the reported value without evaluating it when the workbook is opened.
     */
    public static function set(Worksheet $sheet, string $coordinate, mixed $value): void
    {
        $sheet->setCellValueExplicit($coordinate, (string) ($value ?? ''), DataType::TYPE_STRING);
    }
}
