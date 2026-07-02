USE vnt_aura_db;

-- Update Settings for Founder Image
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('founder_image_url', 'assets/images/founder.jpg'),
('founder_image_upload', ''),
('founder_image_type', 'url'),
('founder_pos_x', '50'),
('founder_pos_y', '50');

-- Drop old treatments table and recreate
DROP TABLE IF EXISTS `treatments`;
CREATE TABLE `treatments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `short_desc` TEXT NOT NULL,
  `what_it_is` TEXT NOT NULL,
  `suitable_for` TEXT NOT NULL,
  `key_benefits` TEXT NOT NULL,
  `duration` VARCHAR(100) NOT NULL,
  `course_recommendation` TEXT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `display_order` INT DEFAULT 0
);

-- Seed Treatments
INSERT INTO `treatments` (`id`, `title`, `short_desc`, `what_it_is`, `suitable_for`, `key_benefits`, `duration`, `course_recommendation`, `image_url`, `display_order`) VALUES
(1, 'Skin Consultation', 'Your personalised skin assessment and treatment planning session.', 'A comprehensive deep-dive into your skin health, medical history, lifestyle, and current regimen. We utilise advanced diagnostic techniques to understand the root causes of your skin concerns.', 'First-time clients, anyone struggling with persistent skin conditions, or those looking to overhaul their skincare routine.', 'Identifies underlying skin conditions; provides a bespoke homecare and treatment plan; prevents wasted money on incorrect products.', '45-60 Minutes', 'A one-off essential first step before embarking on any treatment programme.', 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=800', 1),
(2, 'Bespoke Facial', 'A custom-tailored facial experience designed to address your specific skin needs on the day.', 'A highly adaptable clinical facial that combines various modalities, professional-grade actives, and targeted massage techniques depending on what your skin requires at the exact moment of your appointment.', 'All skin types, especially those needing a deep clean, intense hydration, or a pre-event glow.', 'Deeply cleanses and exfoliates; restores barrier function; floods the skin with hydration; induces deep relaxation.', '60 Minutes', 'Recommended every 4-6 weeks for maintenance of healthy skin.', 'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?auto=format&fit=crop&q=80&w=800', 2),
(3, 'Dermaplaning Facial', 'Physical exfoliation to remove dead skin cells and vellus hair for a flawlessly smooth canvas.', 'A highly effective, non-invasive physical exfoliation procedure. Using a sterile surgical scalpel, we gently glide across the stratum corneum to manually remove keratinized cells and soft facial hair ("peach fuzz").', 'Dull, dry, or rough skin. Excellent for those wanting flawless makeup application or enhanced product absorption.', 'Instant brightening effect; creates a perfectly smooth texture; allows active serums to penetrate significantly deeper into the epidermis.', '45 Minutes', 'Recommended every 4 weeks. Not suitable for active acne.', 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&q=80&w=800', 3),
(4, 'Microdermabrasion', 'Mechanical exfoliation to refine texture, clear congestion, and stimulate cellular turnover.', 'A minimally invasive procedure that uses a diamond-tipped wand to gently sand away the thick outer layer of the skin. This mechanical action stimulates blood flow and encourages lymphatic drainage.', 'Congested skin, mild scarring, uneven texture, and enlarged pores.', 'Immediately softens texture; helps dislodge comedones (blackheads); promotes a brighter, more refined complexion.', '45 Minutes', 'A course of 6 treatments spaced 2-4 weeks apart is recommended for optimal textural improvements.', 'https://images.unsplash.com/photo-1552693673-1bf958298935?auto=format&fit=crop&q=80&w=800', 4),
(5, 'Glow Peel', 'A gentle yet highly effective chemical exfoliation to revive lackluster skin without the downtime.', 'A carefully formulated blend of superficial Alpha Hydroxy Acids (AHAs) and enzymes designed to dissolve the bonds holding dead skin cells together, forcing a rapid, controlled exfoliation without aggressive peeling.', 'Dullness, superficial hyperpigmentation, mild breakouts, and those needing a fast radiance boost.', 'Instantly enhances luminosity; refines pore appearance; evens out minor tonal irregularities with zero to minimal downtime.', '45 Minutes', 'Can be done as a standalone pre-event treatment or a course of 3-6 spaced 2-3 weeks apart.', 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=800', 5),
(6, 'Microneedling', 'Collagen induction therapy to restructure the skin matrix, diminish scarring, and smooth texture.', 'Also known as Collagen Induction Therapy. We use a medical-grade device to create thousands of precise micro-injuries in the dermis. This triggers the body\'s natural wound-healing cascade, stimulating the production of new collagen and elastin.', 'Acne scarring, fine lines, laxity, enlarged pores, and textural irregularities.', 'Fundamentally restructures the skin; thickens the dermis; significantly reduces the appearance of indented scars and deep wrinkles.', '60 Minutes', 'A strict course of 3-6 treatments spaced 4-6 weeks apart is required for structural changes.', 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&q=80&w=800', 6),
(7, 'LED Light Therapy', 'Clinically proven light wavelengths to reduce inflammation, destroy acne bacteria, and energise cells.', 'A non-invasive treatment utilizing specific therapeutic wavelengths of light energy (Blue, Red, and Near-Infrared). These photons penetrate the skin to target specific chromophores, accelerating cellular metabolism and tissue repair.', 'Active acne, rosacea, inflammatory conditions, and post-treatment wound healing.', 'Kills P. acnes bacteria; dramatically reduces erythema (redness) and inflammation; speeds up healing post-invasive treatments.', '30 Minutes', 'Highly recommended as an add-on to other treatments, or an intensive standalone course of 8-12 sessions (2x weekly).', 'https://images.unsplash.com/photo-1598300188481-8091176b6d85?auto=format&fit=crop&q=80&w=800', 7);

-- Programmes Table
DROP TABLE IF EXISTS `programmes`;
CREATE TABLE `programmes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `display_order` INT DEFAULT 0
);

-- Seed Programmes
INSERT INTO `programmes` (`id`, `title`, `description`, `image_url`, `display_order`) VALUES
(1, 'Bright & Even Skin Programme', 'A targeted 12-week protocol designed to lift stubborn hyperpigmentation, melasma, and sun damage. Combines series of bespoke chemical peels with melanin-inhibiting homecare to reveal a luminous, uniform complexion.', 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&q=80&w=800', 1),
(2, 'Clear & Balanced Skin Programme', 'An intensive, holistic journey for those struggling with active acne, hormonal breakouts, and persistent congestion. We utilize targeted extractions, LED therapy, and clinical peels alongside a complete barrier-repairing home regimen to secure long-term clarity.', 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=800', 2),
(3, 'Skin Renewal Programme', 'Focused on deep textural correction. Ideal for clients wishing to resolve acne scarring, enlarged pores, and rough texture. This programme leans heavily on staggered Microneedling sessions to force structural remodeling of the dermis.', 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&q=80&w=800', 3),
(4, 'Healthy Ageing Programme', 'A preventative and restorative journey designed to plump, firm, and stimulate collagen production. Combines advanced modalities to target fine lines, laxity, and dullness, ensuring your skin ages in the healthiest, most vibrant way possible.', 'https://images.unsplash.com/photo-1552693673-1bf958298935?auto=format&fit=crop&q=80&w=800', 4);
