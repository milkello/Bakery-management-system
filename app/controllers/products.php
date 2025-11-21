<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf($_POST['csrf'] ?? '')) {
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        // Handle Edit Product
        $stmt = $pdo->prepare('UPDATE products SET name = ?, sku = ?, price = ?, unit = ? WHERE id = ?');
        $stmt->execute([$_POST['name'], $_POST['sku'], $_POST['price'], $_POST['unit'], $_POST['id']]);
    } else {
        // Handle Add Product
        $stmt = $pdo->prepare('INSERT INTO products (name, sku, price, unit) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_POST['name'], $_POST['sku'], $_POST['price'], $_POST['unit']]);
    }
    header('Location: ?page=products'); 
    exit;
}

// Handle Delete Product via AJAX
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

$prods = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();

// Calculate statistics
$total_products = count($prods);

// Total stock value = sum(price * stock)
$total_value_stmt = $pdo->query('SELECT SUM(price * COALESCE(stock,0)) AS total_stock_value FROM products');
$total_value_row = $total_value_stmt->fetch(PDO::FETCH_ASSOC);
$total_value = $total_value_row['total_stock_value'] ?? 0;

// Average unit price unchanged
$avg_price = $pdo->query('SELECT AVG(price) FROM products')->fetchColumn();
?>

<!-- Products Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="package" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $total_products ?></h3>
        <p class="text-gray-400">Total Products</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="dollar-sign" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= number_format($total_value, 0) ?> Rwf</h3>
        <p class="text-gray-400">Total Value</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="trending-up" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= number_format($avg_price, 0) ?> Rwf</h3>
        <p class="text-gray-400">Avg. Price</p>
    </div>
</div>

