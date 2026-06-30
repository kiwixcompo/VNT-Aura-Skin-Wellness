<?php require_once __DIR__ . '/database.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VNT Aura Skin & Wellness</title>
    <meta name="description" content="Personalised skin consultations and evidence-based skin treatments designed to improve skin health.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header / Navigation -->
    <header>
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <img src="assets/images/logo.png" alt="VNT Aura Skin & Wellness">
            </a>
            
            <div class="menu-toggle" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

            <nav class="nav-links" id="nav-links">
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="treatments.php">Treatments</a>
                <a href="journey.php">Journey</a>
            </nav>
            <a href="booking.php" class="btn btn-outline">Book Consultation</a>
        </div>
    </header>
