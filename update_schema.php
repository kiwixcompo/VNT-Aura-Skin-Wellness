<?php
require_once __DIR__ . '/includes/db.php';

echo "<h2>Checking and Updating Database Schema...</h2>";

try {
    // 1. Add duration to treatments if missing
    $colCheck = $pdo->query("SHOW COLUMNS FROM treatments LIKE 'duration'");
    if ($colCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE treatments ADD COLUMN duration VARCHAR(50) DEFAULT '1 hr'");
        echo "Added 'duration' to treatments.<br>";
    } else {
        echo "'duration' already exists in treatments.<br>";
    }

    // 2. Add aftercare_email_content to treatments
    $colCheck = $pdo->query("SHOW COLUMNS FROM treatments LIKE 'aftercare_email_content'");
    if ($colCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE treatments ADD COLUMN aftercare_email_content TEXT NULL");
        echo "Added 'aftercare_email_content' to treatments.<br>";
    }

    // 3. Add aftercare_email_content to programmes
    $colCheck = $pdo->query("SHOW COLUMNS FROM programmes LIKE 'aftercare_email_content'");
    if ($colCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE programmes ADD COLUMN aftercare_email_content TEXT NULL");
        echo "Added 'aftercare_email_content' to programmes.<br>";
    }

    // 4. Update bookings table
    $colCheck = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'duration'");
    if ($colCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE bookings ADD COLUMN duration VARCHAR(50) DEFAULT '1 hr'");
        echo "Added 'duration' to bookings.<br>";
    }
    
    $colCheck = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'aftercare_sent'");
    if ($colCheck->rowCount() == 0) {
        $pdo->exec("ALTER TABLE bookings ADD COLUMN aftercare_sent TINYINT(1) DEFAULT 0");
        echo "Added 'aftercare_sent' to bookings.<br>";
    }

    // 5. Create booking_intake_forms table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS booking_intake_forms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        dob_day VARCHAR(2),
        dob_month VARCHAR(2),
        dob_year VARCHAR(4),
        address TEXT,
        gender VARCHAR(20),
        height VARCHAR(20),
        weight VARCHAR(20),
        gp_details TEXT,
        has_medical_conditions VARCHAR(10),
        medical_history TEXT,
        signature_name VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
    )");
    echo "Checked/Created booking_intake_forms table.<br>";

    echo "<h3 style='color:green;'>Schema Update Complete! Everything is perfectly synced.</h3>";

} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Error updating schema: " . $e->getMessage() . "</h3>";
}
