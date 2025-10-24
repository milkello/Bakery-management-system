<!-- Recipes Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="book-open" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $total_recipes ?></h3>
        <p class="text-gray-400">Total Recipes</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="package" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= $total_products_with_recipes ?></h3>
        <p class="text-gray-400">Products with Recipes</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="list" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= number_format($avg_ingredients_per_recipe, 1) ?></h3>
        <p class="text-gray-400">Avg. Ingredients</p>
    </div>
</div>

<!-- Recipes Table -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">Recipe List</h3>
        <button id="addRecipeBtn" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i data-feather="plus" class="w-4 h-4"></i>
            <span>Add Recipe</span>
        </button>
    </div>
    
    <div class="space-y-6">
        <?php foreach($recipes as $recipe): ?>
        <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-lime-500 rounded-full flex items-center justify-center">
                        <i data-feather="book-open" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-white"><?= htmlspecialchars($recipe['product_name']) ?></h4>
                        <p class="text-gray-400 text-sm">Recipe ID: <?= $recipe['recipe_id'] ?></p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button class="edit-recipe text-lime-400 hover:text-lime-300" data-id="<?= $recipe['recipe_id'] ?>">
                        <i data-feather="edit" class="w-4 h-4"></i>
                    </button>
                    <button class="delete-recipe text-fuchsia-400 hover:text-fuchsia-300" data-id="<?= $recipe['recipe_id'] ?>">
                        <i data-feather="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <h5 class="text-lime-400 font-semibold mb-3 flex items-center">
                    <i data-feather="list" class="w-4 h-4 mr-2"></i>
                    Ingredients (<?= count($recipe['ingredients']) ?>)
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <?php foreach($recipe['ingredients'] as $ingredient): ?>
                    <div class="bg-gray-900 rounded-lg p-3 flex justify-between items-center">
                        <div>
                            <span class="text-white font-medium"><?= htmlspecialchars($ingredient['material_name']) ?></span>
                            <span class="text-gray-400 text-sm block"><?= $ingredient['quantity'] ?> <?= $ingredient['unit'] ?></span>
                        </div>
                        <span class="bg-lime-500 text-white px-2 py-1 rounded-full text-xs">
                            Material
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if(empty($recipe['ingredients'])): ?>
                <p class="text-gray-400 text-center py-4">No ingredients added yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($recipes)): ?>
        <div class="text-center py-8">
            <i data-feather="book-open" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
            <h3 class="text-xl font-bold text-gray-400 mb-2">No Recipes Found</h3>
            <p class="text-gray-500">Get started by creating your first recipe.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Recipe Modal -->
