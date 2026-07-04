<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../admin/includes/auth.php';

// Only admins can test email
require_login();

// Fetch settings
$smtp_host = get_setting($pdo, 'smtp_host', 'smtp.gmail.com');
$smtp_port = get_setting($pdo, 'smtp_port', '587');
$smtp_username = get_setting($pdo, 'smtp_username', '');
$smtp_password = get_setting($pdo, 'smtp_password', '');
$admin_email = get_setting($pdo, 'admin_email', 'hello@vntaura.com');

echo "<h2>Email Settings Test</h2>";
echo "<strong>Host:</strong> " . htmlspecialchars($smtp_host) . "<br>";
echo "<strong>Port:</strong> " . htmlspecialchars($smtp_port) . "<br>";
echo "<strong>Username:</strong> " . htmlspecialchars($smtp_username) . "<br>";
echo "<strong>Password:</strong> " . (!empty($smtp_password) ? '********' : '(empty)') . "<br>";
echo "<strong>Send To (Admin Email):</strong> " . htmlspecialchars($admin_email) . "<br><br>";

if (empty($smtp_username) || empty($smtp_password)) {
    die("<strong>Error:</strong> You must enter a Gmail Address and an App Password in the dashboard settings before testing.");
}

require_once __DIR__ . '/../vendor/autoload.php';

$mail = new \PHPMailer\PHPMailer\PHPMailer(true);

echo "<h3>SMTP Debug Output:</h3>";
echo "<div style='background: #111; color: #0f0; padding: 15px; font-family: monospace; overflow-x: auto;'>";

try {
    // Enable verbose debug output
    $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
    // Pipe output to the screen
    $mail->Debugoutput = function($str, $level) {
        echo htmlspecialchars($str) . "<br>";
    };

    $mail->isSMTP();
    $mail->Host       = $smtp_host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtp_username;
    $mail->Password   = $smtp_password;
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtp_port;
    
    // Timeout adjustments for testing
    $mail->Timeout = 10;

    $mail->setFrom($smtp_username, 'VNT Aura Test');
    $mail->addAddress($admin_email);

    $mail->isHTML(true);
    $mail->Subject = 'VNT Aura - SMTP Test Successful!';
    $mail->Body    = 'If you are reading this, your SMTP configuration is working perfectly.';

    $mail->send();
    echo "</div><br>";
    echo "<h3 style='color: green;'>Success! Test email has been sent.</h3>";
} catch (Exception $e) {
    echo "</div><br>";
    echo "<h3 style='color: red;'>Error Sending Email</h3>";
    echo "<strong>Mailer Error:</strong> " . htmlspecialchars($mail->ErrorInfo) . "<br>";
    echo "<p>Please check your App Password, ensure 2FA is enabled on your Google account, and check the debug log above for connection issues.</p>";
}
?>
