<?php
// Enable error logging for the entire website
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');
error_reporting(E_ALL);

$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(sprintf('%s=%s', trim($name), trim($value)));
        $_ENV[trim($name)] = trim($value);
    }
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'vnt_aura_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Instead of throwing a fatal 500 error, show a friendly message for debugging
    die("Database Connection Error: " . $e->getMessage() . "<br><br><strong>Note:</strong> If you just uploaded this to a live server, you need to update your database credentials (DB_NAME, DB_USER, DB_PASS) in the .env file or directly in includes/db.php to match your live hosting environment.");
}

function get_setting($pdo, $key, $default = null) {
    $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $res = $stmt->fetch();
    return $res ? $res['setting_value'] : $default;
}
?>
