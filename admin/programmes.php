<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

// Handle deletes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM programmes WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    header('Location: programmes.php?msg=deleted');
    exit;
}

// Handle save (create/update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $duration = $_POST['duration'] ?? '';
    $faces_link = $_POST['faces_link'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;
    
    // Handle Image Upload
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['image_upload']['size'] > 2 * 1024 * 1024) {
            header('Location: programmes.php?msg=size_error');
            exit;
        }
        $tmpName = $_FILES['image_upload']['tmp_name'];
        $name = basename($_FILES['image_upload']['name']);
        $uploadDir = __DIR__ . '/../assets/images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $safeName = 'prog_' . time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $name);
        $destPath = $uploadDir . $safeName;
        if (move_uploaded_file($tmpName, $destPath)) {
            $image_url = 'assets/images/' . $safeName;
        }
    }
    
    if ($id) {
        $stmt = $pdo->prepare('UPDATE programmes SET title=?, description=?, price=?, image_url=?, display_order=? WHERE id=?');
        $stmt->execute([$title, $description, $price, $image_url, $display_order, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO programmes (title, description, price, duration, faces_link, image_url, display_order) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $description, $price, $image_url, $display_order]);
    }
    header('Location: programmes.php?msg=saved');
    exit;
}

$stmt = $pdo->query('SELECT * FROM programmes ORDER BY display_order ASC, id ASC');
$programmes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Skin Journeys - VNT Aura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    
    <!-- Sidebar -->
    <div class="fixed w-64 h-full bg-white border-r border-gray-200 z-10">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-xl font-bold tracking-wider uppercase text-gray-900">VNT Admin</h1>
        </div>
            <a href="index.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-cog w-6"></i> Settings</a>
                        <a href="bookings.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-calendar-alt w-6"></i> Bookings</a>
                        <a href="treatments.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-spa w-6"></i> Advanced Therapies</a>
                        <a href="programmes.php" class="block py-2 px-4 bg-gray-100 text-gray-900 font-medium rounded transition-colors"><i class="fas fa-layer-group w-6"></i> Skin Journeys</a>
                        
                        <p class="px-4 pt-4 text-xs font-bold text-gray-400 uppercase">CMS Content</p>
                        <a href="faqs.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-question-circle w-6"></i> FAQs</a>
                        <a href="testimonials.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-comment-dots w-6"></i> Testimonials</a>
                        <a href="gallery.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-images w-6"></i> Gallery</a>
            
                        <a href="logout.php" class="block py-2 px-4 text-red-600 hover:bg-red-50 rounded transition-colors mt-8"><i class="fas fa-sign-out-alt w-6"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-semibold">Skin Skin Journeys</h2>
            <button onclick="openModal()" class="bg-blue-900 text-white px-4 py-2 rounded shadow hover:bg-blue-800"><i class="fas fa-plus mr-2"></i> Add Skin Journey</button>
        </div>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-6 font-medium">Changes saved successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-6 font-medium">Skin Journey deleted.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'size_error'): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded mb-6 font-medium">Error: Image file size must be less than 2MB.</div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($programmes as $p): ?>
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col">
                    <?php $imgSrc = (strpos($p['image_url'], 'http') === 0 || strpos($p['image_url'], '/') === 0) ? $p['image_url'] : '../' . $p['image_url']; ?>
                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Skin Journey" class="h-48 w-full object-cover">
                    <div class="p-6 flex-grow">
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="text-sm text-gray-600 line-clamp-3 mb-4"><?= htmlspecialchars($p['description']) ?></p>
                        <div class="text-xs text-gray-400 mb-4">Display Order: <?= $p['display_order'] ?></div>
                        <div class="flex justify-end space-x-2 mt-auto">
                            <button onclick='editModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)' class="text-blue-600 hover:bg-blue-50 px-3 py-1 rounded text-sm"><i class="fas fa-edit"></i> Edit</button>
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="text-red-600 hover:bg-red-50 px-3 py-1 rounded text-sm"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="programmeModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative">
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800"><i class="fas fa-times text-xl"></i></button>
            <div class="p-8 pb-4 border-b">
                <h3 class="text-2xl font-semibold" id="modalTitle">Add Skin Journey</h3>
            </div>
            
            <div class="p-8">
                <form method="POST" action="programmes.php" class="space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="form_id" value="">
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Title</label>
                        <input type="text" name="title" id="form_title" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Description</label>
                        <textarea name="description" id="form_description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>
                    
                    <div class="border p-4 rounded-lg bg-gray-50">
                        <label class="block text-gray-700 font-medium mb-2">Image Source</label>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Upload New Image (Max 2MB)</label>
                                <input type="file" name="image_upload" accept="image/*" class="w-full text-sm">
                            </div>
                            <div class="flex items-center text-gray-400 text-sm">
                                <span class="flex-1 border-t"></span>
                                <span class="px-3">OR</span>
                                <span class="flex-1 border-t"></span>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Existing Image / URL</label>
                                <input type="text" name="image_url" id="form_image_url" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" required>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Display Order</label>
                        <input type="number" name="display_order" id="form_display_order" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="0" required>
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">Save Skin Journey</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('programmeModal');
        const mTitle = document.getElementById('modalTitle');
        const formId = document.getElementById('form_id');
        const formTitle = document.getElementById('form_title');
        const formDesc = document.getElementById('form_description');
        const formImage = document.getElementById('form_image_url');
        const formOrder = document.getElementById('form_display_order');

        function openModal() {
            mTitle.textContent = 'Add Skin Journey';
            formId.value = '';
            formTitle.value = '';
            formDesc.value = '';
            formImage.value = '';
            formOrder.value = '0';
            modal.style.display = 'flex';
        }

        function editModal(data) {
            mTitle.textContent = 'Edit Skin Journey';
            formId.value = data.id;
            formTitle.value = data.title;
            formDesc.value = data.description;
            formImage.value = data.image_url;
            formOrder.value = data.display_order;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }
    </script>
</body>
</html>
