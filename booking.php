<?php 
require_once 'includes/header.php'; 

$services = [
    'consultation' => ['name' => 'Signature Skin Consultation', 'price' => 50],
    'bespoke' => ['name' => 'Aura Bespoke Facial™', 'price' => 120],
    'renewal' => ['name' => 'Aura Skin Renewal™', 'price' => 150],
    'refining' => ['name' => 'Aura Skin Refining™', 'price' => 95]
];

$selected_service = $_GET['service'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $date = $_POST['date'] ?? '';
    $service_key = $_POST['service'] ?? '';

    if (!empty($name) && !empty($email) && array_key_exists($service_key, $services)) {
        try {
            // Save as Pending
            $stmt = $db->prepare("INSERT INTO bookings (name, email, phone, preferred_date, concerns, status) VALUES (?, ?, ?, ?, ?, 'Pending Payment')");
            $stmt->execute([$name, $email, $phone, $date, $services[$service_key]['name']]);
            $booking_id = $db->lastInsertId();
            
            // Redirect to mock checkout
            header("Location: checkout.php?id=$booking_id&amount=" . $services[$service_key]['price']);
            exit;
        } catch (Exception $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields and select a valid service.";
    }
}
?>

<section style="padding: 4rem 0;">
    <div class="container">
        <h2 class="section-title">Book Your Appointment</h2>
        <div class="booking-form" style="background: var(--white); padding: 3rem; border: 1px solid var(--border);">
            <?php if ($error): ?><div style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></div><?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Select Service</label>
                    <select name="service" class="form-control" required>
                        <option value="">-- Choose a Service --</option>
                        <?php foreach($services as $key => $s): ?>
                            <option value="<?php echo $key; ?>" <?php if($selected_service === $key) echo 'selected'; ?>>
                                <?php echo $s['name']; ?> - £<?php echo number_format($s['price'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Preferred Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Continue to Payment</button>
            </form>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
