<?php
require_once 'includes/database.php';
require_once 'app/Controllers/AuthController.php';

use App\Controllers\AuthController;

$email = $_GET['email'] ?? '';
$error = '';
$success = '';

if (!$email) {
    header("Location: register.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    
    if ($code) {
        $auth = new AuthController($db);
        try {
            $auth->verifyEmail($email, $code);
            $success = "Your email has been verified! You can now log in.";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "Please enter the 6-digit code.";
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<section style="padding: 6rem 0; min-height: 70vh; display: flex; align-items: center;">
    <div class="container">
        <div class="booking-form" style="background: var(--white); padding: 4rem; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
            <h2 class="text-center" style="margin-bottom: 2rem;">Verify Your Email</h2>
            
            <?php if ($success): ?>
                <div style="color: green; margin-bottom: 2rem; text-align: center; font-size: 18px;"><?php echo htmlspecialchars($success); ?></div>
                <a href="client-login.php" class="btn btn-primary" style="display: block; text-align: center;">Continue to Login</a>
            <?php else: ?>
                <p class="text-center" style="color: var(--secondary); margin-bottom: 2rem;">We sent a 6-digit code to <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                <?php if ($error): ?><div style="color: red; margin-bottom: 1rem; text-align: center;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label style="text-align: center;">6-Digit Code</label>
                        <input type="text" name="code" class="form-control" style="font-size: 24px; text-align: center; letter-spacing: 5px; max-width: 300px; margin: 0 auto;" maxlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; max-width: 300px; margin: 1rem auto; display: block;">Verify</button>
                </form>
                
                <div style="text-align: center; margin-top: 2rem; font-size: 12px; color: #888;">
                    <p><strong>Development Sandbox Mode:</strong><br> Since SMTP might not be configured, you can find your OTP code inside the <code>public/verification_codes.txt</code> file!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
