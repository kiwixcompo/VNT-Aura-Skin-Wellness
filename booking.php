<?php 
require_once 'includes/header.php'; 

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $date = $_POST['date'] ?? '';
    $concerns = $_POST['concerns'] ?? '';

    if (!empty($name) && !empty($email) && !empty($phone)) {
        try {
            $stmt = $db->prepare("INSERT INTO bookings (name, email, phone, preferred_date, concerns) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $date, $concerns]);
            
            // Email notification logic
            $to = "valeriescorner@gmail.com";
            $subject = "New Consultation Booking: " . $name;
            $body = "A new consultation has been booked.\n\nName: $name\nEmail: $email\nPhone: $phone\nPreferred Date: $date\nConcerns: $concerns";
            $headers = "From: no-reply@vntaura.com\r\n";
            
            // Attempt to send email (may fail on local WAMP without SMTP config, but we save the booking regardless)
            @mail($to, $subject, $body, $headers);
            
            $message = "<div class='alert success'>Thank you! Your consultation request has been received. We will contact you shortly.</div>";
        } catch (Exception $e) {
            $message = "<div class='alert error'>Error saving booking: " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert error'>Please fill in all required fields.</div>";
    }
}
?>

<style>
    .booking-page { padding: 4rem 0; background-color: var(--white); min-height: 70vh; }
    .booking-form-container { max-width: 600px; margin: 0 auto; background: var(--bg-color); padding: 3rem; border-radius: 12px; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-dark); }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-family: var(--font-body); }
    textarea.form-control { resize: vertical; min-height: 100px; }
    .btn-submit { width: 100%; text-align: center; margin-top: 1rem; }
    .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; text-align: center; }
    .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<section class="booking-page">
    <div class="container">
        <h2 class="text-center section-title">Book a Consultation</h2>
        <div class="booking-form-container">
            <?php echo $message; ?>
            <form action="booking.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="date">Preferred Date</label>
                    <input type="date" id="date" name="date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="concerns">Skin Concerns / Goals</label>
                    <textarea id="concerns" name="concerns" class="form-control" placeholder="Tell us briefly about your skin..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-submit">Submit Request</button>
            </form>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
