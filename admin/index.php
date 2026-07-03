<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$msg = $_GET['msg'] ?? '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'hero_video_type' => $_POST['hero_video_type'] ?? 'url',
        'hero_video_url' => $_POST['hero_video_url'] ?? '',
        'hero_video_upload' => $_POST['hero_video_upload'] ?? '',
        'hero_video_start' => $_POST['hero_video_start'] ?? '0',
        'hero_video_end' => $_POST['hero_video_end'] ?? '',
        'hero_video_pos_x' => $_POST['hero_video_pos_x'] ?? '50',
        'hero_video_pos_y' => $_POST['hero_video_pos_y'] ?? '50',
        'founder_image_type' => $_POST['founder_image_type'] ?? 'url',
        'founder_image_url' => $_POST['founder_image_url'] ?? '',
        'founder_image_upload' => $_POST['founder_image_upload'] ?? '',
        'founder_pos_x' => $_POST['founder_pos_x'] ?? '50',
        'founder_pos_y' => $_POST['founder_pos_y'] ?? '50',
        'seo_title' => $_POST['seo_title'] ?? '',
        'seo_description' => $_POST['seo_description'] ?? '',
        
        'consultation_title' => $_POST['consultation_title'] ?? '',
        'consultation_subtitle' => $_POST['consultation_subtitle'] ?? '',
        'consultation_text' => $_POST['consultation_text'] ?? '',
        'consultation_price' => $_POST['consultation_price'] ?? '',
        'consultation_note' => $_POST['consultation_note'] ?? '',
        'consultation_bullets' => $_POST['consultation_bullets'] ?? '',
        'journeys_title' => $_POST['journeys_title'] ?? '',
        'journeys_subtitle' => $_POST['journeys_subtitle'] ?? '',
        'journeys_text' => $_POST['journeys_text'] ?? '',
        'therapies_title' => $_POST['therapies_title'] ?? '',
        'therapies_subtitle' => $_POST['therapies_subtitle'] ?? '',
        'therapies_text' => $_POST['therapies_text'] ?? '',
        'about_title' => $_POST['about_title'] ?? '',
        'about_text1' => $_POST['about_text1'] ?? '',
        'about_text2' => $_POST['about_text2'] ?? '',
        'about_text3' => $_POST['about_text3'] ?? '',
        'about_quote' => $_POST['about_quote'] ?? '',
        'about_author' => $_POST['about_author'] ?? '',
        'bio_title' => $_POST['bio_title'] ?? '',
        'bio_subtitle' => $_POST['bio_subtitle'] ?? '',
        'bio_text' => $_POST['bio_text'] ?? '',
        'results_title' => $_POST['results_title'] ?? '',
        'faqs_title' => $_POST['faqs_title'] ?? '',
        'contact_title' => $_POST['contact_title'] ?? '',
        'contact_address' => $_POST['contact_address'] ?? '',
        'contact_phone' => $_POST['contact_phone'] ?? '',
        'contact_email_display' => $_POST['contact_email_display'] ?? '',
        'contact_hours' => $_POST['contact_hours'] ?? '',
        'admin_email' => $_POST['admin_email'] ?? '',
        'notify_admin' => isset($_POST['notify_admin']) ? '1' : '0',
        'notify_client' => isset($_POST['notify_client']) ? '1' : '0'    ];
    
    // Check for file upload
    if (isset($_FILES['video_upload_file']) && $_FILES['video_upload_file']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['video_upload_file']['tmp_name'];
        $name = basename($_FILES['video_upload_file']['name']);
        
        // Simple security checks would go here for mime types, extensions, size limit (500MB)
        // For MVP, move uploaded file to assets/uploads
        $uploadDir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $name);
        $destPath = $uploadDir . $safeName;
        
        if (move_uploaded_file($tmpName, $destPath)) {
            $updates['hero_video_upload'] = 'assets/uploads/' . $safeName;
        }
    } elseif (isset($_FILES['video_upload_file']) && $_FILES['video_upload_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $msg = 'upload_error_' . $_FILES['video_upload_file']['error'];
    }
    
    // Check for founder image upload
    if (isset($_FILES['founder_upload_file']) && $_FILES['founder_upload_file']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['founder_upload_file']['tmp_name'];
        $name = basename($_FILES['founder_upload_file']['name']);
        
        $uploadDir = __DIR__ . '/../assets/images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $safeName = 'founder_uploaded_' . time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $name);
        $destPath = $uploadDir . $safeName;
        
        if (move_uploaded_file($tmpName, $destPath)) {
            $updates['founder_image_upload'] = 'assets/images/' . $safeName;
        }
    } elseif (isset($_FILES['founder_upload_file']) && $_FILES['founder_upload_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $msg = 'upload_error_' . $_FILES['founder_upload_file']['error'];
    }
    
    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
    
    foreach ($updates as $k => $v) {
        $stmt->execute([$k, $v, $v]);
    }
    
    header('Location: index.php?msg=saved');
    exit;
}

// Fetch settings
$stmt = $pdo->query('SELECT setting_key, setting_value FROM settings');
$settingsRaw = $stmt->fetchAll();
$settings = [];
foreach ($settingsRaw as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - VNT Aura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    
    <!-- Sidebar -->
    <div class="fixed w-64 h-full bg-white border-r border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-xl font-bold tracking-wider uppercase text-gray-900">VNT Admin</h1>
        </div>
        <nav class="p-4 space-y-2">
            <a href="index.php" class="block py-2 px-4 bg-gray-100 rounded text-gray-900 font-medium"><i class="fas fa-cog w-6"></i> Settings</a>
            <a href="bookings.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-calendar-alt w-6"></i> Bookings</a>
            <a href="treatments.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-spa w-6"></i> Advanced Therapies</a>
            <a href="programmes.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-layer-group w-6"></i> Skin Journeys</a>
            
            <p class="px-4 pt-4 text-xs font-bold text-gray-400 uppercase">CMS Content</p>
            <a href="faqs.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-question-circle w-6"></i> FAQs</a>
            <a href="testimonials.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-comment-dots w-6"></i> Testimonials</a>
            <a href="gallery.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-images w-6"></i> Gallery</a>
            <a href="logout.php" class="block py-2 px-4 text-red-600 hover:bg-red-50 rounded transition-colors mt-8"><i class="fas fa-sign-out-alt w-6"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <h2 class="text-3xl font-semibold mb-8">Dashboard Settings</h2>
        
        <?php if ($msg === 'saved'): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-6 font-medium">Settings saved successfully.</div>
        <?php elseif ($msg === 'password_updated'): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-6 font-medium">Password updated successfully.</div>
        <?php elseif (strpos($msg, 'upload_error_') === 0): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded mb-6 font-medium">
                Upload failed! The file may be too large for your server configuration. (Error Code: <?= htmlspecialchars(str_replace('upload_error_', '', $msg)) ?>)
            </div>
        <?php endif; ?>

        <form method="post" action="index.php" enctype="multipart/form-data" class="space-y-8 max-w-4xl">
            
            <!-- Hero Section Settings -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-xl font-medium mb-4 pb-2 border-b">Hero Video Settings</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Video Source Type</label>
                    <select name="hero_video_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="videoTypeSelect">
                        <option value="url" <?= ($settings['hero_video_type']??'url') === 'url' ? 'selected' : '' ?>>URL (YouTube, MP4, etc.)</option>
                        <option value="upload" <?= ($settings['hero_video_type']??'url') === 'upload' ? 'selected' : '' ?>>Direct Upload (Max 500MB)</option>
                    </select>
                </div>
                
                <div class="mb-4" id="videoUrlGroup">
                    <label class="block text-gray-700 font-medium mb-2">Video URL</label>
                    <input type="text" name="hero_video_url" value="<?= htmlspecialchars($settings['hero_video_url'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://youtube.com/watch?v=... or direct .mp4 link">
                </div>
                
                <div class="mb-4 hidden" id="videoUploadGroup">
                    <label class="block text-gray-700 font-medium mb-2">Upload Video</label>
                    <?php if (!empty($settings['hero_video_upload'])): ?>
                        <div class="mb-2 text-sm text-gray-600">Current file: <?= htmlspecialchars($settings['hero_video_upload']) ?></div>
                    <?php endif; ?>
                    <input type="hidden" name="hero_video_upload" value="<?= htmlspecialchars($settings['hero_video_upload'] ?? '') ?>">
                    <input type="file" name="video_upload_file" accept="video/mp4,video/webm,video/quicktime" class="w-full px-4 py-2 border rounded-lg">
                </div>

                <!-- Live Preview & Cropping UI -->
                <div class="mt-8 border-t pt-6">
                    <h4 class="text-lg font-medium mb-4">Trimming & Cropping Preview</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Start Time (seconds)</label>
                            <input type="number" id="previewStart" name="hero_video_start" value="<?= htmlspecialchars($settings['hero_video_start'] ?? '0') ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" min="0">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">End Time (seconds)</label>
                            <input type="number" id="previewEnd" name="hero_video_end" value="<?= htmlspecialchars($settings['hero_video_end'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" placeholder="e.g. 15 (leave blank to play to end)">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">X Position (Horizontal %)</label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="previewPosX" name="hero_video_pos_x" value="<?= htmlspecialchars($settings['hero_video_pos_x'] ?? '50') ?>" class="w-full" min="0" max="100">
                                <span id="posXVal" class="text-sm font-medium w-12"><?= htmlspecialchars($settings['hero_video_pos_x'] ?? '50') ?>%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Y Position (Vertical %)</label>
                            <div class="flex items-center gap-4">
                                <input type="range" id="previewPosY" name="hero_video_pos_y" value="<?= htmlspecialchars($settings['hero_video_pos_y'] ?? '50') ?>" class="w-full" min="0" max="100">
                                <span id="posYVal" class="text-sm font-medium w-12"><?= htmlspecialchars($settings['hero_video_pos_y'] ?? '50') ?>%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview Box -->
                    <div class="relative w-full aspect-video bg-black rounded-lg overflow-hidden border border-gray-300">
                        <!-- We use a standard video tag for preview, even if the source is URL or Upload. 
                             If it's a youtube URL, preview might fail unless we embed an iframe, but for simplicity of UI we'll show an iframe for YT, video for direct. -->
                        <div id="previewContainer" class="w-full h-full relative">
                            <!-- Injected by JS -->
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">Note: Preview container is 16:9, but on the live site the video will be fullscreen (100vh).</p>
                </div>
            </div>

            <!-- Founder Section Settings -->
            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Meet Valerie (Founder Image)</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Image Source Type</label>
                    <select name="founder_image_type" id="founder_image_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="toggleFounderUpload()">
                        <option value="url" <?= ($settings['founder_image_type'] ?? '') === 'url' ? 'selected' : '' ?>>Direct URL</option>
                        <option value="upload" <?= ($settings['founder_image_type'] ?? '') === 'upload' ? 'selected' : '' ?>>Direct Upload</option>
                    </select>
                </div>
                
                <div id="founder_url_group" class="mb-4 <?= ($settings['founder_image_type'] ?? '') === 'upload' ? 'hidden' : '' ?>">
                    <label class="block text-gray-700 font-medium mb-2">Image URL</label>
                    <input type="text" name="founder_image_url" value="<?= htmlspecialchars($settings['founder_image_url'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="assets/images/founder.jpg">
                </div>
                
                <div id="founder_upload_group" class="mb-4 <?= ($settings['founder_image_type'] ?? '') === 'url' ? 'hidden' : '' ?>">
                    <label class="block text-gray-700 font-medium mb-2">Upload Image</label>
                    <?php if(!empty($settings['founder_image_upload'])): ?>
                        <div class="text-sm text-green-600 mb-2">Current file: <?= htmlspecialchars(basename($settings['founder_image_upload'])) ?></div>
                    <?php endif; ?>
                    <input type="file" name="founder_upload_file" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="hidden" name="founder_image_upload" value="<?= htmlspecialchars($settings['founder_image_upload'] ?? '') ?>">
                </div>

                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">Image Focal Point (Cropping Adjustments)</h4>
                    <p class="text-sm text-gray-500 mb-4">Adjust these sliders to ensure the founder's face is perfectly framed inside the layout.</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">X-Position (Horizontal %)</label>
                            <input type="range" name="founder_pos_x" id="fXSlider" min="0" max="100" value="<?= htmlspecialchars($settings['founder_pos_x'] ?? '50') ?>" class="w-full">
                            <span id="fXVal" class="text-xs text-gray-500"><?= htmlspecialchars($settings['founder_pos_x'] ?? '50') ?>%</span>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Y-Position (Vertical %)</label>
                            <input type="range" name="founder_pos_y" id="fYSlider" min="0" max="100" value="<?= htmlspecialchars($settings['founder_pos_y'] ?? '50') ?>" class="w-full">
                            <span id="fYVal" class="text-xs text-gray-500"><?= htmlspecialchars($settings['founder_pos_y'] ?? '50') ?>%</span>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- CMS Content Sections -->
            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Consultation Section</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-gray-700 font-medium mb-1">Title</label><input type="text" name="consultation_title" value="<?= htmlspecialchars($settings['consultation_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Subtitle</label><input type="text" name="consultation_subtitle" value="<?= htmlspecialchars($settings['consultation_subtitle'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div class="md:col-span-2"><label class="block text-gray-700 font-medium mb-1">Text</label><textarea name="consultation_text" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['consultation_text'] ?? '') ?></textarea></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Price</label><input type="text" name="consultation_price" value="<?= htmlspecialchars($settings['consultation_price'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Note</label><input type="text" name="consultation_note" value="<?= htmlspecialchars($settings['consultation_note'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div class="md:col-span-2"><label class="block text-gray-700 font-medium mb-1">Bullets (one per line)</label><textarea name="consultation_bullets" rows="4" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['consultation_bullets'] ?? '') ?></textarea></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Skin Journeys & Therapies Titles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-gray-700 font-medium mb-1">Journeys Title</label><input type="text" name="journeys_title" value="<?= htmlspecialchars($settings['journeys_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Journeys Subtitle</label><input type="text" name="journeys_subtitle" value="<?= htmlspecialchars($settings['journeys_subtitle'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div class="md:col-span-2"><label class="block text-gray-700 font-medium mb-1">Journeys Text</label><textarea name="journeys_text" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['journeys_text'] ?? '') ?></textarea></div>
                    
                    <div><label class="block text-gray-700 font-medium mb-1">Therapies Title</label><input type="text" name="therapies_title" value="<?= htmlspecialchars($settings['therapies_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Therapies Subtitle</label><input type="text" name="therapies_subtitle" value="<?= htmlspecialchars($settings['therapies_subtitle'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div class="md:col-span-2"><label class="block text-gray-700 font-medium mb-1">Therapies Text</label><textarea name="therapies_text" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['therapies_text'] ?? '') ?></textarea></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">About Section</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div><label class="block text-gray-700 font-medium mb-1">Title</label><input type="text" name="about_title" value="<?= htmlspecialchars($settings['about_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Text Paragraph 1</label><textarea name="about_text1" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['about_text1'] ?? '') ?></textarea></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Text Paragraph 2</label><textarea name="about_text2" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['about_text2'] ?? '') ?></textarea></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Text Paragraph 3</label><textarea name="about_text3" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['about_text3'] ?? '') ?></textarea></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Quote</label><input type="text" name="about_quote" value="<?= htmlspecialchars($settings['about_quote'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Quote Author</label><input type="text" name="about_author" value="<?= htmlspecialchars($settings['about_author'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    
                    <h4 class="font-medium mt-4">Bio Sub-section</h4>
                    <div><label class="block text-gray-700 font-medium mb-1">Bio Title</label><input type="text" name="bio_title" value="<?= htmlspecialchars($settings['bio_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Bio Subtitle</label><input type="text" name="bio_subtitle" value="<?= htmlspecialchars($settings['bio_subtitle'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Bio Text (Markdown/Linebreaks support)</label><textarea name="bio_text" rows="5" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['bio_text'] ?? '') ?></textarea></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Other Headings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-gray-700 font-medium mb-1">Results Title</label><input type="text" name="results_title" value="<?= htmlspecialchars($settings['results_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">FAQs Title</label><input type="text" name="faqs_title" value="<?= htmlspecialchars($settings['faqs_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Contact & Footer</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-gray-700 font-medium mb-1">Contact Title</label><input type="text" name="contact_title" value="<?= htmlspecialchars($settings['contact_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Display Email</label><input type="text" name="contact_email_display" value="<?= htmlspecialchars($settings['contact_email_display'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Address</label><input type="text" name="contact_address" value="<?= htmlspecialchars($settings['contact_address'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div><label class="block text-gray-700 font-medium mb-1">Phone</label><input type="text" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>" class="w-full px-4 py-2 border rounded"></div>
                    <div class="md:col-span-2"><label class="block text-gray-700 font-medium mb-1">Opening Hours (one per line)</label><textarea name="contact_hours" rows="4" class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($settings['contact_hours'] ?? '') ?></textarea></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8 border-l-4 border-l-blue-500">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Email Notifications</h3>
                <p class="text-gray-500 text-sm mb-4">Configure email alerts for new bookings. The system will use standard PHP mail(), which requires a functioning local SMTP (like MailHog) or a live server environment to actually send emails.</p>
                <div class="grid grid-cols-1 gap-4">
                    <div><label class="block text-gray-700 font-medium mb-1">Admin Alert Email</label><input type="email" name="admin_email" value="<?= htmlspecialchars($settings['admin_email'] ?? '') ?>" class="w-full px-4 py-2 border rounded" placeholder="Where should new booking alerts go?"></div>
                    <div class="flex items-center">
                        <input type="checkbox" id="notify_admin" name="notify_admin" value="1" <?= ($settings['notify_admin'] ?? '1') == '1' ? 'checked' : '' ?> class="w-5 h-5 text-blue-600 rounded">
                        <label for="notify_admin" class="ml-2 text-gray-700 font-medium">Send Email to Admin on New Booking</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="notify_client" name="notify_client" value="1" <?= ($settings['notify_client'] ?? '1') == '1' ? 'checked' : '' ?> class="w-5 h-5 text-blue-600 rounded">
                        <label for="notify_client" class="ml-2 text-gray-700 font-medium">Send Confirmation Email to Client</label>
                    </div>
                </div>
                <div class="mt-6 border-t pt-4">
                    <h4 class="font-medium text-gray-800 mb-2">SMTP Settings (For sending emails)</h4>
                    <p class="text-xs text-gray-500 mb-4">Because you have 2FA enabled on your Google account, you must generate an <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-500 underline">App Password</a> to use here.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1 text-sm">SMTP Host</label>
                            <input type="text" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com') ?>" class="w-full px-4 py-2 border rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1 text-sm">SMTP Port</label>
                            <input type="text" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>" class="w-full px-4 py-2 border rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1 text-sm">Gmail Address (Username)</label>
                            <input type="email" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" class="w-full px-4 py-2 border rounded text-sm" placeholder="yourname@gmail.com">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1 text-sm">App Password</label>
                            <input type="password" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>" class="w-full px-4 py-2 border rounded text-sm" placeholder="16-character App Password">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-xl font-medium mb-4 pb-2 border-b">SEO Settings</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Meta Title</label>
                    <input type="text" name="seo_title" value="<?= htmlspecialchars($settings['seo_title'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Meta Description</label>
                    <textarea name="seo_description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($settings['seo_description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Booking Settings Removed -->
            
            <button type="submit" class="bg-blue-600 text-white font-medium py-3 px-8 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">Save All Settings</button>

        </form>
    </div>

    <script>
        const typeSelect = document.getElementById('videoTypeSelect');
        const urlGroup = document.getElementById('videoUrlGroup');
        const uploadGroup = document.getElementById('videoUploadGroup');
        const urlInput = document.querySelector('input[name="hero_video_url"]');
        
        // Preview Elements
        const previewStart = document.getElementById('previewStart');
        const previewEnd = document.getElementById('previewEnd');
        const previewPosX = document.getElementById('previewPosX');
        const previewPosY = document.getElementById('previewPosY');
        const posXVal = document.getElementById('posXVal');
        const posYVal = document.getElementById('posYVal');
        const previewContainer = document.getElementById('previewContainer');
        
        // PHP Injected Values for initial preview
        const currentUpload = <?= json_encode($settings['hero_video_upload'] ?? '') ?>;
        
        function updatePreview() {
            const type = typeSelect.value;
            let src = type === 'url' ? urlInput.value : ('../' + currentUpload);
            
            // If they just switched to upload but haven't uploaded anything yet
            if (type === 'upload' && !currentUpload) {
                previewContainer.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-gray-400">Please upload a video and save to preview.</div>';
                return;
            }
            if (!src) return;

            const start = previewStart.value || 0;
            const end = previewEnd.value;
            const x = previewPosX.value;
            const y = previewPosY.value;
            
            posXVal.textContent = x + '%';
            posYVal.textContent = y + '%';
            
            const objectStyle = `object-fit: cover; object-position: ${x}% ${y}%; width: 100%; height: 100%;`;
            
            // Check if YouTube
            if (src.includes('youtube.com') || src.includes('youtu.be')) {
                // simple ID extraction
                let videoId = src.split('v=')[1];
                if (!videoId && src.includes('youtu.be/')) videoId = src.split('youtu.be/')[1];
                if (videoId) {
                    const ampersandPosition = videoId.indexOf('&');
                    if(ampersandPosition !== -1) videoId = videoId.substring(0, ampersandPosition);
                    
                    let ytUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&playlist=${videoId}&controls=0`;
                    if (start > 0) ytUrl += `&start=${start}`;
                    if (end > 0) ytUrl += `&end=${end}`;
                    
                    // Note: object-fit doesn't work perfectly on iframes natively, but we apply it anyway
                    // A true wrapper is needed for YT crop, but this gives a rough idea.
                    previewContainer.innerHTML = `<iframe style="${objectStyle} pointer-events: none; transform: scale(1.5);" src="${ytUrl}" frameborder="0" allow="autoplay; fullscreen"></iframe>`;
                }
            } else {
                // Direct video
                let vidSrc = src;
                if (start > 0 || end > 0) {
                    vidSrc += `#t=${start}`;
                    if (end > 0) vidSrc += `,${end}`;
                }
                
                previewContainer.innerHTML = `<video autoplay muted loop playsinline style="${objectStyle}">
                    <source src="${vidSrc}">
                </video>`;
            }
        }
        
        function toggleVideoGroups() {
            if (typeSelect.value === 'url') {
                urlGroup.classList.remove('hidden');
                uploadGroup.classList.add('hidden');
            } else {
                urlGroup.classList.add('hidden');
                uploadGroup.classList.remove('hidden');
            }
            updatePreview();
        }

        function toggleFounderUpload() {
            const type = document.getElementById('founder_image_type').value;
            if (type === 'upload') {
                document.getElementById('founder_url_group').classList.add('hidden');
                document.getElementById('founder_upload_group').classList.remove('hidden');
            } else {
                document.getElementById('founder_url_group').classList.remove('hidden');
                document.getElementById('founder_upload_group').classList.add('hidden');
            }
        }
        
        typeSelect.addEventListener('change', toggleVideoGroups);
        xSlider.addEventListener('input', (e) => {
            xVal.textContent = e.target.value + '%';
            updatePreview();
        });
        ySlider.addEventListener('input', (e) => {
            yVal.textContent = e.target.value + '%';
            updatePreview();
        });
        
        // Init
        toggleVideoGroups();
        toggleFounderUpload();
        updatePreview();
        const fXSlider = document.getElementById('fXSlider');
        const fYSlider = document.getElementById('fYSlider');
        const fXVal = document.getElementById('fXVal');
        const fYVal = document.getElementById('fYVal');
        
        if (fXSlider) {
            fXSlider.addEventListener('input', (e) => fXVal.textContent = e.target.value + '%');
            fYSlider.addEventListener('input', (e) => fYVal.textContent = e.target.value + '%');
        }
    </script>
</body>
</html>
