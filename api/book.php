<?php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$service = $_POST['service'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$notes = $_POST['notes'] ?? '';

if (empty($name) || empty($email) || empty($phone) || empty($service) || empty($date) || empty($time)) {
    http_response_code(400);
    echo json_encode(['error' => 'Please fill in all required fields.']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO bookings (client_name, client_email, client_phone, service, preferred_date, preferred_time, notes) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $phone, $service, $date, $time, $notes]);

    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Fetch email settings
    $admin_email = get_setting($pdo, 'admin_email', 'hello@vntaura.com');
    $notify_admin = get_setting($pdo, 'notify_admin', '1') == '1';
    $notify_client = get_setting($pdo, 'notify_client', '1') == '1';
    
    // Fetch SMTP settings
    $smtp_host = get_setting($pdo, 'smtp_host', 'smtp.gmail.com');
    $smtp_port = get_setting($pdo, 'smtp_port', '587');
    $smtp_username = get_setting($pdo, 'smtp_username', '');
    $smtp_password = get_setting($pdo, 'smtp_password', '');
    
    function send_smtp_email($to, $subject, $body, $smtp_host, $smtp_port, $smtp_username, $smtp_password) {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp_username;
            $mail->Password   = $smtp_password;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtp_port;

            $mail->setFrom($smtp_username, 'VNT Aura Skin & Wellness');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    if ($notify_admin && !empty($admin_email) && !empty($smtp_username) && !empty($smtp_password)) {
        $admin_subject = "New Booking Request: $service";
        $admin_message = "
        <h2>New Booking Request</h2>
        <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
        <p><strong>Service:</strong> " . htmlspecialchars($service) . "</p>
        <p><strong>Date:</strong> " . htmlspecialchars($date) . "</p>
        <p><strong>Time:</strong> " . htmlspecialchars($time) . "</p>
        <p><strong>Notes:</strong><br>" . nl2br(htmlspecialchars($notes)) . "</p>
        ";
        send_smtp_email($admin_email, $admin_subject, $admin_message, $smtp_host, $smtp_port, $smtp_username, $smtp_password);
    }
    
    if ($notify_client && !empty($email) && !empty($smtp_username) && !empty($smtp_password)) {
        $client_subject = "Booking Request Received - VNT Aura";
        $client_message = "
        <h2>Hello " . htmlspecialchars($name) . ",</h2>
        <p>Thank you for booking with VNT Aura Skin & Wellness.</p>
        <p>We have received your request for <strong>" . htmlspecialchars($service) . "</strong> on <strong>" . htmlspecialchars($date) . "</strong> at <strong>" . htmlspecialchars($time) . "</strong>.</p>
        <p>Our team will contact you shortly to confirm your appointment.</p>
        <br>
        <p>Warm regards,<br>VNT Aura Team</p>
        ";
        send_smtp_email($email, $client_subject, $client_message, $smtp_host, $smtp_port, $smtp_username, $smtp_password);
    }

    echo json_encode(['success' => true, 'message' => 'Your booking request has been received. We will contact you shortly to confirm.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
