<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->exec("ALTER TABLE programmes ADD COLUMN faces_link VARCHAR(255) NULL");
    echo "Added faces_link to programmes.<br>";
} catch (PDOException $e) {
    echo "programmes table might already have faces_link.<br>";
}

try {
    $pdo->exec("ALTER TABLE treatments ADD COLUMN faces_link VARCHAR(255) NULL");
    echo "Added faces_link to treatments.<br>";
} catch (PDOException $e) {
    echo "treatments table might already have faces_link.<br>";
}

echo "Migration done.";
?>
