<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }

// Handle Add Material
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf($_POST['csrf'] ?? '')) {
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        // Handle Edit Material
        $stmt = $pdo->prepare('UPDATE raw_materials SET name = ?, unit = ?, category = ?, unit_cost = ?, stock_quantity = ?, low_threshold = ? WHERE id = ?');
        $stmt->execute([$_POST['name'], $_POST['unit'], $_POST['category'], $_POST['unit_cost'], $_POST['stock_quantity'], $_POST['low_threshold'], $_POST['id']]);
    } else {
        // Handle Add Material
        $stmt = $pdo->prepare('INSERT INTO raw_materials (name, unit, category, unit_cost, stock_quantity, low_threshold) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$_POST['name'], $_POST['unit'], $_POST['category'], $_POST['unit_cost'], $_POST['stock_quantity'], $_POST['low_threshold']]);
    }
    header('Location: ?page=raw_materials'); 
    exit;
}

// Handle Delete Material via AJAX
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('DELETE FROM raw_materials WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

$mats = $pdo->query('SELECT * FROM raw_materials ORDER BY created_at DESC')->fetchAll();

// Calculate statistics
$total_materials = count($mats);
$total_inventory_value = $pdo->query('SELECT SUM(unit_cost * stock_quantity) FROM raw_materials')->fetchColumn();
$low_stock_count = $pdo->query('SELECT COUNT(*) FROM raw_materials WHERE stock_quantity <= low_threshold')->fetchColumn();
?>

<!-- Raw Materials Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="box" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $total_materials ?></h3>
        <p class="text-gray-400">Total Materials</p>
    </div> -->
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="dollar-sign" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= number_format($total_inventory_value, 0) ?> Rwf</h3>
        <p class="text-gray-400">Total Stock Value</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="alert-triangle" class="w-12 h-12 text-yellow-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-yellow-400"><?= $low_stock_count ?></h3>
        <p class="text-gray-400">Low Stock Items</p>
    </div>
</div>

<!-- Raw Materials Table -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">Raw Materials List</h3>
        <div class="flex items-center space-x-3">
            <a href="?page=export_page_pdf&type=raw_materials" target="_blank"
               class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i data-feather="download" class="w-4 h-4"></i>
                <span>Export PDF</span>
            </a>
            <button id="addMaterialBtn" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i data-feather="plus" class="w-4 h-4"></i>
                <span>Add Material</span>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 px-4 text-lime-400">Material</th>
                    <th class="text-left py-3 px-4 text-lime-400">Stock</th>
                    <th class="text-left py-3 px-4 text-lime-400">Unit Cost</th>
                    <th class="text-left py-3 px-4 text-lime-400">Total Value</th>
                    <th class="text-left py-3 px-4 text-lime-400">Status</th>
                    <th class="text-left py-3 px-4 text-lime-400">Actions</th>
                </tr>
            </thead>
            <tbody id="materialsTableBody">
                <?php foreach($mats as $m): 
                    $total_value = $m['unit_cost'] * $m['stock_quantity'];
                    $is_low_stock = $m['stock_quantity'] <= $m['low_threshold'];
                    $status_color = $is_low_stock ? 'red' : 'green';
                    $status_text = $is_low_stock ? 'Low Stock' : 'In Stock';
                ?>
                <tr class="border-b border-gray-700 hover:bg-gray-700" data-material-id="<?= $m['id'] ?>">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                                <i data-feather="box" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($m['name']) ?></p>
                                <p class="text-gray-400 text-sm"><?= htmlspecialchars($m['unit']) ?></p>
                            </div>
                        </div>
                    </td>
                    <!-- <td class="py-3 px-4">
                        <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full text-sm">
                            <?= htmlspecialchars($m['category'] ?? 'Uncategorized') ?>
                        </span>
                    </td> -->
                    <td class="py-3 px-4">
                        <div class="flex flex-col">
                            <span class="font-medium"><?= number_format($m['stock_quantity'], 3) ?></span>
                            <span class="text-gray-400 text-sm">Threshold: <?= number_format($m['low_threshold'], 0) ?></span>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-fuchsia-400 font-bold"><?= number_format($m['unit_cost'], 0) ?> Rwf</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-lime-400 font-bold"><?= number_format($total_value, 0) ?> Rwf</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="bg-<?= $status_color ?>-500 text-white px-2 py-1 rounded-full text-xs">
                            <?= $status_text ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <button class="edit-material text-lime-400 hover:text-lime-300" data-id="<?= $m['id'] ?>">
                                <i data-feather="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="delete-material text-fuchsia-400 hover:text-fuchsia-300" data-id="<?= $m['id'] ?>">
                                <i data-feather="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Material Modal -->
<div id="materialModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalTitle" class="text-xl font-bold text-lime-400">Add New Material</h3>
            <button id="closeMaterialModal" class="text-gray-400 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="materialForm" method="POST" class="space-y-4">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" id="materialId" name="id">
            <input type="hidden" id="actionType" name="action" value="add">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Material Name</label>
                    <input type="text" id="materialName" name="name" placeholder="Enter material name" required 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Unit</label>
                    <input type="text" id="materialUnit" name="unit" placeholder="kg, liter, pieces" 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Category</label>
                    <input type="text" id="materialCategory" name="category" placeholder="Enter category" 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Unit Cost</label>
                    <input type="number" id="materialUnitCost" name="unit_cost" placeholder="0.0000" step="0.0001" required
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Stock Quantity</label>
                    <input type="number" id="materialStock" name="stock_quantity" placeholder="0.000" step="0.001" required
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Low Stock Threshold</label>
                    <input type="number" id="materialThreshold" name="low_threshold" placeholder="0.000" step="0.001" required
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" id="cancelMaterialModal" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" id="saveMaterial" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Save Material
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
        <div class="text-center">
            <i data-feather="alert-triangle" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
            <h3 class="text-xl font-bold text-white mb-2">Delete Material</h3>
            <p class="text-gray-400 mb-6">Are you sure you want to delete this material? This action cannot be undone.</p>
            
            <div class="flex justify-center space-x-4">
                <button id="cancelDelete" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
                <button id="confirmDelete" class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Modal elements
    const materialModal = document.getElementById('materialModal');
    const deleteModal = document.getElementById('deleteModal');
    const materialForm = document.getElementById('materialForm');
    const modalTitle = document.getElementById('modalTitle');
    let currentMaterialId = null;

    // Show Add Material Modal
    document.getElementById('addMaterialBtn').addEventListener('click', () => {
        modalTitle.textContent = 'Add New Material';
        materialForm.reset();
        document.getElementById('materialId').value = '';
        document.getElementById('actionType').value = 'add';
        materialModal.classList.remove('hidden');
    });

    // Close modals
    document.getElementById('closeMaterialModal').addEventListener('click', () => {
        materialModal.classList.add('hidden');
    });

    document.getElementById('cancelMaterialModal').addEventListener('click', () => {
        materialModal.classList.add('hidden');
    });

    document.getElementById('cancelDelete').addEventListener('click', () => {
        deleteModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    materialModal.addEventListener('click', (e) => {
        if (e.target === materialModal) {
            materialModal.classList.add('hidden');
        }
    });

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
        }
    });

    // Attach event listeners to edit and delete buttons
    function attachEventListeners() {
        // Edit buttons
        document.querySelectorAll('.edit-material').forEach(button => {
            button.addEventListener('click', (e) => {
                const materialId = e.currentTarget.getAttribute('data-id');
                editMaterial(materialId);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-material').forEach(button => {
            button.addEventListener('click', (e) => {
                const materialId = e.currentTarget.getAttribute('data-id');
                showDeleteModal(materialId);
            });
        });
    }

    // Initial attachment
    attachEventListeners();

    // Edit material function
    async function editMaterial(id) {
        try {
            // Get material data from the table row
            const row = document.querySelector(`tr[data-material-id="${id}"]`);
            if (row) {
                const name = row.querySelector('.font-medium').textContent;
                const unit = row.querySelector('.text-gray-400.text-sm').textContent;
                const category = row.querySelector('.bg-gray-700').textContent.trim();
                const stockElements = row.querySelectorAll('.flex.flex-col span');
                const stockQuantity = stockElements[0].textContent.trim();
                const lowThreshold = stockElements[1].textContent.replace('Threshold: ', '').trim();
                const unitCost = row.querySelector('.text-fuchsia-400').textContent.replace('$', '');
                
                modalTitle.textContent = 'Edit Material';
                document.getElementById('materialId').value = id;
                document.getElementById('actionType').value = 'edit';
                document.getElementById('materialName').value = name;
                document.getElementById('materialUnit').value = unit;
                document.getElementById('materialCategory').value = category === 'Uncategorized' ? '' : category;
                document.getElementById('materialUnitCost').value = parseFloat(unitCost);
                document.getElementById('materialStock').value = parseFloat(stockQuantity);
                document.getElementById('materialThreshold').value = parseFloat(lowThreshold);
                
                materialModal.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading material data:', error);
            showNotification('Error loading material data', 'error');
        }
    }

    // Show delete confirmation modal
    function showDeleteModal(id) {
        currentMaterialId = id;
        deleteModal.classList.remove('hidden');
    }

    // Handle delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', async () => {
        try {
            const response = await fetch(`?page=raw_materials&action=delete&id=${currentMaterialId}`);
            
            if (response.ok) {
                // Remove the row from the table
                const row = document.querySelector(`tr[data-material-id="${currentMaterialId}"]`);
                if (row) {
                    row.remove();
                }
                
                deleteModal.classList.add('hidden');
                
                // Show success message
                showNotification('Material deleted successfully', 'success');
                
                // Reload page to update stats
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error('Failed to delete material');
            }
        } catch (error) {
            console.error('Error deleting material:', error);
            showNotification('Error deleting material', 'error');
        }
    });

    // Handle form submission
    materialForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitButton = document.getElementById('saveMaterial');
        const originalText = submitButton.textContent;
        
        try {
            submitButton.textContent = 'Saving...';
            submitButton.disabled = true;

            const response = await fetch('?page=raw_materials', {
                method: 'POST',
                body: new FormData(materialForm)
            });

            if (response.ok) {
                materialModal.classList.add('hidden');
                showNotification('Material saved successfully', 'success');
                
                // Reload page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error('Failed to save material');
            }
        } catch (error) {
            console.error('Error saving material:', error);
            showNotification('Error saving material', 'error');
        } finally {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });

    // Notification function
    function showNotification(message, type) {
        // Remove existing notifications
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-lime-500 text-white' : 'bg-fuchsia-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Initial feather icons render
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>