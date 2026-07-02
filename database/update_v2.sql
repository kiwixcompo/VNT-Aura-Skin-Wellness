USE vnt_aura_db;

-- Bookings Table
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `client_name` VARCHAR(100) NOT NULL,
  `client_email` VARCHAR(100) NOT NULL,
  `client_phone` VARCHAR(20) NOT NULL,
  `service` VARCHAR(100) NOT NULL,
  `preferred_date` DATE NOT NULL,
  `preferred_time` VARCHAR(50) NOT NULL,
  `notes` TEXT,
  `status` ENUM('Pending', 'Approved', 'Cancelled') DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Treatments Table
DROP TABLE IF EXISTS `treatments`;
CREATE TABLE `treatments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `short_desc` TEXT NOT NULL,
  `science_desc` TEXT NOT NULL,
  `ideal_for` VARCHAR(255) NOT NULL,
  `downtime` VARCHAR(100) NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `display_order` INT DEFAULT 0
);

-- Seed Treatments
INSERT INTO `treatments` (`id`, `title`, `short_desc`, `science_desc`, `ideal_for`, `downtime`, `image_url`, `display_order`) VALUES
(1, 'Collagen Induction Therapy (Microneedling)', 'Stimulate your skin\'s natural healing cascade to smooth texture, diminish scarring, and restore a youthful, plump matrix.', 'Microneedling creates controlled micro-injuries in the dermis, triggering the release of growth factors and stimulating the production of new collagen and elastin fibres. This structural remodeling improves the tensile strength and elasticity of the skin.', 'Acne scarring, fine lines, enlarged pores, and uneven texture.', '1-3 days of mild redness', 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&q=80&w=800', 1),
(2, 'Advanced Clinical Peels', 'Tailored chemical exfoliation to accelerate cellular turnover, revealing a brighter, deeply clarified, and perfectly even complexion.', 'We utilize specific molecular weights of AHAs, BHAs, and sophisticated enzyme blends to dissolve the desmosome bonds holding dead stratum corneum cells together. This forces an accelerated cellular turnover rate, bringing fresh, healthy cells to the surface while lifting pigmentation.', 'Hyperpigmentation, active congestion, dullness, and photo-aging.', 'Varies from 0 to 7 days depending on depth', 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=800', 2),
(3, 'Dermal LED Phototherapy', 'Harness the power of clinically proven light wavelengths to reduce inflammation, destroy acne bacteria, and supercharge cellular energy.', 'LED therapy utilizes specific, proven wavelengths of light (like 415nm Blue and 830nm Near-Infrared). These photons are absorbed by the mitochondria of the skin cells, increasing ATP production and accelerating tissue repair and cellular metabolism without any thermal damage.', 'Active acne, rosacea, post-treatment healing, and overall skin vitality.', 'Zero downtime', 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=800', 3);
