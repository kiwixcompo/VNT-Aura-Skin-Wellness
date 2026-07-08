<?php
require_once __DIR__ . '/db.php';

$seoTitle = get_setting($pdo, 'seo_title', 'VNT Aura Skin & Wellness');
$seoDesc = get_setting($pdo, 'seo_description', 'Personalised skin consultations and evidence-based skin treatments.');
$calendlyUrl = get_setting($pdo, 'calendly_url', 'https://calendly.com/vnt-aura-skin-wellness');

        $themePrimary = get_setting($pdo, 'theme_primary', '#FFFFFF');
        $themeSecondary = get_setting($pdo, 'theme_secondary', '#000000');
        $themeAccent = get_setting($pdo, 'theme_accent', '#333333');
        $themeBg = get_setting($pdo, 'theme_bg', '#FFFFFF');
        $themeText = get_setting($pdo, 'theme_text', '#000000');
        
        $siteLogo = get_setting($pdo, 'site_logo', '');
        $siteFavicon = get_setting($pdo, 'site_favicon', '');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($seoTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($seoDesc) ?>">
        <?php if (!empty($siteFavicon)): ?>
        <link rel="icon" href="<?= htmlspecialchars($siteFavicon) ?>" type="image/png">
    <?php elseif (!empty($siteLogo)): ?>
        <link rel="icon" href="<?= htmlspecialchars($siteLogo) ?>" type="image/png">
    <?php else: ?>
        <link rel="icon" href="assets/images/favicon.png" type="image/png">
    <?php endif; ?>
    
    <!-- Tailwind CSS (CDN for development) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?= htmlspecialchars($themePrimary) ?>',
                        secondary: '<?= htmlspecialchars($themeSecondary) ?>',
                        accent: '<?= htmlspecialchars($themeAccent) ?>',
                        bg: '<?= htmlspecialchars($themeBg) ?>',
                        text: '<?= htmlspecialchars($themeText) ?>'
                    },
                    fontFamily: {
                        heading: ['"Cormorant Garamond"', 'serif'],
                        body: ['"Inter"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- GSAP & ScrollTrigger -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Calendly link widget -->
    <link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
    <script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript" async></script>
    <script>
        function openCalendly() {
            Calendly.initPopupWidget({url: '<?= htmlspecialchars($calendlyUrl ?? 'https://calendly.com/vnt-aura-skin-wellness') ?>'});
            return false;
        }
    </script>
    
    <script>
        const globalBookingMode = '<?= htmlspecialchars(get_setting($pdo, "booking_mode", "faces")) ?>';
        const globalFacesUrl = '<?= htmlspecialchars(get_setting($pdo, "faces_url", "")) ?>';

        function openBookingModal(serviceName = null, serviceFacesUrl = null) {
            // Check if Faces is enabled
            let targetUrl = serviceFacesUrl && serviceFacesUrl.trim() !== '' ? serviceFacesUrl : globalFacesUrl;
            
            if (globalBookingMode === 'faces' && targetUrl) {
                const iframe = document.getElementById('facesIframe');
                if (iframe) {
                    document.getElementById('facesLoader').style.display = 'flex';
                    iframe.src = targetUrl;
                }
                const facesModal = document.getElementById('facesModal');
                if (facesModal) {
                    facesModal.classList.remove('hidden');
                    facesModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                } else {
                    // Fallback just in case
                    window.open(targetUrl, '_blank');
                }
                
                const mobileMenu = document.getElementById('mobile-menu');
                if(mobileMenu) {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('flex');
                }
                return false;
            }

            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.classList.remove('hidden');
                bookingModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
            
            const mobileMenu = document.getElementById('mobile-menu');
            if(mobileMenu) {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
            }
            return false;
        }
        function closeBookingModal() {
            document.getElementById('bookingModal').classList.remove('flex');
            document.getElementById('bookingModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
        
        // Close mobile menu on link click
        document.addEventListener('DOMContentLoaded', () => {
            const mobileLinks = document.querySelectorAll('#mobile-menu a');
            const mobileMenu = document.getElementById('mobile-menu');
            mobileLinks.forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('flex');
                });
            });
        });
    </script>
</head>
<body class="bg-bg text-text antialiased">

<!-- Navigation -->
<header class="fixed w-full top-0 z-50 sylk-nav transition-all duration-300 border-b border-black/5 <?= htmlspecialchars($navClass ?? '') ?>">
    <div class="max-w-7xl mx-auto px-8 py-5 flex justify-between items-center">
        <!-- Logo -->
        <a href="index.php" class="w-32 md:w-40 flex items-center">
            <?php if (!empty($siteLogo)): ?>
                <img src="<?= htmlspecialchars($siteLogo) ?>" alt="VNT Aura Logo" class="w-full h-auto object-contain">
            <?php else: ?>
                <span class="font-heading text-3xl text-text tracking-widest leading-none">VNT AURA</span>
            <?php endif; ?>
        </a>
        
        <!-- Desktop Menu -->
        <nav class="hidden md:flex space-x-10 items-center">
            <a href="#home" class="text-text hover:opacity-70 transition-colors font-light text-[15px]">Home</a>
            <a href="#treatments" class="text-text hover:opacity-70 transition-colors font-light text-[15px]">Advanced Skin Therapies</a>
            <a href="#programmes" class="text-text hover:opacity-70 transition-colors font-light text-[15px]">Packages</a>
            <a href="#founder" class="text-text hover:opacity-70 transition-colors font-light text-[15px]">Meet Valerie</a>
            <a href="#contact" class="text-text hover:opacity-70 transition-colors font-light text-[15px]">Contact</a>
        </nav>

        <!-- Right Icons -->
        <div class="hidden md:flex items-center space-x-6">
            <a href="admin/login.php" class="text-text hover:opacity-70 transition-colors text-lg" title="Admin Login"><i class="fa-regular fa-user"></i></a>
            <a href="#" onclick="openBookingModal(); return false;" class="text-text hover:opacity-70 transition-colors text-lg" title="Book Appointment"><i class="fa-regular fa-calendar-check"></i></a>
        </div>
        
        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-btn" class="md:hidden text-2xl text-text">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="hidden absolute top-full left-0 w-full bg-bg/95 backdrop-blur-md shadow-xl flex-col items-center py-8 space-y-6">
        <a href="#home" class="text-lg text-text font-light">Home</a>
        <a href="#treatments" class="text-lg text-text font-light">Advanced Skin Therapies</a>
        <a href="#programmes" class="text-lg text-text font-light">Packages</a>
        <a href="#founder" class="text-lg text-text font-light">Meet Valerie</a>
        <a href="#contact" class="text-lg text-text font-light">Contact</a>
    </div>
</header>
