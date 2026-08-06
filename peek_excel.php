<?php
require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = "C:/Users/Advan/Downloads/INVENTARIS SARANA DAN PRASARANA DAN NON MEDIS.xlsx";
$spreadsheet = IOFactory::load($file);
foreach ($spreadsheet->getSheetNames() as $sheetName) {
    echo "Sheet: $sheetName\n";
    $sheet = $spreadsheet->getSheetByName($sheetName);
    $rows = $sheet->toArray();
    echo "Total Rows: " . count($rows) . "\n";
    print_r(array_slice($rows, 4, 1));
}

