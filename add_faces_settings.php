<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->prepare('INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)');
$stmt->execute(['booking_mode', 'faces']);
$stmt->execute(['faces_url', '']);

echo "Settings inserted.<br>";
?>