<div id="recipeModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalTitle" class="text-xl font-bold text-lime-400">Add New Recipe</h3>
            <button id="closeRecipeModal" class="text-gray-400 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="recipeForm" method="POST" class="space-y-6">
            <!-- Product Selection -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Select Product</label>
                <select name="product_id" required 
                        class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                    <option value="">Choose a product...</option>
                    <?php foreach($products as $product): ?>
                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Ingredients Section -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-lime-400">Ingredients</h4>
                    <button type="button" id="addIngredient" class="bg-lime-500 hover:bg-lime-600 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1">
                        <i data-feather="plus" class="w-4 h-4"></i>
                        <span>Add Ingredient</span>
                    </button>
                </div>
                
                <div id="ingredientsContainer" class="space-y-3">
                    <!-- Ingredients will be added here dynamically -->
                    <div class="ingredient-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-5">
                            <label class="block text-gray-400 text-sm mb-2">Material</label>
                            <select name="materials[]" 
                                    class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                                <option value="">Select material...</option>
                                <?php foreach($materials as $material): ?>
                                <option value="<?= $material['id'] ?>"><?= htmlspecialchars($material['name']) ?> (<?= $material['unit'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-gray-400 text-sm mb-2">Quantity</label>
                            <input type="number" name="quantities[]" step="0.001" placeholder="0.000"
                                   class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-gray-400 text-sm mb-2">Unit</label>
                            <input type="text" name="units[]" placeholder="kg, liter"
                                   class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        </div>
                        <div class="md:col-span-1">
                            <button type="button" class="remove-ingredient text-fuchsia-400 hover:text-fuchsia-300 w-full py-2">
                                <i data-feather="trash-2" class="w-4 h-4 mx-auto"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-700">
                <button type="button" id="cancelRecipeModal" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Save Recipe
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
            <h3 class="text-xl font-bold text-white mb-2">Delete Recipe</h3>
            <p class="text-gray-400 mb-6">Are you sure you want to delete this recipe? This action cannot be undone.</p>
            
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
    const recipeModal = document.getElementById('recipeModal');
    const deleteModal = document.getElementById('deleteModal');
    const recipeForm = document.getElementById('recipeForm');
    const ingredientsContainer = document.getElementById('ingredientsContainer');
    const modalTitle = document.getElementById('modalTitle');
    let currentRecipeId = null;

    // Show Add Recipe Modal
    document.getElementById('addRecipeBtn').addEventListener('click', () => {
        modalTitle.textContent = 'Add New Recipe';
        recipeForm.reset();
        // Reset to one ingredient row
        ingredientsContainer.innerHTML = createIngredientRow();
        recipeModal.classList.remove('hidden');
    });

    // Close modals
    document.getElementById('closeRecipeModal').addEventListener('click', () => {
        recipeModal.classList.add('hidden');
    });

    document.getElementById('cancelRecipeModal').addEventListener('click', () => {
        recipeModal.classList.add('hidden');
    });

    document.getElementById('cancelDelete').addEventListener('click', () => {
        deleteModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    recipeModal.addEventListener('click', (e) => {
        if (e.target === recipeModal) {
            recipeModal.classList.add('hidden');
        }
    });

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
        }
    });

    // Add ingredient row
    document.getElementById('addIngredient').addEventListener('click', () => {
        ingredientsContainer.insertAdjacentHTML('beforeend', createIngredientRow());
        feather.replace();
        attachRemoveListeners();
    });

    // Remove ingredient row
    function attachRemoveListeners() {
        document.querySelectorAll('.remove-ingredient').forEach(button => {
            button.addEventListener('click', function() {
                if (document.querySelectorAll('.ingredient-row').length > 1) {
                    this.closest('.ingredient-row').remove();
                }
            });
        });
    }

    // Create ingredient row HTML
    function createIngredientRow() {
        return `
            <div class="ingredient-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5">
                    <label class="block text-gray-400 text-sm mb-2">Material</label>
                    <select name="materials[]" 
                            class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <option value="">Select material...</option>
                        <?php foreach($materials as $material): ?>
                        <option value="<?= $material['id'] ?>"><?= htmlspecialchars($material['name']) ?> (<?= $material['unit'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-gray-400 text-sm mb-2">Quantity</label>
                    <input type="number" name="quantities[]" step="0.001" placeholder="0.000"
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-gray-400 text-sm mb-2">Unit</label>
                    <input type="text" name="units[]" placeholder="kg, liter"
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div class="md:col-span-1">
                    <button type="button" class="remove-ingredient text-fuchsia-400 hover:text-fuchsia-300 w-full py-2">
                        <i data-feather="trash-2" class="w-4 h-4 mx-auto"></i>
                    </button>
                </div>
            </div>
        `;
    }

    // Attach event listeners to delete buttons
    function attachEventListeners() {
        document.querySelectorAll('.delete-recipe').forEach(button => {
            button.addEventListener('click', (e) => {
                const recipeId = e.currentTarget.getAttribute('data-id');
                showDeleteModal(recipeId);
            });
        });
    }

    // Initial attachment
    attachEventListeners();
    attachRemoveListeners();

    // Show delete confirmation modal
    function showDeleteModal(id) {
        currentRecipeId = id;
        deleteModal.classList.remove('hidden');
    }

    // Handle delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', async () => {
        try {
            window.location.href = `?page=recipes&action=delete&id=${currentRecipeId}`;
        } catch (error) {
            console.error('Error deleting recipe:', error);
            showNotification('Error deleting recipe', 'error');
        }
    });

    // Notification function
    function showNotification(message, type) {
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