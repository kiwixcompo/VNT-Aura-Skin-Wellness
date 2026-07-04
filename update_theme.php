<?php
// update_theme.php
// This script updates the database settings to use the minimal white theme.
// Upload this to your live server and run it once, then delete it.

require_once __DIR__ . '/includes/db.php';

$updates = [
    'theme_primary' => '#FFFFFF',
    'theme_secondary' => '#000000',
    'theme_accent' => '#333333',
    'theme_bg' => '#FFFFFF',
    'theme_text' => '#000000'
];

try {
    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
    
    foreach ($updates as $k => $v) {
        $stmt->execute([$k, $v, $v]);
    }
    
    echo "<h2 style='color: green;'>Theme updated to White Minimalist successfully!</h2>";
    echo "<p>You can now delete this script (update_theme.php) from your server.</p>";
    echo "<p><a href='index.php'>Go to Homepage</a></p>";
} catch (\PDOException $e) {
    echo "<h2 style='color: red;'>Database Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