<!-- Products Table -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">Product List</h3>
        <div class="flex items-center space-x-3">
            <a href="?page=export_page_pdf&type=products" target="_blank"
               class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i data-feather="download" class="w-4 h-4"></i>
                <span>Export PDF</span>
            </a>
            <button id="addProductBtn" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i data-feather="plus" class="w-4 h-4"></i>
                <span>Add Product</span>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 px-4 text-lime-400">Product</th>
                    <th class="text-left py-3 px-4 text-lime-400">SKU</th>
                    <th class="text-left py-3 px-4 text-lime-400">Price</th>
                    <th class="text-left py-3 px-4 text-lime-400">Stock</th>
                    <th class="text-left py-3 px-4 text-lime-400">Value</th>
                    <th class="text-left py-3 px-4 text-lime-400">Unit</th>
                    <th class="text-left py-3 px-4 text-lime-400">Created</th>
                    <th class="text-left py-3 px-4 text-lime-400">Actions</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                <?php foreach($prods as $p): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-700" data-product-id="<?= $p['id'] ?>">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                                <i data-feather="package" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($p['name']) ?></p>
                                <p class="text-gray-400 text-sm">ID: <?= htmlspecialchars($p['id']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full text-sm">
                            <?= htmlspecialchars($p['sku'] ?? 'N/A') ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-fuchsia-400 font-bold"><?= number_format($p['price'], 0) ?> Rwf</span>
                    </td>
                    <td class="py-3 px-4 text-gray-300">
                        <?= number_format($p['stock'] ?? 0, 0) ?>
                    </td>
                    <td class="py-3 px-4 text-gray-300">
                        <?php $row_value = ($p['price'] ?? 0) * ($p['stock'] ?? 0); ?>
                        <?= number_format($row_value, 0) ?> Rwf
                    </td>
                    <td class="py-3 px-4">
                        <span class="bg-lime-500 text-white px-2 py-1 rounded-full text-xs">
                            <?= htmlspecialchars($p['unit'] ?? 'piece') ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-400 text-sm">
                        <?= date('M j, Y', strtotime($p['created_at'])) ?>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <button class="edit-product text-lime-400 hover:text-lime-300" data-id="<?= $p['id'] ?>">
                                <i data-feather="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="delete-product text-fuchsia-400 hover:text-fuchsia-300" data-id="<?= $p['id'] ?>">
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

<!-- Add/Edit Product Modal -->
<div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalTitle" class="text-xl font-bold text-lime-400">Add New Product</h3>
            <button id="closeProductModal" class="text-gray-400 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="productForm" method="POST" class="space-y-4">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" id="productId" name="id">
            <input type="hidden" id="actionType" name="action" value="add">
            
            <div>
                <label class="block text-gray-400 text-sm mb-2">Product Name</label>
                <input type="text" id="productName" name="name" placeholder="Enter product name" required 
                       class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
            </div>
            
            <div>
                <label class="block text-gray-400 text-sm mb-2">SKU</label>
                <input type="text" id="productSku" name="sku" placeholder="Enter SKU" 
                       class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Price</label>
                    <input type="number" id="productPrice" name="price" placeholder="0.00" step="0.01" min="0" required
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Unit</label>
                    <input type="text" id="productUnit" name="unit" placeholder="piece" 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" id="cancelProductModal" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" id="saveProduct" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Save Product
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
            <h3 class="text-xl font-bold text-white mb-2">Delete Product</h3>
            <p class="text-gray-400 mb-6">Are you sure you want to delete this product? This action cannot be undone.</p>
            
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
    const productModal = document.getElementById('productModal');
    const deleteModal = document.getElementById('deleteModal');
    const productForm = document.getElementById('productForm');
    const modalTitle = document.getElementById('modalTitle');
    let currentProductId = null;

    // Show Add Product Modal
    document.getElementById('addProductBtn').addEventListener('click', () => {
        modalTitle.textContent = 'Add New Product';
        productForm.reset();
        document.getElementById('productId').value = '';
        document.getElementById('actionType').value = 'add';
        productModal.classList.remove('hidden');
    });

    // Close modals
    document.getElementById('closeProductModal').addEventListener('click', () => {
        productModal.classList.add('hidden');
    });

    document.getElementById('cancelProductModal').addEventListener('click', () => {
        productModal.classList.add('hidden');
    });

    document.getElementById('cancelDelete').addEventListener('click', () => {
        deleteModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    productModal.addEventListener('click', (e) => {
        if (e.target === productModal) {
            productModal.classList.add('hidden');
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
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.currentTarget.getAttribute('data-id');
                editProduct(productId);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-product').forEach(button => {
            button.addEventListener('click', (e) => {
                const productId = e.currentTarget.getAttribute('data-id');
                showDeleteModal(productId);
            });
        });
    }

    // Initial attachment
    attachEventListeners();

    // Edit product function
    async function editProduct(id) {
        try {
            // Get product data (you might want to create a separate endpoint for this)
            // For now, we'll get it from the table row
            const row = document.querySelector(`tr[data-product-id="${id}"]`);
            if (row) {
                const name = row.querySelector('.font-medium').textContent;
                const sku = row.querySelector('.bg-gray-700').textContent.trim();
                const price = row.querySelector('.text-fuchsia-400').textContent.replace('$', '');
                const unit = row.querySelector('.bg-lime-500').textContent;
                
                modalTitle.textContent = 'Edit Product';
                document.getElementById('productId').value = id;
                document.getElementById('actionType').value = 'edit';
                document.getElementById('productName').value = name;
                document.getElementById('productSku').value = sku === 'N/A' ? '' : sku;
                document.getElementById('productPrice').value = parseFloat(price);
                document.getElementById('productUnit').value = unit;
                
                productModal.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading product data:', error);
            alert('Error loading product data');
        }
    }

    // Show delete confirmation modal
    function showDeleteModal(id) {
        currentProductId = id;
        deleteModal.classList.remove('hidden');
    }

    // Handle delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', async () => {
        try {
            const response = await fetch(`?page=products&action=delete&id=${currentProductId}`);
            
            if (response.ok) {
                // Remove the row from the table
                const row = document.querySelector(`tr[data-product-id="${currentProductId}"]`);
                if (row) {
                    row.remove();
                }
                
                deleteModal.classList.add('hidden');
                
                // Show success message
                showNotification('Product deleted successfully', 'success');
                
                // Reload page to update stats
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error('Failed to delete product');
            }
        } catch (error) {
            console.error('Error deleting product:', error);
            showNotification('Error deleting product', 'error');
        }
    });

    // Handle form submission
    productForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitButton = document.getElementById('saveProduct');
        const originalText = submitButton.textContent;
        
        try {
            submitButton.textContent = 'Saving...';
            submitButton.disabled = true;

            const response = await fetch('?page=products', {
                method: 'POST',
                body: new FormData(productForm)
            });

            if (response.ok) {
                productModal.classList.add('hidden');
                showNotification('Product saved successfully', 'success');
                
                // Reload page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error('Failed to save product');
            }
        } catch (error) {
            console.error('Error saving product:', error);
            showNotification('Error saving product', 'error');
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