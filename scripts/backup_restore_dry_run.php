<?php

declare(strict_types=1);

if (!extension_loaded('sqlite3')) {
    fwrite(STDERR, "SQLite3 extension is required for the disposable recovery drill.\n");
    exit(1);
}

$workspace = dirname(__DIR__) . '/writable/verification';
if (!is_dir($workspace) && !mkdir($workspace, 0770, true) && !is_dir($workspace)) {
    fwrite(STDERR, "Cannot create verification workspace.\n");
    exit(1);
}

$token    = bin2hex(random_bytes(6));
$source   = $workspace . '/backup-source-' . $token . '.sqlite';
$backup   = $workspace . '/backup-copy-' . $token . '.sqlite';
$restored = $workspace . '/backup-restored-' . $token . '.sqlite';

try {
    $db = new SQLite3($source, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    $db->exec('CREATE TABLE recovery_probe (id INTEGER PRIMARY KEY, marker TEXT NOT NULL UNIQUE)');
    $db->exec("INSERT INTO recovery_probe (marker) VALUES ('alpha'), ('beta'), ('gamma')");
    $expected = queryMarkers($db);
    $db->close();

    if (!copy($source, $backup) || !copy($backup, $restored)) {
        throw new RuntimeException('Backup or restore copy failed.');
    }

    $restoredDb = new SQLite3($restored, SQLITE3_OPEN_READONLY);
    $actual = queryMarkers($restoredDb);
    $restoredDb->close();

    if ($actual !== $expected) {
        throw new RuntimeException('Restored data does not match the source markers.');
    }

    printf("PASS disposable backup/restore: %d rows, digest %s\n", count($actual), hash('sha256', implode('|', $actual)));
    echo "Scope: local SQLite file mechanics only; this is not a MySQL production restore claim.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL disposable backup/restore: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
} finally {
    foreach ([$source, $backup, $restored] as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

/** @return list<string> */
function queryMarkers(SQLite3 $db): array
{
    $result = $db->query('SELECT marker FROM recovery_probe ORDER BY id');
    $markers = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $markers[] = (string) $row['marker'];
    }
    return $markers;
}
