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


// Seed Settings if empty
$stmt = $pdo->query('SELECT COUNT(*) FROM settings');
if ($stmt->fetchColumn() == 0) {
    $defaultSettings = [
        'hero_video_type' => 'url',
        'hero_video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
        'hero_video_upload' => '',
        'hero_video_start' => '0',
        'hero_video_end' => '',
        'hero_video_pos_x' => '50',
        'hero_video_pos_y' => '50',
        'founder_image_type' => 'url',
        'founder_image_url' => 'assets/images/founder.jpg',
        'founder_image_upload' => '',
        'founder_pos_x' => '50',
        'founder_pos_y' => '50',
        'seo_title' => 'VNT Aura Skin & Wellness',
        'seo_description' => 'Personalised skin consultations and evidence-based skin treatments.',
        'consultation_title' => 'New Client Consultation',
        'consultation_subtitle' => 'Every skin journey starts with a consultation.',
        'consultation_text' => 'Your consultation includes:\n* Skin assessment\n* Discussion of concerns and goals\n* Lifestyle and skincare review\n* Treatment recommendations\n* Personalised treatment plan\n* Homecare recommendations',
        'consultation_price' => '£20',
        'consultation_note' => '(Redeemable against treatment booked on the day)',
        'consultation_bullets' => '',
        'journeys_title' => 'Our Skin Journeys',
        'journeys_subtitle' => 'Curated Experiences',
        'journeys_text' => "We don't just perform treatments; we deliver results. Our skin journeys combine multiple modalities over a set period to fundamentally transform your skin.",
        'therapies_title' => 'Advanced Skin Therapies',
        'therapies_subtitle' => 'Clinical Excellence',
        'therapies_text' => 'We focus on long-term cellular health rather than quick fixes. Explore our curated, evidence-based therapies designed to restore, rebuild, and protect your skin.',
        'about_title' => 'Where Science Meets Luxury',
        'about_text1' => 'At VNT Aura Skin & Wellness, we believe that healthy skin is the foundation of true beauty. Our approach combines advanced clinical treatments with a luxurious, deeply relaxing experience.',
        'about_text2' => 'Every treatment is bespoke, tailored to your unique skin type, concerns, and lifestyle. We utilise cutting-edge technology alongside potent, evidence-based skincare formulations to deliver visible, long-lasting results.',
        'about_text3' => 'Step into our sanctuary and let us guide you on a transformative journey to your most radiant, confident self.',
        'about_quote' => '"Beautiful skin requires commitment, not a miracle."',
        'about_author' => 'Erno Laszlo',
        'bio_title' => 'Meet Valerie',
        'bio_subtitle' => 'Founder & Lead Skin Therapist',
        'bio_text' => "With over a decade of experience in the aesthetics industry, Valerie's passion lies in empowering individuals through skin health.\n\nHer philosophy is rooted in education, transparency, and a holistic approach to skincare. She continually updates her knowledge with the latest advancements in dermatological science to provide her clients with the highest standard of care.",
        'results_title' => 'Real Client Results',
        'faqs_title' => 'Frequently Asked Questions',
        'contact_title' => 'Contact & Location',
        'contact_address' => '123 Wellness Way, London, UK',
        'contact_phone' => '+44 (0) 20 1234 5678',
        'contact_email_display' => 'hello@vntaura.com',
        'contact_hours' => "Monday: Closed\nTuesday - Friday: 10am - 7pm\nSaturday: 9am - 5pm\nSunday: Closed",
        'admin_email' => 'admin@vntaura.com',
        'notify_admin' => '1',
        'notify_client' => '1',
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => '587',
        'smtp_username' => '',
        'smtp_password' => '',
        'theme_primary' => '#D1C5B4',
        'theme_secondary' => '#2C362F',
        'theme_accent' => '#A58B75',
        'theme_bg' => '#FAF9F6',
        'theme_text' => '#1F2421',
        'site_logo' => ''
    ];
    
    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)');
    foreach ($defaultSettings as $key => $val) {
        $stmt->execute([$key, $val]);
    }
    echo "Seeded Settings.<br>";
}


echo "<br><strong>Migration completed successfully! You can now safely delete this file.</strong>";
