<?php 
require_once 'includes/database.php';

$booking_id = $_GET['id'] ?? null;
$amount = $_GET['amount'] ?? 0;

if (!$booking_id || !$amount) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate successful payment processing
    // In a real scenario, this would communicate with Stripe/PayPal APIs
    
    // Update booking status
    $stmt = $db->prepare("UPDATE bookings SET status = 'Paid & Confirmed' WHERE id = ?");
    $stmt->execute([$booking_id]);
    
    // Redirect to success page
    header("Location: success.php?id=$booking_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Checkout - VNT Aura</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Jost', sans-serif; background-color: #fcfcfc; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #1c1c1c; }
        .checkout-box { background: white; padding: 3rem; border: 1px solid #e0e0e0; width: 100%; max-width: 450px; text-align: left; }
        h2 { margin-top: 0; font-weight: 500; font-size: 1.5rem; text-align: center; margin-bottom: 2rem; }
        .amount-display { text-align: center; font-size: 2rem; margin-bottom: 2rem; color: #B4975A; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #5e5e5e; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; font-family: 'Jost', sans-serif; box-sizing: border-box; }
        .row { display: flex; gap: 1rem; }
        .btn-pay { display: block; width: 100%; padding: 15px; background: #1c1c1c; color: white; border: none; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; font-family: 'Jost', sans-serif; margin-top: 2rem; transition: 0.3s; }
        .btn-pay:hover { background: #5e5e5e; }
        .secure-badge { text-align: center; font-size: 11px; color: #888; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <div class="checkout-box">
        <h2>Secure Checkout</h2>
        <div class="amount-display">£<?php echo number_format($amount, 2); ?></div>
        
        <form method="POST">
            <div class="form-group">
                <label>Card Number (Test Mode)</label>
                <input type="text" class="form-control" placeholder="4242 4242 4242 4242" required>
            </div>
            <div class="row">
                <div class="form-group" style="flex: 1;">
                    <label>Expiry</label>
                    <input type="text" class="form-control" placeholder="MM/YY" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>CVC</label>
                    <input type="text" class="form-control" placeholder="123" required>
                </div>
            </div>
            <div class="form-group">
                <label>Name on Card</label>
                <input type="text" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-pay">Pay £<?php echo number_format($amount, 2); ?></button>
        </form>
        
        <div class="secure-badge">
            🔒 Powered by Simulated Stripe Integration
        </div>
    </div>
</body>
</html>
