<?php
require_once __DIR__ . '/includes/db.php';

// Add logic to setup email with given credentials
$settings = [
    'smtp_username' => 'vntauraskinandwellness@gmail.com',
    'smtp_password' => 'qarnizxlywamaflb',
    'admin_email' => 'vntauraskinandwellness@gmail.com'
];

$stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
foreach ($settings as $key => $val) {
    $stmt->execute([$key, $val, $val]);
}

echo "<h2>Email Settings configured successfully!</h2>";
echo "<p>Please delete this file (setup_email.php) for security purposes.</p>";
echo "<a href='admin/index.php'>Go to Admin Dashboard</a>";
?>
