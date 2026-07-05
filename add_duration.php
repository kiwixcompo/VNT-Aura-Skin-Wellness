<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->exec("ALTER TABLE programmes ADD COLUMN duration VARCHAR(50) NULL");
    echo "Added duration to programmes.<br>";
} catch (PDOException $e) {}

try {
    $pdo->exec("ALTER TABLE treatments ADD COLUMN duration VARCHAR(50) NULL");
    echo "Added duration to treatments.<br>";
} catch (PDOException $e) {}

echo "Duration Migration done.";
?>
