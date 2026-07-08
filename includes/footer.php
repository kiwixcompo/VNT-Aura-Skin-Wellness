<footer class="bg-primary text-secondary py-16">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12 text-center md:text-left">
        <!-- Brand -->
        <div class="space-y-4 reveal-up">
            <h3 class="text-3xl font-heading tracking-wider">VNT Aura Skin & Wellness</h3>
            <p class="text-secondary/80 font-light italic text-lg">Healthy Skin. Calm Confidence.</p>
        </div>
        
        <!-- Contact Info -->
        <div class="space-y-4 reveal-up">
            <h4 class="text-xl font-heading tracking-widest uppercase text-secondary">Contact</h4>
            <p class="font-light">Leeds, United Kingdom</p>
            <p class="font-light"><a href="mailto:vntauraskinandwellness@gmail.com" class="hover:text-white transition-colors">vntauraskinandwellness@gmail.com</a></p>
            <?php
                $social_instagram = get_setting($pdo, 'social_instagram', 'https://instagram.com/vntaura');
                $social_facebook = get_setting($pdo, 'social_facebook', 'https://facebook.com/vntaura');
                $social_tiktok = get_setting($pdo, 'social_tiktok', 'https://tiktok.com/@vntaura');
                $social_twitter = get_setting($pdo, 'social_twitter', 'https://twitter.com/vntaura');
            ?>
            <div class="flex justify-center md:justify-start space-x-4 pt-2">
                <?php if($social_instagram): ?>
                    <a href="<?= htmlspecialchars($social_instagram) ?>" class="text-secondary hover:text-white transition-colors text-xl" target="_blank"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if($social_facebook): ?>
                    <a href="<?= htmlspecialchars($social_facebook) ?>" class="text-secondary hover:text-white transition-colors text-xl" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if($social_tiktok): ?>
                    <a href="<?= htmlspecialchars($social_tiktok) ?>" class="text-secondary hover:text-white transition-colors text-xl" target="_blank"><i class="fab fa-tiktok"></i></a>
                <?php endif; ?>
                <?php if($social_twitter): ?>
                    <a href="<?= htmlspecialchars($social_twitter) ?>" class="text-secondary hover:text-white transition-colors text-xl" target="_blank"><i class="fab fa-twitter"></i></a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Links & Disclaimer -->
        <div class="space-y-4 reveal-up">
            <h4 class="text-xl font-heading tracking-widest uppercase text-secondary">Information</h4>
            <p class="font-light text-sm text-secondary/80 leading-relaxed">
                Disclaimer: Results vary between individuals. Treatment recommendations are made following a professional consultation and skin assessment.
            </p>
            <div class="pt-4 flex space-x-4 justify-center md:justify-start text-sm text-secondary/80">
                <a href="privacy.php" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="terms.php" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
    <div class="mt-12 pt-8 border-t border-secondary/20 text-center text-sm text-secondary/60 font-light flex justify-center items-center gap-2">
        <p>&copy; <?= date('Y') ?> VNT Aura Skin & Wellness. All rights reserved.</p>
        <span>|</span>
        <a href="admin/login.php" class="text-secondary/60 hover:text-secondary transition-colors">Manage</a>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.19/bundled/lenis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
