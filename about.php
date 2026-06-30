<?php require_once 'includes/header.php'; ?>

    <section class="founder" style="padding-top: 4rem;">
        <div class="container">
            <div class="split-layout">
                <div class="split-image">
                    <img src="<?php echo htmlspecialchars(get_setting($db, 'founder_image', 'assets/images/valerie_real.png')); ?>" alt="Valerie, Founder" loading="lazy">
                </div>
                <div class="split-content">
                    <h2>Meet Valerie</h2>
                    <h3 style="margin-bottom: 2rem; color: var(--accent);"><?php echo htmlspecialchars(get_setting($db, 'founder_greeting', 'Hi, I’m Valerie, founder of VNT Aura Skin & Wellness.')); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars(get_setting($db, 'founder_bio', 'For many years, I struggled to balance motherhood with taking care of myself. Like many women, I often placed everyone else’s needs before my own, and somewhere along the way, my confidence and self-care took a back seat.'))); ?></p>
                    <p>My personal journey taught me that self-care isn’t a luxury—it’s an important part of overall wellbeing. Looking after your skin can be the beginning of rebuilding confidence, creating healthy habits and making time for yourself again.</p>
                    <p>That’s why I founded VNT Aura Skin & Wellness: to create a welcoming space where women feel seen, supported and empowered throughout their skincare journey.</p>
                    <p>Every client receives personalised care because I believe there is no one-size-fits-all approach to healthy skin. My goal is to help you understand your skin, develop sustainable skincare habits and feel confident in your own skin—one treatment at a time.</p>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
