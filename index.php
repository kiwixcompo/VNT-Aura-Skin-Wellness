<?php require_once 'includes/header.php'; ?>

    <section class="hero">
        <div class="container">
            <h1><?php echo get_setting($db, 'hero_headline', 'Healthy Skin.<br><em>Calm Confidence.</em>'); ?></h1>
            <p><?php echo htmlspecialchars(get_setting($db, 'hero_subheadline', 'Personalised skin consultations and evidence-based skin treatments designed to improve skin health and help you achieve clearer, brighter, healthier-looking skin.')); ?></p>
            <a href="booking.php" class="btn btn-primary">Book Your Glow-Up</a>
            
            <div class="hero-image">
                <img src="<?php echo htmlspecialchars(get_setting($db, 'hero_image', 'assets/images/hero.png')); ?>" alt="VNT Aura Spa">
            </div>
        </div>
    </section>

    <section class="why-us">
        <div class="container">
            <h2 class="section-title">Why VNT Aura?</h2>
            <div class="split-layout">
                <div class="split-content">
                    <p>At VNT Aura, we are passionate about delivering expert beauty and skincare services in a serene, professional environment. Our mission is to empower each client to feel confident, refreshed, and radiant through personalized treatments that combine luxury with results.</p>
                    <p>We specialize in advanced facial treatments, targeted skincare solutions, and wellness therapies designed to enhance your natural beauty and restore balance from the inside out. Every service is performed by a licensed professional using premium products and proven techniques tailored to your unique needs.</p>
                    <p>At the heart of VNT Aura is a commitment to quality, consistency, and client care. Whether you’re seeking to improve your skin, unwind from daily stress, or maintain a healthy self-care routine, our goal is to provide a space where beauty meets professionalism—and every visit leaves you feeling your best.</p>
                    <div style="margin-top: 2rem;">
                        <h4 style="font-family: var(--font-body); font-weight: 500;">- <?php echo htmlspecialchars(explode(',', get_setting($db, 'founder_greeting', 'Valerie'))[0]); ?></h4>
                        <p style="font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Founder & C.E.O</p>
                    </div>
                </div>
                <div class="split-image">
                    <img src="<?php echo htmlspecialchars(get_setting($db, 'about_image', 'assets/images/about.png')); ?>" alt="Treatment Room">
                </div>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
