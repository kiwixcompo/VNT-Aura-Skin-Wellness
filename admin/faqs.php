<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

// Handle deletes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM faqs WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    header('Location: faqs.php?msg=deleted');
    exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = $_POST['id'] ?? '';
    $question = $_POST['question'] ?? '';
    $answer = $_POST['answer'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;
    
    
    
    if ($id) {
        $stmt = $pdo->prepare('UPDATE faqs SET question=?, answer=?, display_order=? WHERE id=?');
        $stmt->execute([$question, $answer, $display_order, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO faqs (question, answer, display_order) VALUES (?, ?, ?)');
        $stmt->execute([$question, $answer, $display_order]);
    }
    header('Location: faqs.php?msg=saved');
    exit;
}

$stmt = $pdo->query('SELECT * FROM faqs ORDER BY display_order ASC, id ASC');
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage FAQs - VNT Aura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    
    <!-- Sidebar -->
    <div class="fixed w-64 h-full bg-white border-r border-gray-200 z-10">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-xl font-bold tracking-wider uppercase text-gray-900">VNT Admin</h1>
        </div>
        <nav class="p-4 space-y-2">
            <a href="index.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-cog w-6"></i> Settings</a>
            <a href="bookings.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-calendar-alt w-6"></i> Bookings</a>
            <a href="treatments.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-spa w-6"></i> Advanced Therapies</a>
            <a href="programmes.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-layer-group w-6"></i> Skin Journeys</a>
            
            <p class="px-4 pt-4 text-xs font-bold text-gray-400 uppercase">CMS Content</p>
            <a href="faqs.php" class="block py-2 px-4 bg-gray-100 text-gray-900 font-medium rounded transition-colors"><i class="fas fa-question-circle w-6"></i> FAQs</a>
            <a href="testimonials.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-comment-dots w-6"></i> Testimonials</a>
            <a href="gallery.php" class="block py-2 px-4 text-gray-600 hover:bg-gray-100 rounded transition-colors"><i class="fas fa-images w-6"></i> Gallery</a>

            <a href="logout.php" class="block py-2 px-4 text-red-600 hover:bg-red-50 rounded transition-colors mt-8"><i class="fas fa-sign-out-alt w-6"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-semibold">FAQs</h2>
            <button onclick="openModal()" class="bg-blue-900 text-white px-4 py-2 rounded shadow hover:bg-blue-800"><i class="fas fa-plus mr-2"></i> Add New</button>
        </div>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-6 font-medium">Changes saved successfully.</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-6 font-medium">Item deleted.</div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 font-medium text-gray-600">Question</th>
                        <th class="p-4 font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="p-4 font-medium"><?= htmlspecialchars($item["question"]) ?></td>
                        <td class="p-4">
                            <button onclick='editModal(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)' class="text-blue-600 hover:underline mr-3">Edit</button>
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="itemModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-8 relative">
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800"><i class="fas fa-times text-xl"></i></button>
            <h3 class="text-2xl font-semibold mb-6" id="modalTitle">Add Item</h3>
            
            <form method="POST" action="faqs.php" class="space-y-4" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="form_id" value="">
                
                <div><label class="block text-gray-700 font-medium mb-1">Question</label><input type="text" name="question" id="form_q" class="w-full px-4 py-2 border rounded-lg" required></div>
                <div><label class="block text-gray-700 font-medium mb-1">Answer</label><textarea name="answer" id="form_a" rows="4" class="w-full px-4 py-2 border rounded-lg" required></textarea></div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Display Order</label>
                    <input type="number" name="display_order" id="form_display_order" class="w-full px-4 py-2 border rounded-lg" value="0" required>
                </div>
                
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('itemModal');
        const mTitle = document.getElementById('modalTitle');
        const formId = document.getElementById('form_id');
        const formOrder = document.getElementById('form_display_order');
        const formQ = document.getElementById('form_q');
        const formA = document.getElementById('form_a');

        function openModal() {
            mTitle.textContent = 'Add Item';
            formId.value = '';
            formOrder.value = '0';
            formQ.value = '';
            formA.value = '';
            modal.style.display = 'flex';
        }

        function editModal(data) {
            mTitle.textContent = 'Edit Item';
            formId.value = data.id;
            formOrder.value = data.display_order;
            formQ.value = data.question;
            formA.value = data.answer;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }
    </script>
</body>
</html>
