<?php
require_once __DIR__ . '/includes/db.php';

echo "Starting database migration...<br>";

// Create FAQs table
$pdo->exec("
CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "FAQs table checked/created.<br>";

// Create Testimonials table
$pdo->exec("
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    feedback TEXT NOT NULL,
    rating INT DEFAULT 5,
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "Testimonials table checked/created.<br>";

// Create Gallery table
$pdo->exec("
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT '',
    image_url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "Gallery table checked/created.<br>";

// Also ensure `display_order` exists on faqs in case it was created with `sort_order`
try {
    $pdo->exec("ALTER TABLE faqs CHANGE sort_order display_order INT DEFAULT 0");
    echo "Fixed old sort_order column in FAQs.<br>";
} catch (PDOException $e) {
    // Column already exists or doesn't exist to rename, ignore safely
}

// Seed FAQs if empty
$stmt = $pdo->query('SELECT COUNT(*) FROM faqs');
if ($stmt->fetchColumn() == 0) {
    $faqs = [
        ['What should I expect during my first consultation?', 'During your first consultation, we will discuss your skin concerns, goals, current skincare routine, and lifestyle. This helps us create a personalised treatment plan tailored to your needs.', 1],
        ['Do you offer treatments for acne-prone skin?', 'Yes, we offer specialized treatments and programmes designed to clarify and balance acne-prone skin, alongside professional homecare recommendations.', 2],
        ['How often should I get a facial?', 'For optimal results, we generally recommend a professional treatment every 4-6 weeks, as this aligns with your skin\'s natural renewal cycle. However, this can vary based on your specific skin journey.', 3]
    ];
    $stmt = $pdo->prepare('INSERT INTO faqs (question, answer, display_order) VALUES (?, ?, ?)');
    foreach ($faqs as $faq) $stmt->execute($faq);
    echo "Seeded FAQs.<br>";
}

// Seed Testimonials if empty
$stmt = $pdo->query('SELECT COUNT(*) FROM testimonials');
if ($stmt->fetchColumn() == 0) {
    $testimonials = [
        ['Sarah M.', 'Valerie completely transformed my skin. The Aura Glow journey was exactly what I needed. My skin has never looked so healthy and radiant!', 5, 1],
        ['Jessica T.', 'I struggled with adult acne for years. After just a few months of following my personalised treatment plan, my skin is clear and I finally feel confident without makeup.', 5, 2],
        ['Elena R.', 'The bespoke facial was incredibly relaxing, and Valerie\'s knowledge of skin health is unmatched. Highly recommend!', 5, 3]
    ];
    $stmt = $pdo->prepare('INSERT INTO testimonials (client_name, feedback, rating, display_order) VALUES (?, ?, ?, ?)');
    foreach ($testimonials as $test) $stmt->execute($test);
    echo "Seeded Testimonials.<br>";
}

// Seed Gallery if empty
$stmt = $pdo->query('SELECT COUNT(*) FROM gallery');
if ($stmt->fetchColumn() == 0) {
    $gallery = [
        ['Acne Treatment Results', 'https://images.unsplash.com/photo-1512496015851-a1c814df71d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 1],
        ['Skin Rejuvenation', 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 2],
        ['Hydration Therapy', 'https://images.unsplash.com/photo-1522337660859-02fbefca4702?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 3]
    ];
    $stmt = $pdo->prepare('INSERT INTO gallery (title, image_url, display_order) VALUES (?, ?, ?)');
    foreach ($gallery as $gal) $stmt->execute($gal);
    echo "Seeded Gallery.<br>";
}

echo "<br><strong>Migration completed successfully! You can now safely delete this file.</strong>";
