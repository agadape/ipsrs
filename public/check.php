<?php
$pdo = new PDO('mysql:host=localhost;dbname=ipsrs', 'root', '');
$stmt = $pdo->query("SHOW CREATE TABLE template_checklist");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
