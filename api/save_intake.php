<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/EmailHelper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$booking_id = $_POST['booking_id'] ?? null;
if (!$booking_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing booking ID']);
    exit;
}

try {
    // 1. Save intake form
    $stmt = $pdo->prepare('INSERT INTO booking_intake_forms (booking_id, dob_day, dob_month, dob_year, address, gender, height, weight, gp_details, has_medical_conditions, medical_history, signature_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    
    $stmt->execute([
        $booking_id,
        $_POST['dob_day'] ?? '',
        $_POST['dob_month'] ?? '',
        $_POST['dob_year'] ?? '',
        $_POST['address'] ?? '',
        $_POST['gender'] ?? '',
        $_POST['height'] ?? '',
        $_POST['weight'] ?? '',
        $_POST['gp_details'] ?? '',
        $_POST['has_medical_conditions'] ?? 'No',
        $_POST['medical_history'] ?? '',
        $_POST['signature_name'] ?? ''
    ]);

    // 2. Fetch booking details to send confirmation email
    $bStmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
    $bStmt->execute([$booking_id]);
    $booking = $bStmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        $emailHelper = new EmailHelper();
        $bookingData = [
            'name' => $booking['client_name'],
            'email' => $booking['client_email'],
            'phone' => $booking['client_phone'],
            'service' => $booking['service'],
            'date' => $booking['preferred_date'],
            'time' => $booking['preferred_time'],
            'notes' => $booking['notes']
        ];
        
        $notify_client = get_setting($pdo, 'notify_client', '1') == '1';
        if ($notify_client && !empty($booking['client_email'])) {
            $emailHelper->sendClientBookingConfirmation($booking['client_email'], $bookingData);
        }
        
        // Mark status as approved/confirmed since they paid deposit and did intake? 
        // We'll leave it as is or Admin can change it.
    }

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
