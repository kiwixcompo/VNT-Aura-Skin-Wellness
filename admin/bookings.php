<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();


// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $stmt = $pdo->prepare('DELETE FROM bookings WHERE id = ?');
    $stmt->execute([$_POST['delete_booking_id']]);
    header('Location: bookings.php?msg=deleted');
    exit;
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $bookingId = $_POST['booking_id'];
    $newStatus = $_POST['status'];
    
    // Fetch current status and client details
    $stmt = $pdo->prepare('SELECT client_email, client_name, status, service, preferred_date, preferred_time FROM bookings WHERE id = ?');
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();
    
    if ($booking && $booking['status'] !== $newStatus) {
        $stmt = $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $bookingId]);
        
        // Fetch email settings
        $smtp_username = get_setting($pdo, 'smtp_username', '');
        $smtp_password = get_setting($pdo, 'smtp_password', '');
        
        if (!empty($smtp_username) && !empty($smtp_password)) {
            require_once __DIR__ . '/../vendor/autoload.php';
            $smtp_host = get_setting($pdo, 'smtp_host', 'smtp.gmail.com');
            $smtp_port = get_setting($pdo, 'smtp_port', '587');
            
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
                $mail->addAddress($booking['client_email']);

                $mail->isHTML(true);
                $mail->Subject = "Your Booking Status: " . ucfirst($newStatus);
                $mail->Body    = "
                    <h3>Hello " . htmlspecialchars($booking['client_name']) . ",</h3>
                    <p>The status of your booking for <strong>" . htmlspecialchars($booking['service']) . "</strong> on " . htmlspecialchars($booking['preferred_date']) . " at " . htmlspecialchars($booking['preferred_time']) . " has been updated to: <strong style='text-transform: uppercase;'>" . htmlspecialchars($newStatus) . "</strong>.</p>
                    <p>If you have any questions, please contact us.</p>
                    <br>
                    <p>Best regards,<br>VNT Aura Skin & Wellness</p>
                ";
                $mail->send();
            } catch (Exception $e) {
                // Log error or ignore
                error_log("Failed to send status update email: " . $mail->ErrorInfo);
            }
        }
    }
    
    header('Location: bookings.php?msg=updated');
    exit;
}

$stmt = $pdo->query('SELECT * FROM bookings ORDER BY created_at DESC');
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings - VNT Aura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    
    <!-- Sidebar -->
    <div class="fixed w-64 h-full bg-white border-r border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-xl font-bold tracking-wider uppercase text-gray-900">VNT Admin</h1>
        </div>
            <a href="index.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-cog w-6"></i> Settings</a>
                        <a href="bookings.php" class="block py-2 px-4 bg-gray-100 text-gray-900 font-medium rounded transition-colors"><i class="fas fa-calendar-alt w-6"></i> Bookings</a>
                        <a href="treatments.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-spa w-6"></i> Advanced Therapies</a>
                        <a href="programmes.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-layer-group w-6"></i> Skin Journeys</a>
                        
                        <p class="px-4 pt-4 text-xs font-bold text-gray-400 uppercase">CMS Content</p>
                        <a href="faqs.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-question-circle w-6"></i> FAQs</a>
                        <a href="testimonials.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-comment-dots w-6"></i> Testimonials</a>
                        <a href="gallery.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-images w-6"></i> Gallery</a>
            
                        <a href="logout.php" class="block py-2 px-4 text-red-600 hover:bg-red-50 rounded transition-colors mt-8"><i class="fas fa-sign-out-alt w-6"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <h2 class="text-3xl font-semibold mb-8">Booking Requests</h2>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-6 font-medium">Booking status updated successfully.</div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="p-4 font-medium text-gray-600">Client Info</th>
                        <th class="p-4 font-medium text-gray-600">Service & Date</th>
                        <th class="p-4 font-medium text-gray-600">Notes</th>
                        <th class="p-4 font-medium text-gray-600">Status</th>
                        <th class="p-4 font-medium text-gray-600 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (count($bookings) === 0): ?>
                        <tr><td colspan="5" class="p-8 text-center text-gray-500">No booking requests yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 align-top">
                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($b['client_name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($b['client_email']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($b['client_phone']) ?></div>
                                </td>
                                <td class="p-4 align-top">
                                    <div class="font-medium text-blue-900"><?= htmlspecialchars($b['service']) ?></div>
                                    <div class="text-sm text-gray-600"><?= date('F j, Y', strtotime($b['preferred_date'])) ?> at <?= htmlspecialchars($b['preferred_time']) ?></div>
                                    <div class="text-xs text-gray-400 mt-1">Requested: <?= date('M j g:ia', strtotime($b['created_at'])) ?></div>
                                </td>
                                <td class="p-4 align-top max-w-xs text-sm text-gray-600 truncate" title="<?= htmlspecialchars($b['notes']) ?>">
                                    <?= empty($b['notes']) ? '<em class="text-gray-400">None</em>' : htmlspecialchars($b['notes']) ?>
                                </td>
                                <td class="p-4 align-top">
                                    <?php 
                                        $color = $b['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 
                                                 ($b['status'] === 'Cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                                    ?>
                                    <span class="px-2 py-1 rounded text-xs font-medium <?= $color ?>"><?= $b['status'] ?></span>
                                </td>
                                <td class="p-4 align-top text-right">
                                    <form method="POST" action="bookings.php" class="inline-block">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <select name="status" class="border rounded px-2 py-1 text-sm bg-white" onchange="this.form.submit()">
                                            <option value="Pending" <?= $b['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Approved" <?= $b['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="Cancelled" <?= $b['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
