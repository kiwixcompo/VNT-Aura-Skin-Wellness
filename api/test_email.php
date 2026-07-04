<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../admin/includes/auth.php';

// Only admins can test email
require_login();

echo "<h2>Email Settings Test (Environment Based)</h2>";
echo "<strong>Host:</strong> " . htmlspecialchars($_ENV['MAIL_HOST'] ?? 'not set') . "<br>";
echo "<strong>Port:</strong> " . htmlspecialchars($_ENV['MAIL_PORT'] ?? 'not set') . "<br>";
echo "<strong>Username:</strong> " . htmlspecialchars($_ENV['MAIL_USERNAME'] ?? 'not set') . "<br>";
echo "<strong>Password:</strong> " . (!empty($_ENV['MAIL_PASSWORD']) ? '********' : '(empty)') . "<br>";
echo "<strong>Send To (Admin Email):</strong> " . htmlspecialchars($_ENV['MAIL_FROM_ADDRESS'] ?? 'not set') . "<br><br>";

if (empty($_ENV['MAIL_USERNAME']) || empty($_ENV['MAIL_PASSWORD'])) {
    die("<strong>Error:</strong> You must configure MAIL_USERNAME and MAIL_PASSWORD in your .env file.");
}

require_once __DIR__ . '/../includes/EmailHelper.php';

echo "<h3>SMTP Debug Output:</h3>";
echo "<div style='background: #111; color: #0f0; padding: 15px; font-family: monospace; overflow-x: auto;'>";

$emailHelper = new EmailHelper();
$success = $emailHelper->sendTestEmail($_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@vntaura.com');

echo "</div><br>";

if ($success) {
    echo "<h3 style='color: green;'>Success! Test email has been sent using EmailHelper.</h3>";
} else {
    echo "<h3 style='color: red;'>Error Sending Email</h3>";
    echo "<p>Please check your App Password, ensure 2FA is enabled on your Google account, and check the debug log above for connection issues.</p>";
}
?>