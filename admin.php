<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'includes/database.php';

$message = '';

// Handle Text Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_text'])) {
    $stmt = $db->prepare("UPDATE settings SET value = ? WHERE key_name = ?");
    foreach (['hero_headline', 'hero_subheadline', 'founder_greeting', 'founder_bio'] as $key) {
        if (isset($_POST[$key])) {
            $stmt->execute([$_POST[$key], $key]);
        }
    }
    $message = "<div class='alert success'>Text content updated successfully.</div>";
}

// Handle Image Uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_upload'])) {
    $target_dir = "uploads/";
    $key_name = $_POST['image_key'];
    
    $file_ext = strtolower(pathinfo($_FILES["image_upload"]["name"], PATHINFO_EXTENSION));
    $new_filename = $key_name . '_' . time() . '.' . $file_ext;
    $target_file = $target_dir . $new_filename;

    // Check if image file is a actual image
    $check = getimagesize($_FILES["image_upload"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["image_upload"]["tmp_name"], $target_file)) {
            // Update database
            $stmt = $db->prepare("UPDATE settings SET value = ? WHERE key_name = ?");
            $stmt->execute([$target_file, $key_name]);
            $message = "<div class='alert success'>Image updated successfully.</div>";
        } else {
            $message = "<div class='alert error'>Sorry, there was an error uploading your file.</div>";
        }
    } else {
        $message = "<div class='alert error'>File is not an image.</div>";
    }
}

// Handle Status Updates
if (isset($_GET['complete'])) {
    $stmt = $db->prepare("UPDATE bookings SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$_GET['complete']]);
    header('Location: admin.php?msg=marked');
    exit;
}

// Handle Deletions
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: admin.php?msg=deleted');
    exit;
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'marked') $message = "<div class='alert success'>Booking marked as completed.</div>";
    if ($_GET['msg'] === 'deleted') $message = "<div class='alert success'>Booking deleted successfully.</div>";
}

// Fetch Bookings
$stmt = $db->query("SELECT * FROM bookings ORDER BY created_at DESC");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_bookings = count($bookings);
$pending_bookings = count(array_filter($bookings, function($b) { return $b['status'] === 'Pending'; }));
$completed_bookings = $total_bookings - $pending_bookings;

// Fetch Current Settings
$texts = [
    'hero_headline' => get_setting($db, 'hero_headline'),
    'hero_subheadline' => get_setting($db, 'hero_subheadline'),
    'founder_greeting' => get_setting($db, 'founder_greeting'),
    'founder_bio' => get_setting($db, 'founder_bio')
];

