<?php
require_once 'includes/database.php';
require_once 'app/Controllers/AuthController.php';

use App\Controllers\AuthController;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        $auth = new AuthController($db);
        try {
            $userId = $auth->login($email, $password);
            
            // Success: In a real app, set $_SESSION['user_id'] = $userId;
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            if ($e->getMessage() === 'UNVERIFIED') {
                header("Location: verify-email.php?email=" . urlencode($email));
                exit;
            } else {
                $error = $e->getMessage();
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<section style="padding: 6rem 0; min-height: 70vh; display: flex; align-items: center;">
    <div class="container">
        <div class="booking-form" style="background: var(--white); padding: 4rem; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
            <h2 class="text-center" style="margin-bottom: 2rem;">Client Login</h2>
            
            <?php if ($error): ?><div style="color: red; margin-bottom: 1rem; text-align: center;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Log In</button>
            </form>
            <p class="text-center" style="margin-top: 2rem; font-size: 14px;">Don't have an account? <a href="register.php" style="color: var(--accent); text-decoration: underline;">Register here</a></p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
