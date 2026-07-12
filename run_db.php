<?php
$db = new mysqli('127.0.0.1', 'root', '', 'ipsrs');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$res = $db->query("ALTER TABLE jadwal_preventif ADD COLUMN id_aset VARCHAR(50) NULL AFTER id;");
if ($res) {
    echo "Success adding id_aset.\n";
} else {
    echo "Error: " . $db->error . "\n";
}
