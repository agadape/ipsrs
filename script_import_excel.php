<?php
require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = "C:/Users/Advan/Downloads/INVENTARIS SARANA DAN PRASARANA DAN NON MEDIS.xlsx";
if (!file_exists($file)) die("File tidak ditemukan.");

$spreadsheet = IOFactory::load($file);
$pdo = new PDO("mysql:host=localhost;dbname=ipsrs", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Wipe existing data for a clean fresh seed
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE aset_series; TRUNCATE aset; SET FOREIGN_KEY_CHECKS = 1;");
echo "DB Wiped.\n";

$mappings = [
    "Genset" => ["nama" => "Genset", "kategori" => "Kelistrikan", "cols" => ["merk"=>1, "no_seri"=>2, "lokasi"=>3, "keterangan"=>4]],
    "UPS" => ["nama" => "UPS", "kategori" => "Kelistrikan", "cols" => ["merk"=>1, "kapasitas"=>2, "lokasi"=>3]],
    "Oksigen Konsentrator" => ["nama" => "Oksigen Konsentrator", "kategori" => "Alat Medis", "cols" => ["unit"=>1, "merk"=>2, "model"=>3, "no_seri"=>4]],
    "Hepafilter" => ["nama" => "Hepafilter", "kategori" => "Alat Non Medis", "cols" => ["unit"=>1, "no_seri"=>3, "lokasi"=>4]],
    "Water Heater" => ["nama" => "Water Heater", "kategori" => "Fasilitas Ruangan", "cols" => ["unit"=>1, "merk"=>2, "no_seri"=>3]],
    "Refrigerator" => ["nama" => "Kulkas / Refrigerator", "kategori" => "Elektronik", "cols" => ["merk"=>1, "model"=>2, "no_seri"=>3, "unit"=>4, "ruangan"=>5]],
    "Refrigerator obat" => ["nama" => "Kulkas Obat", "kategori" => "Elektronik", "cols" => ["unit"=>1, "merk"=>2, "no_seri"=>3]],
    "CCTV" => ["nama" => "Kamera CCTV", "kategori" => "Keamanan", "cols" => ["ruangan"=>1, "unit"=>2, "merk"=>3]],
    "BED BANGSAL" => ["nama" => "Tempat Tidur Pasien", "kategori" => "Fasilitas Pasien", "cols" => ["unit"=>1, "ruangan"=>2, "merk"=>3, "model"=>4, "no_seri"=>5]],
    "BED POLI" => ["nama" => "Tempat Tidur Periksa", "kategori" => "Fasilitas Pasien", "cols" => ["unit"=>1, "ruangan"=>2, "merk"=>3, "model"=>4, "no_seri"=>5]],
    "Brankar" => ["nama" => "Brankar / Stretcher", "kategori" => "Fasilitas Pasien", "cols" => ["merk"=>1, "model"=>2, "no_seri"=>3, "unit"=>4]],
    "TRAFO TM" => ["nama" => "Trafo TM", "kategori" => "Kelistrikan", "cols" => ["merk"=>1, "kapasitas"=>2, "no_seri"=>5, "keterangan"=>6]],
];

$totalAset = 0;
$totalSeries = 0;
$cacheAset = []; // key: nama_kategori => id_aset

foreach ($mappings as $sheetName => $map) {
    $sheet = $spreadsheet->getSheetByName($sheetName);
    if (!$sheet) { echo "Sheet not found: $sheetName\n"; continue; }
    $rows = $sheet->toArray();
    
    $startRow = 0;
    foreach ($rows as $i => $row) {
        if (!empty($row[0]) && is_numeric($row[0])) { $startRow = $i; break; }
    }
    
    echo "Processing $sheetName from row $startRow...\n";
    $count = 0;
    
    for ($i = $startRow; $i < count($rows); $i++) {
        $row = $rows[$i];
        if (empty($row[0]) && empty($row[1])) continue;
        
        $cacheKey = strtolower($map["nama"] . "_" . $map["kategori"]);
        if (!isset($cacheAset[$cacheKey])) {
            $stmt = $pdo->prepare("SELECT id FROM aset WHERE nama = ? AND kategori = ?");
            $stmt->execute([$map["nama"], $map["kategori"]]);
            $existing = $stmt->fetchColumn();
            if ($existing) {
                $cacheAset[$cacheKey] = $existing;
            } else {
                $idAset = $pdo->query("SELECT UUID()")->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO aset (id, nama, jenis, kategori) VALUES (?, ?, ?, ?)");
                $stmt->execute([$idAset, $map["nama"], "Sarana", $map["kategori"]]);
                $cacheAset[$cacheKey] = $idAset;
                $totalAset++;
            }
        }
        $idAset = $cacheAset[$cacheKey];
        
        $merk = isset($map["cols"]["merk"]) ? trim(substr($row[$map["cols"]["merk"]] ?? "", 0, 100)) : null;
        $model = isset($map["cols"]["model"]) ? trim(substr($row[$map["cols"]["model"]] ?? "", 0, 100)) : null;
        $kapasitas = isset($map["cols"]["kapasitas"]) ? trim(substr($row[$map["cols"]["kapasitas"]] ?? "", 0, 50)) : null;
        
        $no_seri = isset($map["cols"]["no_seri"]) ? trim((string)($row[$map["cols"]["no_seri"]] ?? "")) : "";
        $unit = isset($map["cols"]["unit"]) ? trim((string)($row[$map["cols"]["unit"]] ?? "")) : "";
        $ruangan = isset($map["cols"]["ruangan"]) ? trim((string)($row[$map["cols"]["ruangan"]] ?? "")) : "";
        $lokasi = isset($map["cols"]["lokasi"]) ? trim((string)($row[$map["cols"]["lokasi"]] ?? "")) : "";
        
        if (empty($lokasi)) $lokasi = trim("$unit $ruangan");
        if (empty($unit)) $unit = "Poliklinik / Rawat Jalan";
        if (empty($lokasi)) $lokasi = "Belum Ditentukan";
        $gedung = "Utama";
        
        $nomor_aset = "INV-" . date("Y") . "-" . str_pad($totalSeries + 1, 4, "0", STR_PAD_LEFT);
        
        $idSeries = $pdo->query("SELECT UUID()")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO aset_series (id, id_aset, nomor_aset, no_seri, lokasi, gedung, ruangan, unit, kondisi, status, merk, model, kapasitas, sumber_import) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $idSeries, 
            $idAset, 
            $nomor_aset,
            $no_seri ?: null, 
            substr($lokasi, 0, 200), 
            $gedung,
            substr($ruangan, 0, 100), 
            substr($unit, 0, 100), 
            "Baik", 
            "Aktif",
            $merk ?: null,
            $model ?: null,
            $kapasitas ?: null,
            "Excel_$sheetName"
        ]);
        $totalSeries++;
        $count++;
    }
    echo "  -> Added $count items for $sheetName\n";
}

echo "\n--- SUCCESS ---\n";
echo "Total Master Aset Created: $totalAset\n";
echo "Total Item/Series Created: $totalSeries\n";

