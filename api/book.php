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
        $price = 0;
        if ($service === 'Initial Consultation' || $service === 'Skin Consultation') {
            $price_str = get_setting($pdo, 'consultation_price', '20');
            $price = (float) preg_replace('/[^0-9.]/', '', $price_str);
        } else {
            $stmt2 = $pdo->prepare("SELECT price FROM treatments WHERE title = ?");
            $stmt2->execute([$service]);
            if ($t = $stmt2->fetch()) {
                $price = (float) $t['price'];
            } else {
                $stmt3 = $pdo->prepare("SELECT price FROM programmes WHERE title = ?");
                $stmt3->execute([$service]);
                if ($p = $stmt3->fetch()) {
                    $price = (float) $p['price'];
                }
            }
        }
        
        if ($price > 0) {
            $paypal_email = get_setting($pdo, 'paypal_email', 'vntauraskinandwellness@gmail.com');
            $paypal_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=" . urlencode($paypal_email) . "&amount=" . number_format($price, 2, '.', '') . "&currency_code=USD&item_name=" . urlencode($service);
            echo json_encode(['success' => true, 'redirect' => $paypal_url, 'message' => 'Redirecting to PayPal...']);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Your booking request has been received. We will contact you shortly to confirm.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
