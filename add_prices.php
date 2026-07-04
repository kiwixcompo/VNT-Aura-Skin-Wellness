<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->exec("ALTER TABLE programmes ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00 AFTER description");
    echo "Added price to programmes.<br>";
} catch (PDOException $e) {
    echo "Column price might already exist in programmes.<br>";
}

try {
    $pdo->exec("ALTER TABLE treatments ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00 AFTER description");
    echo "Added price to treatments.<br>";
} catch (PDOException $e) {
    echo "Column price might already exist in treatments.<br>";
}

// Ensure the consultation image setting exists
$stmt = $pdo->prepare('INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)');
$stmt->execute(['consultation_image_type', 'url']);
$stmt->execute(['consultation_image_url', 'https://images.unsplash.com/photo-1616394584738-fc6e612e71c9?auto=format&fit=crop&q=80&w=1000']);
$stmt->execute(['consultation_image_upload', '']);
$stmt->execute(['paypal_email', 'vntauraskinandwellness@gmail.com']);

echo "Settings inserted.<br>";
echo "Migration finished.";
?>
