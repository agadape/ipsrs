<?php
$json = file_get_contents("C:/Users/Advan/Downloads/ipsc7141_ipsrs_db.json");
if (!$json) { echo "File not found."; exit; }
$data = json_decode($json, true);
// print out indexes for kode_kerusakan and laporan_kerusakan if available in JSON
// But the JSON probably only has "data" and "name". 
// Let us just search the raw JSON text for "UNIQUE" or "CREATE TABLE".
?>
