<?php
$pdo = new PDO("mysql:host=localhost;dbname=ipsrs", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT nama, kategori, COUNT(*) as c FROM aset GROUP BY nama, kategori HAVING c > 1");
$duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($duplicates as $dup) {
    echo "Deduplicating " . $dup["nama"] . "...\n";
    $stmt2 = $pdo->prepare("SELECT id FROM aset WHERE nama = ? AND kategori = ? ORDER BY created_at ASC");
    $stmt2->execute([$dup["nama"], $dup["kategori"]]);
    $ids = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($ids) > 1) {
        $master_id = $ids[0];
        $slave_ids = array_slice($ids, 1);
        
        $placeholders = implode(",", array_fill(0, count($slave_ids), "?"));
        
        // Update aset_series to point to the master
        $updateStmt = $pdo->prepare("UPDATE aset_series SET id_aset = ? WHERE id_aset IN ($placeholders)");
        $params = array_merge([$master_id], $slave_ids);
        $updateStmt->execute($params);
        
        // Delete slave asets
        $deleteStmt = $pdo->prepare("DELETE FROM aset WHERE id IN ($placeholders)");
        $deleteStmt->execute($slave_ids);
        
        echo "Merged " . count($slave_ids) . " records into $master_id\n";
    }
}

// Add constraint
try { 
    $pdo->exec("ALTER TABLE aset ADD CONSTRAINT uk_aset_nama_kategori UNIQUE (nama, kategori);");
    echo "Unique constraint added.\n";
} catch(Exception $e) { echo $e->getMessage() . "\n"; }

// Fix jadwal_preventif
$pdo->exec("UPDATE jadwal_preventif SET id_aset = NULL WHERE id_aset NOT IN (SELECT id FROM aset_series)");
try {
    $pdo->exec("ALTER TABLE jadwal_preventif MODIFY COLUMN id_aset char(36) DEFAULT NULL, ADD CONSTRAINT fk_jp_aset_series FOREIGN KEY (id_aset) REFERENCES aset_series (id) ON DELETE SET NULL ON UPDATE CASCADE;");
    echo "FK added to jadwal_preventif.\n";
} catch(Exception $e) { echo $e->getMessage() . "\n"; }

