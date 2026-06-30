<?php
$db_file = __DIR__ . '/database.sqlite';
$is_new = !file_exists($db_file);

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($is_new) {
        // Create bookings table
        $db->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            preferred_date TEXT NOT NULL,
            concerns TEXT,
            status TEXT DEFAULT 'Pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Create settings table for dynamic images
        $db->exec("CREATE TABLE IF NOT EXISTS settings (
            key_name TEXT PRIMARY KEY,
            value TEXT
        )");

        // Insert default image paths
        $stmt = $db->prepare("INSERT INTO settings (key_name, value) VALUES (?, ?)");
        $stmt->execute(['hero_image', 'assets/images/hero.png']);
        $stmt->execute(['about_image', 'assets/images/about.png']);
        $stmt->execute(['treatments_image', 'assets/images/treatment.png']);
        $stmt->execute(['programmes_image', 'assets/images/programmes.png']);
        $stmt->execute(['founder_image', 'assets/images/valerie.png']);
    }

} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

// Helper function to get setting
function get_setting($db, $key, $default = '') {
    $stmt = $db->prepare("SELECT value FROM settings WHERE key_name = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['value'] : $default;
}
?>
 
