<?php require_once 'includes/header.php'; ?>

    <!-- About Section -->
    <section class="about" id="about" style="padding-top: 4rem;">
        <div class="container">
            <div class="about-image">
                <img src="<?php echo htmlspecialchars(get_setting($db, 'about_image', 'assets/images/about.png')); ?>" alt="VNT Aura treatment room">
            </div>
            <div class="about-content">
                <h2>About VNT Aura</h2>
                <p>At VNT Aura Skin & Wellness, we believe healthy skin is about more than appearance—it’s about confidence.</p>
                <p>We specialise in personalised skin consultations and professional treatments tailored to your unique skin concerns and goals.</p>
                <p>Whether you’re looking to improve acne-prone skin, pigmentation, dehydration, texture or signs of skin ageing, every treatment plan begins with understanding your skin and creating a personalised journey focused on long-term skin health.</p>
            </div>
        </div>
    </section>

    <!-- About the Founder Section -->
    <section class="founder" id="founder">
        <div class="container">
            <div class="founder-layout">
                <div class="founder-image">
                    <img src="<?php echo htmlspecialchars(get_setting($db, 'founder_image', 'assets/images/valerie.png')); ?>" alt="Valerie, Founder of VNT Aura">
                </div>
                <div class="founder-content">
                    <h2 class="section-title" style="text-align: left;">Meet Valerie</h2>
                    <h3><?php echo htmlspecialchars(get_setting($db, 'founder_greeting', 'Hi, I’m Valerie, founder of VNT Aura Skin & Wellness.')); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars(get_setting($db, 'founder_bio', 'For many years, I struggled to balance motherhood with taking care of myself. Like many women, I often placed everyone else’s needs before my own, and somewhere along the way, my confidence and self-care took a back seat.'))); ?></p>
                    <p>My personal journey taught me that self-care isn’t a luxury—it’s an important part of overall wellbeing. Looking after your skin can be the beginning of rebuilding confidence, creating healthy habits and making time for yourself again.</p>
                    <p>That’s why I founded VNT Aura Skin & Wellness: to create a welcoming space where women feel seen, supported and empowered throughout their skincare journey.</p>
                    <p>Every client receives personalised care because I believe there is no one-size-fits-all approach to healthy skin. My goal is to help you understand your skin, develop sustainable skincare habits and feel confident in your own skin—one treatment at a time.</p>
                    <p><strong>At VNT Aura, healthy skin is about more than appearance. It’s about helping women reconnect with themselves through skincare, self-care and overall wellness.</strong></p>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
