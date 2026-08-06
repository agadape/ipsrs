<?php
require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
$spreadsheet = IOFactory::load("C:/Users/Advan/Downloads/INVENTARIS SARANA DAN PRASARANA DAN NON MEDIS.xlsx");
$sheet = $spreadsheet->getSheetByName("Sound System");
$rows = $sheet->toArray();
print_r(array_slice($rows, 0, 10));

