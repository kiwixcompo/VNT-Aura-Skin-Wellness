<?php require_once 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo get_setting($db, 'hero_headline', 'Healthy Skin.<br><em>Calm Confidence.</em>'); ?></h1>
                <p><?php echo htmlspecialchars(get_setting($db, 'hero_subheadline', 'Personalised skin consultations and evidence-based skin treatments designed to improve skin health and help you achieve clearer, brighter, healthier-looking skin.')); ?></p>
                <div class="hero-buttons">
                    <a href="booking.php" class="btn btn-primary">Book Consultation</a>
                    <a href="treatments.php" class="btn btn-outline">Explore Treatments</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?php echo htmlspecialchars(get_setting($db, 'hero_image', 'assets/images/hero.png')); ?>" alt="Woman with glowing healthy skin">
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-us" id="why-us">
        <div class="container">
            <h2 class="text-center section-title">Why Choose VNT Aura</h2>
            <div class="why-grid">
                <div class="why-item">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Personalised treatment plans</span>
                </div>
                <div class="why-item">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>One-to-one client care</span>
                </div>
                <div class="why-item">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Professional skin consultations</span>
                </div>
                <div class="why-item">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Evidence-based skincare approach</span>
                </div>
                <div class="why-item">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Focus on long-term skin health</span>
                </div>
                <div class="why-item">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Tailored homecare recommendations</span>
                </div>
                <div class="why-item">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Inclusive approach for all skin tones</span>
                </div>
                <div class="why-item">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Calm, welcoming and relaxing environment</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq" id="faq">
        <div class="container">
            <h2 class="text-center section-title">Frequently Asked Questions</h2>
            <div class="faq-accordion">
                <details class="faq-item" name="faq">
                    <summary>Do I need a consultation first? <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="faq-content">
                        <p>Yes. Every new client begins with a consultation so we can assess your skin and recommend the most appropriate treatment plan.</p>
                    </div>
                </details>
                <details class="faq-item" name="faq">
                    <summary>How many treatments will I need? <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="faq-content">
                        <p>Treatment recommendations vary depending on your skin concerns, goals and how your skin responds throughout your programme.</p>
                    </div>
                </details>
                <details class="faq-item" name="faq">
                    <summary>Is there any downtime? <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="faq-content">
                        <p>Some treatments may involve temporary redness or sensitivity. Full aftercare guidance will be provided.</p>
                    </div>
                </details>
                <details class="faq-item" name="faq">
                    <summary>Can I receive treatment during pregnancy? <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="faq-content">
                        <p>Some treatments may not be suitable during pregnancy or breastfeeding. Please contact us before booking.</p>
                    </div>
                </details>
                <details class="faq-item" name="faq">
                    <summary>Do you treat all skin tones? <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="faq-content">
                        <p>Yes. Treatments are tailored to your individual skin type, concerns and goals following consultation.</p>
                    </div>
                </details>
                <details class="faq-item" name="faq">
                    <summary>Do you offer personalised treatment programmes? <i class="fa-solid fa-chevron-down"></i></summary>
                    <div class="faq-content">
                        <p>Yes. Every programme is bespoke and tailored to your individual skin needs and goals.</p>
                    </div>
                </details>
            </div>
        </div>
    </section>

    <!-- Booking CTA Section -->
    <section class="booking-cta" id="booking">
        <div class="container text-center">
            <h2>Ready to Begin Your Skin Journey?</h2>
            <p>Book your consultation today and take the first step towards healthier, more radiant skin.</p>
            <div class="booking-buttons">
                <a href="booking.php" class="btn btn-primary">Book Consultation</a>
                <a href="mailto:valeriescorner@gmail.com" class="btn btn-outline" style="border-color: white; color: white;">Contact Us</a>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
 
