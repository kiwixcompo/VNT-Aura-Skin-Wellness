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

    require_once __DIR__ . '/../includes/EmailHelper.php';
    
    $notify_admin = get_setting($pdo, 'notify_admin', '1') == '1';
    $notify_client = get_setting($pdo, 'notify_client', '1') == '1';
    $admin_email = $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@vntaura.com';
    
    $emailHelper = new EmailHelper();
    $bookingData = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'service' => $service,
        'date' => $date,
        'time' => $time,
        'notes' => $notes
    ];
    
    if ($notify_admin && !empty($admin_email)) {
        $emailHelper->sendAdminBookingNotification($admin_email, $bookingData);
    }
    

    if ($notify_client && !empty($email)) {
        $emailHelper->sendClientBookingConfirmation($email, $bookingData);
    }
    
    $payment_method = $_POST['payment_method'] ?? 'later';
    if ($payment_method === 'paypal') {
        $deposit_price = 20.00; // Flat standard deposit
        $paypal_email = get_setting($pdo, 'paypal_email', 'vntauraskinandwellness@gmail.com');
        $item_name = "Deposit: " . substr($service, 0, 100); // Truncate if too long for paypal
        $paypal_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=" . urlencode($paypal_email) . "&amount=" . number_format($deposit_price, 2, '.', '') . "&currency_code=USD&item_name=" . urlencode($item_name);
        echo json_encode(['success' => true, 'redirect' => $paypal_url, 'message' => 'Redirecting to PayPal for £20 Deposit...']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Your booking request has been received. We will contact you shortly to confirm.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
