<?php
require_once __DIR__ . '/includes/header.php';

$heroVideoType = get_setting($pdo, 'hero_video_type', 'url');
$heroVideoUrl = get_setting($pdo, 'hero_video_url', 'https://www.w3schools.com/html/mov_bbb.mp4');
$heroVideoUpload = get_setting($pdo, 'hero_video_upload', '');
$heroPoster = get_setting($pdo, 'hero_poster', 'assets/images/placeholder-poster.jpg');

$vidStart = get_setting($pdo, 'hero_video_start', '0');
$vidEnd = get_setting($pdo, 'hero_video_end', '');
$vidX = get_setting($pdo, 'hero_video_pos_x', '50');
$vidY = get_setting($pdo, 'hero_video_pos_y', '50');

function safe_fetch_all($pdo, $query) {
    try {
        $stmt = $pdo->query($query);
        return $stmt ? $stmt->fetchAll() : [];
    } catch (PDOException $e) {
        // Fallback for missing tables or columns on live server
        return [];
    }
}

$treatments = safe_fetch_all($pdo, 'SELECT * FROM treatments ORDER BY display_order ASC, id ASC');
$programmes = safe_fetch_all($pdo, 'SELECT * FROM programmes ORDER BY display_order ASC, id ASC');
$faqs = safe_fetch_all($pdo, 'SELECT * FROM faqs ORDER BY display_order ASC, id ASC');
$testimonials = safe_fetch_all($pdo, 'SELECT * FROM testimonials ORDER BY display_order ASC, id ASC');
$gallery_items = safe_fetch_all($pdo, 'SELECT * FROM gallery ORDER BY display_order ASC, id ASC');


$videoSource = ($heroVideoType === 'upload' && !empty($heroVideoUpload)) ? $heroVideoUpload : $heroVideoUrl;
$objectStyle = "object-fit: cover; object-position: {$vidX}% {$vidY}%;";

// Founder image settings
$founderType = get_setting($pdo, 'founder_image_type', 'url');
$founderUrl = get_setting($pdo, 'founder_image_url', 'assets/images/founder.jpg');
$founderUpload = get_setting($pdo, 'founder_image_upload', '');
$founderX = get_setting($pdo, 'founder_pos_x', '50');
$founderY = get_setting($pdo, 'founder_pos_y', '50');
$founderSource = ($founderType === 'upload' && !empty($founderUpload)) ? $founderUpload : $founderUrl;
$founderStyle = "object-fit: cover; object-position: {$founderX}% {$founderY}%;";
?>

