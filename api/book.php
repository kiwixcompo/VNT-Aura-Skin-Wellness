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
    // Fetch duration from DB based on service title
    $duration = '1 hr'; // Default
    $stmtDuration = $pdo->prepare("SELECT duration FROM treatments WHERE title = ?");
    $stmtDuration->execute([$service]);
    $tRes = $stmtDuration->fetch();
    if ($tRes) {
        $duration = $tRes['duration'] ?: '1 hr';
    } else {
        $stmtDuration = $pdo->prepare("SELECT duration FROM programmes WHERE title = ?");
        $stmtDuration->execute([$service]);
        $pRes = $stmtDuration->fetch();
        if ($pRes) {
            $duration = $pRes['duration'] ?: '1 hr';
        }
    }

    $stmt = $pdo->prepare('INSERT INTO bookings (client_name, client_email, client_phone, service, preferred_date, preferred_time, notes, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $phone, $service, $date, $time, $notes, $duration]);
    $booking_id = $pdo->lastInsertId();

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
    

    $payment_method = $_POST['payment_method'] ?? 'later';
    
    // We only send the confirmation immediately if they "Pay Later" (or if it's faces flow, where we don't handle confirmation anyway)
    // If it's paypal or bypass, we send the confirmation AFTER they fill the intake form!
    if ($notify_client && !empty($email) && $payment_method === 'later') {
        $emailHelper->sendClientBookingConfirmation($email, $bookingData);
    }
    
    $is_faces_flow = $_POST['is_faces_flow'] ?? '0';
    if ($is_faces_flow === '1') {
        $faces_url = $_POST['dynamic_faces_url'] ?? get_setting($pdo, 'faces_url', '');
        if (empty($faces_url)) {
            $faces_url = get_setting($pdo, 'faces_url', '');
        }
        echo json_encode(['success' => true, 'redirect' => $faces_url, 'is_faces' => true, 'message' => 'Redirecting to Faces Consent...']);
        exit;
    }

    if ($payment_method === 'bypass') {
        $intake_url = "intake.php?booking_id=" . $booking_id;
        echo json_encode(['success' => true, 'redirect' => $intake_url, 'message' => 'Payment Bypassed! Redirecting to Intake Form...']);
        exit;
    }

    if ($payment_method === 'paypal') {
        $deposit_price = 20.00; // Flat standard deposit
        $paypal_email = get_setting($pdo, 'paypal_email', 'vntauraskinandwellness@gmail.com');
        $item_name = "Deposit: " . substr($service, 0, 100); // Truncate if too long for paypal
        
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['REQUEST_URI']);
        $return_url = $base_url . "/../intake.php?booking_id=" . $booking_id;
        
        $paypal_url = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=" . urlencode($paypal_email) . "&amount=" . number_format($deposit_price, 2, '.', '') . "&currency_code=USD&item_name=" . urlencode($item_name) . "&return=" . urlencode($return_url);
        echo json_encode(['success' => true, 'redirect' => $paypal_url, 'message' => 'Redirecting to PayPal for £20 Deposit...']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Your booking request has been received. We will contact you shortly to confirm.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
