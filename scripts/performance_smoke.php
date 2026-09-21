<?php

declare(strict_types=1);

if (!extension_loaded('sqlite3')) {
    fwrite(STDERR, "SQLite3 extension is required.\n");
    exit(1);
}

$rows = 20_000;
$db = new SQLite3(':memory:');
$db->exec('PRAGMA journal_mode = MEMORY');
$db->exec('PRAGMA synchronous = OFF');
$db->exec('CREATE TABLE laporan_kerusakan (id INTEGER PRIMARY KEY, no_order TEXT, tanggal TEXT, jam_laporan TEXT, status TEXT, kode TEXT, keluhan TEXT, pelapor TEXT, response_time INTEGER)');
$db->exec('CREATE INDEX idx_lk_tanggal ON laporan_kerusakan(tanggal)');
$db->exec('CREATE INDEX idx_lk_status ON laporan_kerusakan(status)');
$db->exec('BEGIN');
$insert = $db->prepare('INSERT INTO laporan_kerusakan VALUES (:id, :order_no, :date, :time, :status, :code, :complaint, :reporter, :response)');
$statuses = ['Laporan Masuk', 'Didisposisi', 'Survei', 'Dalam Perbaikan', 'Menunggu Suku Cadang', 'Menunggu Vendor', 'Selesai'];
for ($id = 1; $id <= $rows; $id++) {
    $insert->bindValue(':id', $id, SQLITE3_INTEGER);
    $insert->bindValue(':order_no', sprintf('LK-2026-%05d', $id), SQLITE3_TEXT);
    $insert->bindValue(':date', sprintf('2026-%02d-%02d', ($id % 12) + 1, ($id % 28) + 1), SQLITE3_TEXT);
    $insert->bindValue(':time', sprintf('%02d:%02d:00', $id % 24, $id % 60), SQLITE3_TEXT);
    $insert->bindValue(':status', $statuses[$id % count($statuses)], SQLITE3_TEXT);
    $insert->bindValue(':code', 'K' . ($id % 25), SQLITE3_TEXT);
    $insert->bindValue(':complaint', 'Keluhan sintetis ' . $id, SQLITE3_TEXT);
    $insert->bindValue(':reporter', 'Pelapor ' . ($id % 150), SQLITE3_TEXT);
    $insert->bindValue(':response', $id % 45, SQLITE3_INTEGER);
    $insert->execute();
}
$db->exec('COMMIT');

$fullStart = hrtime(true);
$result = $db->query('SELECT * FROM laporan_kerusakan ORDER BY tanggal DESC, jam_laporan DESC');
$all = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $all[] = $row;
}
$filtered = array_values(array_filter($all, static fn (array $row): bool => $row['status'] === 'Selesai' && str_starts_with($row['tanggal'], '2026-09')));
$fullMs = elapsedMs($fullStart);

$sqlStart = hrtime(true);
$statement = $db->prepare("SELECT id, no_order, tanggal, jam_laporan, status FROM laporan_kerusakan WHERE tanggal >= '2026-09-01' AND tanggal < '2026-10-01' AND status = :status ORDER BY tanggal DESC, jam_laporan DESC LIMIT 50");
$statement->bindValue(':status', 'Selesai', SQLITE3_TEXT);
$page = $statement->execute();
$pageRows = 0;
while ($page->fetchArray(SQLITE3_ASSOC)) {
    $pageRows++;
}
$sqlMs = elapsedMs($sqlStart);

printf("Dataset: %d LK rows\n", $rows);
printf("Current-style full load + PHP filter: %.3f ms, %d rows loaded, %d matched\n", $fullMs, count($all), count($filtered));
printf("SQL filter + 50-row page: %.3f ms, %d rows returned\n", $sqlMs, $pageRows);
printf("Observed local ratio: %.1fx\n", $sqlMs > 0 ? $fullMs / $sqlMs : 0);
echo "Scope: synthetic in-memory SQLite evidence; remeasure on hosted MySQL before setting an SLA.\n";

if (count($all) !== $rows || $pageRows > 50) {
    fwrite(STDERR, "FAIL benchmark integrity check.\n");
    exit(1);
}

function elapsedMs(int $startedAt): float
{
    return (hrtime(true) - $startedAt) / 1_000_000;
}