// Fetch Current Images
$images = [
    'hero_image' => get_setting($db, 'hero_image'),
    'about_image' => get_setting($db, 'about_image'),
    'treatments_image' => get_setting($db, 'treatments_image'),
    'programmes_image' => get_setting($db, 'programmes_image'),
    'founder_image' => get_setting($db, 'founder_image'),
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - VNT Aura</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; margin: 0; color: #333; }
        .header { background: #455A44; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header a { color: white; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); padding: 8px 15px; border-radius: 4px; transition: background 0.3s; }
        .header a:hover { background: rgba(255,255,255,0.1); }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #B4975A; }
        .stat-card h3 { margin: 0; color: #666; font-size: 1rem; font-weight: 500; }
        .stat-card .number { font-size: 2.5rem; font-weight: 600; color: #455A44; margin-top: 10px; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 2rem; margin-bottom: 2rem; }
        h2 { margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; font-weight: 600; color: #555; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        tr:hover { background-color: #fcfcfc; }
        .status-pending { color: #856404; background: #fff3cd; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-completed { color: #155724; background: #d4edda; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .btn-sm { display: inline-block; padding: 6px 12px; background: #455A44; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; transition: opacity 0.2s; }
        .btn-sm:hover { opacity: 0.9; }
        .btn-danger { background: #dc3545; }
        .image-manager { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem; }
        .image-card { border: 1px solid #eee; padding: 1.5rem; border-radius: 8px; text-align: center; background: #fafafa; transition: transform 0.2s; }
        .image-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .image-card img { max-width: 100%; height: 180px; object-fit: cover; border-radius: 6px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .image-card strong { display: block; margin-bottom: 15px; color: #444; }
        .image-card input[type="file"] { margin: 10px 0; width: 100%; font-size: 12px; }
        .image-card button { background: #B4975A; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; width: 100%; font-weight: 500; transition: background 0.3s; }
        .image-card button:hover { background: #a3864a; }
        
        /* Form styling for text manager */
        .text-form-group { margin-bottom: 1.5rem; }
        .text-form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #444; }
        .text-form-group input[type="text"], .text-form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: 'Inter', sans-serif; box-sizing: border-box; }
        .text-form-group textarea { resize: vertical; min-height: 80px; }
        .btn-submit-text { background: #455A44; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 14px; }
        .btn-submit-text:hover { background: #334433; }

        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; }
        .alert.success { background-color: #d4edda; color: #155724; }
        .alert.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>VNT Aura Admin</h1>
        <div>
            <a href="index.php" style="margin-right: 10px;">View Site</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php echo $message; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="number"><?php echo $total_bookings; ?></div>
            </div>
            <div class="stat-card" style="border-bottom-color: #856404;">
                <h3>Pending Actions</h3>
                <div class="number"><?php echo $pending_bookings; ?></div>
            </div>
            <div class="stat-card" style="border-bottom-color: #155724;">
                <h3>Completed</h3>
                <div class="number"><?php echo $completed_bookings; ?></div>
            </div>
        </div>

        <div class="card">
            <h2>Consultation Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date Received</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Preferred Date</th>
                        <th>Concerns</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bookings) > 0): ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($b['created_at'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($b['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($b['email']); ?><br><?php echo htmlspecialchars($b['phone']); ?></td>
                                <td><?php echo htmlspecialchars($b['preferred_date']); ?></td>
                                <td><?php echo htmlspecialchars(substr($b['concerns'], 0, 50)) . '...'; ?></td>
                                <td>
                                    <?php if ($b['status'] === 'Pending'): ?>
                                        <span class="status-pending">Pending</span>
                                    <?php else: ?>
                                        <span class="status-completed">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($b['status'] === 'Pending'): ?>
                                        <a href="?complete=<?php echo $b['id']; ?>" class="btn-sm">Mark Done</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo $b['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 3rem; color: #888;">No bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Website Media Manager</h2>
            <p style="color: #666; margin-bottom: 20px;">Upload new images to replace the current ones on the website.</p>
            <div class="image-manager">
                <?php foreach ($images as $key => $path): ?>
                <div class="image-card">
                    <img src="<?php echo htmlspecialchars($path); ?>" alt="<?php echo $key; ?>">
                    <strong><?php echo ucwords(str_replace('_', ' ', $key)); ?></strong>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="image_key" value="<?php echo $key; ?>">
                        <input type="file" name="image_upload" accept="image/*" required>
                        <button type="submit">Upload & Replace</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <h2>Website Text Content Manager</h2>
            <p style="color: #666; margin-bottom: 20px;">Update the main text shown on your website's front end.</p>
            <form method="POST">
                <input type="hidden" name="update_text" value="1">
                
                <h3 style="margin-top: 20px; color: #455A44;">Hero Section (Homepage)</h3>
                <div class="text-form-group">
                    <label>Hero Headline (You can use HTML like &lt;br&gt; and &lt;em&gt;)</label>
                    <input type="text" name="hero_headline" value="<?php echo htmlspecialchars($texts['hero_headline']); ?>" required>
                </div>
                <div class="text-form-group">
                    <label>Hero Subheadline</label>
                    <textarea name="hero_subheadline" required><?php echo htmlspecialchars($texts['hero_subheadline']); ?></textarea>
                </div>

                <h3 style="margin-top: 20px; color: #455A44;">Founder Section (About Page)</h3>
                <div class="text-form-group">
                    <label>Founder Greeting</label>
                    <input type="text" name="founder_greeting" value="<?php echo htmlspecialchars($texts['founder_greeting']); ?>" required>
                </div>
                <div class="text-form-group">
                    <label>Founder Bio (First Paragraph)</label>
                    <textarea name="founder_bio" required><?php echo htmlspecialchars($texts['founder_bio']); ?></textarea>
                </div>

                <button type="submit" class="btn-submit-text">Save Text Changes</button>
            </form>
        </div>

    </div>
</body>
</html>
 