<main>
    <!-- HERO SECTION -->
    <section id="home" class="relative h-screen w-full overflow-hidden flex items-center justify-center">
        <!-- Video Background -->
        <div class="absolute inset-0 w-full h-full z-0">
            <?php if (strpos($videoSource, 'youtube.com') !== false || strpos($videoSource, 'youtu.be') !== false): 
                // Basic extract for youtube ID
                $videoId = '';
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoSource, $match)) {
                    $videoId = $match[1];
                }
                
                $ytParams = "?autoplay=1&mute=1&controls=0&loop=1&playlist={$videoId}&showinfo=0&rel=0";
                if ($vidStart > 0) $ytParams .= "&start={$vidStart}";
                if ($vidEnd > 0) $ytParams .= "&end={$vidEnd}";
            ?>
                <iframe class="w-full h-full pointer-events-none" style="<?= htmlspecialchars($objectStyle) ?> transform: scale(1.5);"
                    src="https://www.youtube.com/embed/<?= htmlspecialchars($videoId) . $ytParams ?>" 
                    frameborder="0" allowfullscreen></iframe>
            <?php else: 
                $srcUrl = htmlspecialchars($videoSource);
                if ($vidStart > 0 || $vidEnd > 0) {
                    $srcUrl .= "#t={$vidStart}";
                    if ($vidEnd > 0) $srcUrl .= ",{$vidEnd}";
                }
            ?>
                <video class="w-full h-full" style="<?= htmlspecialchars($objectStyle) ?>" autoplay muted loop playsinline poster="<?= htmlspecialchars($heroPoster) ?>">
                    <source src="<?= $srcUrl ?>">
                </video>
            <?php endif; ?>
            <div class="absolute inset-0 video-overlay"></div>
        </div>

        <div class="relative z-10 text-center px-6 max-w-4xl mx-auto flex flex-col items-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl text-white font-body font-light mb-10 tracking-wide reveal-up" style="opacity:0;">
                Healthy skin begins with expert care.
            </h1>
            <div class="flex flex-col sm:flex-row gap-6 justify-center reveal-up" style="opacity:0; transition-delay: 0.2s;">
                <a href="#" onclick="openBookingModal(); return false;" class="btn-luxury px-10 py-3 text-[15px] font-light">Book Now</a>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-10 text-bg animate-bounce flex flex-col items-center">
            <span class="text-xs uppercase tracking-widest mb-2 opacity-70">Discover</span>
            <i class="fas fa-chevron-down opacity-70"></i>
        </div>
    </section>

    

    
    
    <!-- START HERE: SKIN CONSULTATION -->
    <section id="start-here" class="py-32 px-6 bg-bg">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 space-y-6 reveal-up">
                    <span class="text-accent uppercase tracking-widest text-sm font-semibold mb-2 block">Start Here</span>
                    <h2 class="text-4xl md:text-5xl font-heading text-secondary"><?= htmlspecialchars(get_setting($pdo, 'consultation_title', 'New Client Consultation')) ?></h2>
                    <p class="text-gray-700 font-light leading-relaxed text-lg">
                        Every skin journey starts with a consultation.
                    </p>
                    <div class="bg-white/60 p-6 rounded-xl border border-gray-200 shadow-sm mt-4">
                        <h4 class="font-medium text-secondary mb-3">Your consultation includes:</h4>
                        <ul class="space-y-2 text-gray-700 font-light">
                            <?php 
                            $raw_bullets = get_setting($pdo, 'consultation_text', '');
                            if (empty($raw_bullets)) {
                                $raw_bullets = "Skin assessment\nDiscussion of concerns and goals\nLifestyle and skincare review\nTreatment recommendations\nPersonalised treatment plan\nHomecare recommendations";
                            }
                            $bullets = array_filter(array_map('trim', explode("\n", $raw_bullets)));
                            foreach ($bullets as $b): 
                                $b = ltrim($b, '* ');
                            ?>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-3"></i> <?= htmlspecialchars($b) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="pt-4 flex flex-col sm:flex-row items-center gap-6">
                        <a href="#" onclick="openBookingModal('Skin Consultation'); return false;" class="inline-block rounded-full border border-gray-200 px-10 py-4 bg-bg text-text hover:bg-gray-50 uppercase tracking-widest text-sm hover:bg-opacity-90 transition-all duration-300 shadow-lg">Book Consultation</a>
                        <div>
                            <p class="text-xl font-heading text-secondary">£20</p>
                            <p class="text-xs text-gray-500 uppercase tracking-widest">(Redeemable against treatment booked on the day)</p>
                        </div>
                    </div>
                </div>
                <div class="order-1 lg:order-2 reveal-up">
                    <div class="relative w-full aspect-square rounded-2xl overflow-hidden shadow-2xl">
                        <?php 
                        $cType = get_setting($pdo, 'consultation_image_type', 'url');
                        $cUrl = get_setting($pdo, 'consultation_image_url', 'https://images.unsplash.com/photo-1616394584738-fc6e612e71c9?auto=format&fit=crop&q=80&w=1000');
                        $cUpload = get_setting($pdo, 'consultation_image_upload', '');
                        $consultationImage = ($cType === 'upload' && !empty($cUpload)) ? $cUpload : $cUrl;
                        ?>
                        <img src="<?= htmlspecialchars($consultationImage) ?>" alt="Consultation" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- OUR SKIN JOURNEYS -->
    <section id="programmes" class="py-32 px-6 bg-bg text-text">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-20 reveal-up">
                <div class="max-w-2xl">
                    <span class="text-accent uppercase tracking-widest text-sm font-semibold mb-2 block">Curated Experiences</span>
                    <h2 class="text-4xl md:text-5xl font-heading mb-6">Our Packages</h2>
                    <p class="text-gray-400 font-light text-lg">We don't just perform treatments; we deliver results. Our skin journeys combine multiple modalities over a set period to fundamentally transform your skin.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-20">
                <?php foreach ($programmes as $p): ?>
                <div class="group reveal-up bg-black/5 rounded-2xl overflow-hidden border border-black/5 hover:border-white/30 transition-colors">
                    <div class="overflow-hidden relative h-64">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" class="w-full h-full object-cover grayscale opacity-80 transition-all duration-700 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c1a] to-transparent opacity-80"></div>
                        <h3 class="absolute bottom-6 left-8 text-3xl font-heading text-white"><?= htmlspecialchars($p['title']) ?></h3>
                    </div>
                    <div class="p-8">
                        <div class="text-gray-700 font-light leading-relaxed mb-8 space-y-4 text-sm prose prose-p:text-gray-700 prose-ul:text-gray-700">
                            <?php
                            $desc = htmlspecialchars($p['description']);
                            // Simple parser to make bullet points actual UL/LI so it looks clean
                            $desc = preg_replace('/\n\* (.*)/', '<li class="flex items-start"><i class="fas fa-caret-right text-accent mt-1 mr-2 text-[10px]"></i>$1</li>', $desc);
                            // Wrap consecutive li in ul
                            $desc = preg_replace('/(<li.*?>.*?<\/li>(\s*<li.*?>.*?<\/li>)*)/s', '<ul class="space-y-1 mb-4 mt-2">$1</ul>', $desc);
                            echo nl2br($desc);
                            ?>
                        </div>
                        <a href="#" onclick="openBookingModal('<?= htmlspecialchars(addslashes($p['title'])) ?>', '<?= htmlspecialchars(addslashes($p['faces_link'] ?? '')) ?>'); return false;" class="block text-center text-xs uppercase tracking-widest text-bg bg-accent hover:bg-white transition-colors py-4 rounded font-medium">
                            Book This Journey 
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    
    <!-- ADVANCED SKIN THERAPIES -->
    <section id="treatments" class="py-32 px-6 bg-bg">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20 reveal-up">
                <span class="text-accent uppercase tracking-widest text-sm font-semibold mb-2 block">Clinical Excellence</span>
                <h2 class="text-4xl md:text-5xl font-heading text-secondary">Advanced Skin Therapies</h2>
                <p class="mt-6 text-gray-500 max-w-2xl mx-auto font-light leading-relaxed">We focus on long-term cellular health rather than quick fixes. Explore our curated, evidence-based therapies designed to restore, rebuild, and protect your skin.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 lg:gap-10">
                <?php foreach ($treatments as $t): ?>
                <div class="group reveal-up flex flex-col h-full bg-white rounded-2xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-500">
                    <div class="p-8 flex flex-col flex-grow">
                        <div class="mb-4 border-b border-gray-100 pb-4 flex flex-col items-start gap-1">
                            <div class="flex justify-between items-start w-full">
                                <h3 class="text-2xl font-heading text-secondary pr-4"><?= htmlspecialchars($t['title']) ?></h3>
                                <span class="block text-gray-500 text-sm flex-shrink-0 whitespace-nowrap pt-1"><?= htmlspecialchars($t['duration']) ?></span>
                            </div>
                            <?php if (!empty($t['course_recommendation'])): ?>
                            <span class="block text-accent font-medium text-sm mt-1 leading-tight"><?= htmlspecialchars($t['course_recommendation']) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-500 font-light text-[15px] leading-relaxed mb-6 flex-grow"><?= htmlspecialchars($t['short_desc']) ?></p>
                        
                        <div class="space-y-4 border-t border-gray-100 pt-6 mt-auto">
                            <!-- Detail Accordion / Modal trigger -->
                            <button onclick="toggleTreatmentDetails(<?= $t['id'] ?>)" class="flex justify-between items-center w-full text-left text-secondary font-medium group/btn">
                                <span class="text-sm uppercase tracking-widest text-accent">Discover More</span>
                                <i class="fas fa-plus text-xs text-accent transition-transform duration-300" id="icon-<?= $t['id'] ?>"></i>
                            </button>
                            
                            <!-- Hidden Details -->
                            <div id="details-<?= $t['id'] ?>" class="hidden text-sm font-light text-gray-600 space-y-4 mt-4 pb-2 border-t border-gray-50 pt-4">
                                <p><strong class="text-secondary font-medium block mb-1">What It Is:</strong> <?= nl2br(htmlspecialchars($t['what_it_is'])) ?></p>
                                <p><strong class="text-secondary font-medium block mb-1">Suitable For:</strong> <?= htmlspecialchars($t['suitable_for']) ?></p>
                                <p><strong class="text-secondary font-medium block mb-1">Key Benefits:</strong> <?= htmlspecialchars($t['key_benefits']) ?></p>
                            </div>
                            
                            <a href="#" onclick="openBookingModal('<?= htmlspecialchars(addslashes($t['title'])) ?>', '<?= htmlspecialchars(addslashes($t['faces_link'] ?? '')) ?>'); return false;" class="block text-center w-full py-3 bg-bg text-text uppercase tracking-widest text-xs hover:bg-opacity-90 transition-all mt-4 rounded">Book Therapy</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <script>
        function toggleTreatmentDetails(id) {
            const details = document.getElementById('details-' + id);
            const icon = document.getElementById('icon-' + id);
            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.classList.add('rotate-45');
            } else {
                details.classList.add('hidden');
                icon.classList.remove('rotate-45');
            }
        }
    </script>

    
    <!-- ABOUT VALERIE -->
    <section id="founder" class="py-32 px-6 bg-bg">
        <div class="max-w-7xl mx-auto">
            <div class="mb-24 text-center max-w-4xl mx-auto reveal-up">
                <h2 class="text-3xl md:text-4xl font-heading text-secondary mb-8 leading-tight">
                    At VNT Aura Skin & Wellness, we believe healthy skin begins with understanding.
                </h2>
                <div class="text-gray-700 font-light text-lg space-y-6">
                    <p>Our approach combines professional skin treatments, personalised treatment planning and tailored homecare recommendations to help clients achieve healthier, more confident skin.</p>
                    <p>We understand that every skin journey is unique. That’s why every treatment begins with a thorough consultation, allowing us to identify your concerns, understand your goals and create a plan tailored specifically to your skin’s needs.</p>
                    <p>Whether your focus is hydration, skin clarity, pigmentation management, texture improvement or overall skin rejuvenation, our aim is to provide a supportive and results-focused experience that helps you feel comfortable and confident in your skin.</p>
                    <p class="italic font-medium text-secondary mt-8">"At VNT Aura Skin & Wellness, skincare is more than a treatment—it’s an investment in your confidence, wellbeing and long-term skin health."</p>
                    <p class="text-sm uppercase tracking-widest text-accent">— Valerie, Founder</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 reveal-up">
                    <div class="relative w-full aspect-[4/5] rounded-tl-full rounded-tr-full overflow-hidden shadow-2xl">
                        <img src="<?= htmlspecialchars($founderSource) ?>" alt="Valerie N Temfack" class="w-full h-full" style="<?= $founderStyle ?>">
                    </div>
                </div>
                <div class="order-1 lg:order-2 space-y-6 reveal-up">
                    <span class="text-accent uppercase tracking-widest text-sm font-semibold mb-2 block">Meet Valerie</span>
                    <h2 class="text-4xl md:text-5xl font-heading text-secondary">Founder of VNT Aura Skin & Wellness</h2>
                    <p class="text-gray-700 font-light leading-relaxed text-lg">
                        Valerie believes that selfcare is not a luxury it is an important part of overall wellbeing. In a world where many women spend their time caring for everyone else, she is passionate about encouraging women to be more intentional about caring for themselves.
                    </p>
                    <p class="text-gray-700 font-light leading-relaxed text-lg">
                        Through personalised skin treatments and professional guidance, her aim is to help clients create moments of selfcare that support both confidence and wellbeing. She believes that healthy skin is about more than appearance; it is about feeling comfortable, confident and empowered in your own skin.
                    </p>
                    <p class="text-gray-700 font-light leading-relaxed text-lg">
                        With training in advanced facial treatments, Valerie takes a tailored approach to skincare, recognising that every client’s skin concerns, goals and lifestyle are unique.
                    </p>
                    <p class="text-gray-700 font-light leading-relaxed text-lg">
                        At VNT Aura Skin & Wellness, the focus is on personalised care, professional treatment planning and supporting long term skin health in a welcoming and nurturing environment.
                    </p>
                </div>
            </div>
        </div>
    </section>


    
    <!-- GALLERY -->
    <section class="py-32 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 reveal-up">
                <span class="text-accent uppercase tracking-widest text-sm font-semibold mb-2 block">Real Results</span>
                <h2 class="text-4xl md:text-5xl font-heading text-secondary">Before & After</h2>
                <p class="text-gray-500 font-light mt-4">Transformations achieved through our dedicated skin programmes.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Gallery Item 1 -->
                <div class="reveal-up rounded-xl overflow-hidden shadow-lg border border-gray-100 group">
                    <div class="relative aspect-video">
                        <img src="https://images.unsplash.com/photo-1596755389378-c11d04423447?auto=format&fit=crop&q=80&w=800" alt="Before and After" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white uppercase tracking-widest text-sm border border-white px-6 py-2">Acne Clearance</span>
                        </div>
                    </div>
                </div>
                <!-- Gallery Item 2 -->
                <div class="reveal-up rounded-xl overflow-hidden shadow-lg border border-gray-100 group delay-100">
                    <div class="relative aspect-video">
                        <img src="https://images.unsplash.com/photo-1512496015851-a1dcbfd83049?auto=format&fit=crop&q=80&w=800" alt="Before and After" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white uppercase tracking-widest text-sm border border-white px-6 py-2">Pigmentation Lift</span>
                        </div>
                    </div>
                </div>
                <!-- Gallery Item 3 -->
                <div class="reveal-up rounded-xl overflow-hidden shadow-lg border border-gray-100 group delay-200">
                    <div class="relative aspect-video">
                        <img src="https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&q=80&w=800" alt="Before and After" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white uppercase tracking-widest text-sm border border-white px-6 py-2">Skin Renewal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="py-32 px-6 bg-bg text-text relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="max-w-6xl mx-auto relative z-10">
            <div class="text-center mb-16 reveal-up">
                <i class="fas fa-quote-right text-4xl text-accent mb-6"></i>
                <h2 class="text-4xl md:text-5xl font-heading">Client Love</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-black/5 p-8 border border-black/5 rounded-xl reveal-up backdrop-blur-sm">
                    <div class="flex text-accent mb-4 text-sm">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="font-light italic text-gray-700 mb-6 leading-relaxed">
                        "Valerie completely transformed my skin. I struggled with adult acne for years, and her bespoke programme cleared it up in months. Highly recommend!"
                    </p>
                    <p class="font-medium tracking-widest text-sm uppercase text-text">- Sarah T.</p>
                </div>
                <!-- Testimonial 2 -->
                <div class="bg-black/5 p-8 border border-black/5 rounded-xl reveal-up backdrop-blur-sm delay-100">
                    <div class="flex text-accent mb-4 text-sm">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="font-light italic text-gray-700 mb-6 leading-relaxed">
                        "The most luxurious and informative facial I've ever had. My skin has never looked so glowing and plump. The clinic is absolutely beautiful."
                    </p>
                    <p class="font-medium tracking-widest text-sm uppercase text-text">- Emily R.</p>
                </div>
                <!-- Testimonial 3 -->
                <div class="bg-black/5 p-8 border border-black/5 rounded-xl reveal-up backdrop-blur-sm delay-200">
                    <div class="flex text-accent mb-4 text-sm">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="font-light italic text-gray-700 mb-6 leading-relaxed">
                        "Her knowledge of pigmentation is unmatched. I felt so supported throughout my 12-week programme and the results speak for themselves."
                    </p>
                    <p class="font-medium tracking-widest text-sm uppercase text-text">- Jessica M.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- FAQ -->
    <section id="faq" class="py-32 px-6 bg-bg">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-16 reveal-up">
                <h2 class="text-4xl md:text-5xl font-heading text-secondary">Frequently Asked Questions</h2>
            </div>
            
            <div class="space-y-4 reveal-up">
                <!-- FAQ Item 1 -->
                <div class="border-b border-gray-200 pb-4 faq-item group cursor-pointer">
                    <div class="flex justify-between items-center py-4">
                        <h4 class="text-xl font-heading text-secondary group-hover:text-accent transition-colors">How do I know which treatment to book?</h4>
                        <i class="fas fa-plus text-accent transition-transform duration-300 transform faq-icon"></i>
                    </div>
                    <div class="faq-content hidden text-gray-600 font-light leading-relaxed pb-4">
                        If you are a new client, we always recommend booking a Skin Consultation first. This allows us to thoroughly assess your skin and recommend the most suitable treatment pathway.
                    </div>
                </div>
                <!-- FAQ Item 2 -->
                <div class="border-b border-gray-200 pb-4 faq-item group cursor-pointer">
                    <div class="flex justify-between items-center py-4">
                        <h4 class="text-xl font-heading text-secondary group-hover:text-accent transition-colors">Do you offer payment plans?</h4>
                        <i class="fas fa-plus text-accent transition-transform duration-300 transform faq-icon"></i>
                    </div>
                    <div class="faq-content hidden text-gray-600 font-light leading-relaxed pb-4">
                        Yes, we offer flexible payment options for our comprehensive skin programmes to ensure your skin health journey is accessible.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT CTA -->
    <section id="contact" class="py-40 px-6 bg-[url('https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&q=80&w=2000')] bg-cover bg-center relative bg-fixed">
        <div class="absolute inset-0 bg-bg/90"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center reveal-up">
            <h2 class="text-5xl md:text-7xl font-heading text-secondary mb-8">Ready to Begin Your Skin Journey?</h2>
            <p class="text-xl text-gray-700 font-light mb-12 max-w-2xl mx-auto">
                Book your consultation today and take the first step towards healthier, more radiant skin.
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <a href="#" onclick="openBookingModal(); return false;" class="inline-block rounded-full border border-secondary px-10 py-4 bg-secondary text-white hover:bg-opacity-90 uppercase tracking-widest text-sm hover:bg-opacity-90 transition-all duration-300 shadow-lg">Book Consultation</a>
                <a href="mailto:vntauraskinandwellness@gmail.com" class="inline-flex items-center justify-center rounded-full px-10 py-5 border border-secondary text-secondary hover:bg-secondary hover:text-white uppercase tracking-widest text-sm font-semibold hover:bg-secondary hover:text-white transition-colors">Contact Us</a>
            </div>
        </div>
    </section>

</main>


<?php require_once __DIR__ . '/includes/booking_modal.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
