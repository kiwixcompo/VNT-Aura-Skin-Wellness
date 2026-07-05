<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/EmailHelper.php';

// This script should be run via a Cron Job (e.g. every 5 minutes)
// php /path/to/vntaura/api/cron_aftercare.php
// or accessed via web: curl https://vntaura.com/api/cron_aftercare.php

try {
    // We want bookings where:
    // 1. aftercare_sent = 0
    // 2. preferred_date + preferred_time + duration < NOW()
    
    // Because preferred_time is a string like "09:00 AM", we can combine it with date.
    // Let's fetch all unsent bookings that are from today or earlier, and check in PHP.
    $stmt = $pdo->query("SELECT * FROM bookings WHERE aftercare_sent = 0 AND preferred_date <= CURRENT_DATE()");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $emailHelper = new EmailHelper();
    $sentCount = 0;

    foreach ($bookings as $b) {
        // Construct the datetime of the appointment
        $datetimeStr = $b['preferred_date'] . ' ' . $b['preferred_time'];
        $appointmentTime = strtotime($datetimeStr);
        if (!$appointmentTime) continue; // Invalid time format

        // Parse duration (e.g., "1 hr", "45 Minutes", "30 Minutes")
        $durationStr = strtolower($b['duration']);
        $durationMinutes = 60; // default
        
        if (preg_match('/(\d+)\s*(hr|hour|h)/', $durationStr, $matches)) {
            $durationMinutes = intval($matches[1]) * 60;
        } elseif (preg_match('/(\d+)\s*(min|m)/', $durationStr, $matches)) {
            $durationMinutes = intval($matches[1]);
        }

        // Add duration + maybe a 15-minute buffer so it doesn't send while they're literally walking out the door
        $aftercareTriggerTime = $appointmentTime + ($durationMinutes * 60) + (15 * 60);

        if (time() >= $aftercareTriggerTime) {
            // Time to send!
            // Get the specific aftercare content for this service
            $service = $b['service'];
            
            // Check treatments first
            $tStmt = $pdo->prepare("SELECT aftercare_email_content FROM treatments WHERE title = ?");
            $tStmt->execute([$service]);
            $res = $tStmt->fetch();
            $content = $res ? $res['aftercare_email_content'] : null;

            if (!$content) {
                // Check programmes
                $pStmt = $pdo->prepare("SELECT aftercare_email_content FROM programmes WHERE title = ?");
                $pStmt->execute([$service]);
                $res = $pStmt->fetch();
                $content = $res ? $res['aftercare_email_content'] : "Thank you for your visit. Please ensure you stay hydrated and follow any instructions given by your practitioner.";
            }

            // Send Email
            if (!empty($b['client_email'])) {
                $emailHelper->sendAftercareEmail($b['client_email'], $b['client_name'], $service, $content);
            }

            // Mark as sent
            $upd = $pdo->prepare("UPDATE bookings SET aftercare_sent = 1 WHERE id = ?");
            $upd->execute([$b['id']]);

            $sentCount++;
        }
    }

    echo json_encode(['success' => true, 'sent' => $sentCount, 'message' => "Aftercare cron ran successfully. Sent $sentCount emails."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
