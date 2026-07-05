<?php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$date = $_GET['date'] ?? '';
if (!$date) {
    echo json_encode(['error' => 'Date is required']);
    exit;
}

// Generate base slots (9:00 AM to 5:00 PM, 30 min intervals)
$base_slots = [];
$start_time = strtotime('09:00');
$end_time = strtotime('17:00');
while ($start_time <= $end_time) {
    $base_slots[] = date('H:i', $start_time);
    $start_time = strtotime('+30 minutes', $start_time);
}

// 1. Remove slots already booked in the database
$stmt = $pdo->prepare('SELECT preferred_time FROM bookings WHERE preferred_date = ?');
$stmt->execute([$date]);
$booked_times = $stmt->fetchAll(PDO::FETCH_COLUMN);

// The booked_time might be like "10:00" or "Morning"
// If it's Morning/Afternoon, we block out those ranges. For specific times, we block that specific time.
$blocked_slots = [];
foreach ($booked_times as $bt) {
    if ($bt === 'Morning') {
        $blocked_slots = array_merge($blocked_slots, ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30']);
    } elseif ($bt === 'Afternoon') {
        $blocked_slots = array_merge($blocked_slots, ['12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30']);
    } elseif ($bt === 'Evening') {
        $blocked_slots = array_merge($blocked_slots, ['16:00', '16:30', '17:00']);
    } else {
        // Assume exact time e.g., 10:30
        $blocked_slots[] = date('H:i', strtotime($bt));
    }
}

// 2. Remove slots blocked in Google Calendar (iCal)
$ical_url = get_setting($pdo, 'google_ical_url', '');
if ($ical_url) {
    // Fetch and parse iCal simply
    $ctx = stream_context_create(['http' => ['timeout' => 3]]);
    $ical_data = @file_get_contents($ical_url, false, $ctx);
    if ($ical_data) {
        $lines = explode("\n", $ical_data);
        $in_event = false;
        $event_start = '';
        $event_end = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === 'BEGIN:VEVENT') {
                $in_event = true;
                $event_start = '';
                $event_end = '';
            } elseif ($line === 'END:VEVENT') {
                $in_event = false;
                // Process event
                if ($event_start && $event_end) {
                    // Check if event is on the requested date
                    $e_start_date = date('Y-m-d', $event_start);
                    $e_end_date = date('Y-m-d', $event_end);
                    
                    if ($e_start_date === $date || $e_end_date === $date || ($date > $e_start_date && $date < $e_end_date)) {
                        // Event overlaps with today. Block out slots.
                        foreach ($base_slots as $slot) {
                            $slot_time = strtotime($date . ' ' . $slot);
                            if ($slot_time >= $event_start && $slot_time < $event_end) {
                                $blocked_slots[] = $slot;
                            }
                        }
                    }
                }
            } elseif ($in_event) {
                if (strpos($line, 'DTSTART') === 0) {
                    $parts = explode(':', $line);
                    if (isset($parts[1])) {
                        $val = trim($parts[1]);
                        $event_start = strtotime($val);
                    }
                } elseif (strpos($line, 'DTEND') === 0) {
                    $parts = explode(':', $line);
                    if (isset($parts[1])) {
                        $val = trim($parts[1]);
                        $event_end = strtotime($val);
                    }
                }
            }
        }
    }
}

// Filter base slots
$available_slots = [];
foreach ($base_slots as $slot) {
    if (!in_array($slot, $blocked_slots)) {
        // Format for display
        $available_slots[] = date('g:ia', strtotime($slot));
    }
}

// If no slots available
if (empty($available_slots)) {
    echo json_encode(['slots' => [], 'message' => 'No availability on this date.']);
    exit;
}

echo json_encode(['slots' => $available_slots]);
?>
