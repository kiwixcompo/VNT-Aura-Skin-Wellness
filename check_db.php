<?php
require_once 'includes/database.php';
$stmt = $db->query("SELECT * FROM settings");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['key_name'] . " = " . $r['value'] . "\n";
}
