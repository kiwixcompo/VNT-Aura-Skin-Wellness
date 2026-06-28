<?php require_once 'includes/header.php'; ?>

    <!-- Signature Treatments Section -->
    <section class="treatments" id="treatments" style="padding-top: 4rem;">
        <div class="container">
            <h2 class="text-center section-title">Signature Treatments</h2>
            <div class="treatments-grid">
                <div class="treatment-card">
                    <div class="treatment-icon"><i class="fa-solid fa-clipboard-user"></i></div>
                    <h3>Skin Consultation</h3>
                    <p>Your personalised skin assessment and treatment planning session.</p>
                    <div class="treatment-details">
                        <strong>Perfect for:</strong>
                        <ul>
                            <li>First-time clients</li>
                            <li>Acne concerns</li>
                            <li>Hyperpigmentation</li>
                            <li>Skin ageing</li>
                            <li>Treatment planning</li>
                        </ul>
                    </div>
                </div>
                <div class="treatment-card">
                    <div class="treatment-icon"><i class="fa-solid fa-spa"></i></div>
                    <h3>Aura Bespoke Facial™</h3>
                    <p>Our signature customised facial experience tailored to your skin’s needs on the day of treatment.</p>
                    <div class="treatment-details">
                        <strong>Suitable for:</strong>
                        <ul>
                            <li>Dull skin</li>
                            <li>Dehydrated skin</li>
                            <li>Sensitive skin</li>
                            <li>Skin maintenance</li>
                            <li>Healthy skin support</li>
                        </ul>
                    </div>
                </div>
                <div class="treatment-card">
                    <div class="treatment-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                    <h3>Aura Skin Renewal™ <br><span>(Microneedling)</span></h3>
                    <p>Our signature collagen-induction treatment.</p>
                    <div class="treatment-details">
                        <strong>Designed to improve:</strong>
                        <ul>
                            <li>Acne scarring</li>
                            <li>Fine lines</li>
                            <li>Enlarged pores</li>
                            <li>Skin texture</li>
                        </ul>
                    </div>
                </div>
                <div class="treatment-card">
                    <div class="treatment-icon"><i class="fa-solid fa-droplet"></i></div>
                    <h3>Aura Skin Refining™ <br><span>(Chemical Peel)</span></h3>
                    <p>Our signature skin resurfacing treatment.</p>
                    <div class="treatment-details">
                        <strong>Designed to improve:</strong>
                        <ul>
                            <li>Hyperpigmentation</li>
                            <li>Acne-prone skin</li>
                            <li>Uneven skin tone</li>
                            <li>Dull skin</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Signature Programmes Section -->
    <section class="programmes" id="programmes">
        <div class="container">
            <h2 class="text-center section-title">Signature Programmes</h2>
            <p class="text-center programmes-intro">Every programme is personalised following your consultation and may include a combination of treatments and homecare recommendations.</p>
            
            <div class="programmes-layout">
                <div class="programmes-image">
                    <img src="<?php echo htmlspecialchars(get_setting($db, 'programmes_image', 'assets/images/programmes.png')); ?>" alt="VNT Aura Skincare Programmes">
                </div>
                <div class="programmes-list">
                    <div class="programme-item">
                        <i class="fa-solid fa-star"></i>
                        <div class="programme-content">
                            <h4>The Glow Programme™</h4>
                            <p>Designed for healthy, hydrated and radiant skin.<br><em>Ideal for: Dull skin, Dry skin, Dehydration, Skin maintenance</em></p>
                        </div>
                    </div>
                    <div class="programme-item">
                        <i class="fa-solid fa-leaf"></i>
                        <div class="programme-content">
                            <h4>The Clarity Programme™</h4>
                            <p>Designed for acne-prone and congested skin.<br><em>Ideal for: Acne, Congestion, Blackheads, Oily skin, Post-acne marks</em></p>
                        </div>
                    </div>
                    <div class="programme-item">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                        <div class="programme-content">
                            <h4>The Even Tone Programme™</h4>
                            <p>Designed to improve pigmentation and uneven skin tone.<br><em>Ideal for: Hyperpigmentation, Uneven skin tone, Acne marks, Dull skin</em></p>
                        </div>
                    </div>
                    <div class="programme-item">
                        <i class="fa-solid fa-hourglass-half"></i>
                        <div class="programme-content">
                            <h4>The Timeless Programme™</h4>
                            <p>Designed to improve skin texture while supporting healthier, firmer and more youthful-looking skin.<br><em>Ideal for: Fine lines, Loss of firmness, Uneven texture, Early signs of ageing</em></p>
                        </div>
                    </div>
                    <div class="programme-item highlight">
                        <i class="fa-solid fa-crown"></i>
                        <div class="programme-content">
                            <h4>The Confidence Programme™</h4>
                            <p>Our most personalised programme. Designed for clients with multiple skin concerns or those beginning their skincare journey.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
