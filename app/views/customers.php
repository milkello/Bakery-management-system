<!-- Success/Error Messages -->
<?php if (!empty($message)): ?>
<div class="bg-lime-500 text-white px-6 py-4 rounded-lg mb-6 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <i data-feather="check-circle" class="w-5 h-5"></i>
        <span><?= htmlspecialchars($message) ?></span>
    </div>
    <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
        <i data-feather="x" class="w-5 h-5"></i>
    </button>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="bg-red-500 text-white px-6 py-4 rounded-lg mb-6 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <i data-feather="alert-circle" class="w-5 h-5"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
        <i data-feather="x" class="w-5 h-5"></i>
    </button>
</div>
<?php endif; ?>

<!-- Customers Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="users" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $total_customers ?></h3>
        <p class="text-gray-400">Total Customers</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="user" class="w-12 h-12 text-blue-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-blue-400"><?= $regular_count ?></h3>
        <p class="text-gray-400">Regular</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="briefcase" class="w-12 h-12 text-purple-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-purple-400"><?= $wholesale_count ?></h3>
        <p class="text-gray-400">Wholesale</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="star" class="w-12 h-12 text-yellow-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-yellow-400"><?= $vip_count ?></h3>
        <p class="text-gray-400">VIP</p>
    </div>
</div>

<!-- Top Buyers Section -->
<?php if (!empty($top_buyers)): ?>
<div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">🏆 Top 5 Customers (Last 30 Days)</h3>
        <a href="?page=exports_pdf&type=customers&from=<?= date('Y-m-d', strtotime('-30 days')) ?>&to=<?= date('Y-m-d') ?>" target="_blank"
           class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i data-feather="download" class="w-4 h-4"></i>
            <span>Export PDF</span>
        </a>
    </div>
    
    <div class="grid grid-cols-1 gap-4">
        <?php 
        $rank = 1;
        foreach($top_buyers as $buyer): 
            $rank_colors = ['bg-yellow-500', 'bg-gray-400', 'bg-orange-600', 'bg-lime-600', 'bg-blue-600'];
            $rank_color = $rank_colors[$rank - 1] ?? 'bg-gray-600';
            $medals = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
            $medal = $medals[$rank - 1] ?? '';
        ?>
        <div class="bg-gray-700 rounded-lg p-4 flex items-center justify-between hover:bg-gray-600 transition-colors">
            <div class="flex items-center space-x-4">
                <div class="<?= $rank_color ?> rounded-full w-12 h-12 flex items-center justify-center text-white font-bold text-xl">
                    <?= $medal ?>
                </div>
                <div>
                    <p class="text-white font-bold text-lg"><?= htmlspecialchars($buyer['name']) ?></p>
                    <div class="flex items-center space-x-3 text-sm text-gray-400">
                        <span class="flex items-center space-x-1">
                            <i data-feather="tag" class="w-3 h-3"></i>
                            <span><?= htmlspecialchars($buyer['customer_type']) ?></span>
                        </span>
                        <?php if (!empty($buyer['phone'])): ?>
                        <span class="flex items-center space-x-1">
                            <i data-feather="phone" class="w-3 h-3"></i>
                            <span><?= htmlspecialchars($buyer['phone']) ?></span>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-lime-400"><?= number_format($buyer['total_spent'], 0) ?> Rwf</p>
                <div class="text-sm text-gray-400 space-y-1">
                    <p><?= number_format($buyer['total_purchases']) ?> purchases</p>
                    <p class="text-xs">Last: <?= date('M j', strtotime($buyer['last_purchase'])) ?></p>
                </div>
            </div>
        </div>
        <?php 
        $rank++;
        endforeach; 
        ?>
    </div>
</div>
<?php endif; ?>


<!-- Customer History Modal -->
<div id="customerHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-lime-400">Customer History</h3>
            <button id="closeHistoryModal" class="text-gray-400 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div id="historyContent" class="space-y-2 text-sm text-gray-300">
            <!-- Filled via AJAX -->
            <p>Loading...</p>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">Customers List</h3>
        <div class="flex items-center space-x-3">
            <a href="?page=exports_pdf&type=customers&from=<?= date('Y-m-d', strtotime('-30 days')) ?>&to=<?= date('Y-m-d') ?>" target="_blank"
               class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i data-feather="download" class="w-4 h-4"></i>
                <span>Export PDF</span>
            </a>
            <button id="addCustomerBtn" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i data-feather="plus" class="w-4 h-4"></i>
                <span>Add Customer</span>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 px-4 text-lime-400">Customer</th>
                    <th class="text-left py-3 px-4 text-lime-400">Contact</th>
                    <th class="text-left py-3 px-4 text-lime-400">Type</th>
                    <th class="text-left py-3 px-4 text-lime-400">Address</th>
                    <th class="text-left py-3 px-4 text-lime-400">Added By</th>
                    <th class="text-left py-3 px-4 text-lime-400">Actions</th>
                </tr>
            </thead>
            <tbody id="customersTableBody">
                <?php foreach($customers as $customer): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-700" data-customer-id="<?= $customer['id'] ?>">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                                <i data-feather="user" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($customer['name']) ?></p>
                                <?php if (!empty($customer['notes'])): ?>
                                <p class="text-gray-400 text-sm"><?= htmlspecialchars(substr($customer['notes'], 0, 30)) ?><?= strlen($customer['notes']) > 30 ? '...' : '' ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex flex-col">
                            <?php if (!empty($customer['phone'])): ?>
                            <span class="text-sm"><i data-feather="phone" class="w-3 h-3 inline"></i> <?= htmlspecialchars($customer['phone']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($customer['email'])): ?>
                            <span class="text-gray-400 text-sm"><i data-feather="mail" class="w-3 h-3 inline"></i> <?= htmlspecialchars($customer['email']) ?></span>
                            <?php endif; ?>
                            <?php if (empty($customer['phone']) && empty($customer['email'])): ?>
                            <span class="text-gray-400 text-sm">No contact info</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <?php
                        $type_colors = [
                            'Regular' => 'blue',
                            'Wholesale' => 'purple',
                            'VIP' => 'yellow'
                        ];
                        $color = $type_colors[$customer['customer_type']] ?? 'gray';
                        ?>
                        <span class="bg-<?= $color ?>-500 text-white px-3 py-1 rounded-full text-sm">
                            <?= htmlspecialchars($customer['customer_type']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-sm"><?= !empty($customer['address']) ? htmlspecialchars(substr($customer['address'], 0, 30)) : 'N/A' ?><?= !empty($customer['address']) && strlen($customer['address']) > 30 ? '...' : '' ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-gray-400 text-sm"><?= htmlspecialchars($customer['created_by_name'] ?? 'Unknown') ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <button class="view-history text-blue-400 hover:text-blue-300" 
                                    data-id="<?= $customer['id'] ?>" title="View History">
                                <i data-feather="clock" class="w-4 h-4"></i>
                            </button>
                            <button class="edit-customer text-lime-400 hover:text-lime-300" 
                                    data-id="<?= $customer['id'] ?>"
                                    data-name="<?= htmlspecialchars($customer['name']) ?>"
                                    data-phone="<?= htmlspecialchars($customer['phone'] ?? '') ?>"
                                    data-email="<?= htmlspecialchars($customer['email'] ?? '') ?>"
                                    data-address="<?= htmlspecialchars($customer['address'] ?? '') ?>"
                                    data-type="<?= htmlspecialchars($customer['customer_type']) ?>"
                                    data-notes="<?= htmlspecialchars($customer['notes'] ?? '') ?>">
                                <i data-feather="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="delete-customer text-fuchsia-400 hover:text-fuchsia-300" data-id="<?= $customer['id'] ?>">
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

<!-- Add/Edit Customer Modal -->
<div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalTitle" class="text-xl font-bold text-lime-400">Add New Customer</h3>
            <button id="closeCustomerModal" class="text-gray-400 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="customerForm" method="POST" class="space-y-4">
            <input type="hidden" name="customer_id" id="customerId">
            <input type="hidden" name="save_customer" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-400 text-sm mb-2">Customer Name *</label>
                    <input type="text" id="customerName" name="name" placeholder="Enter customer name" required 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Phone Number</label>
                    <input type="tel" id="customerPhone" name="phone" placeholder="+250 XXX XXX XXX" 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Email Address</label>
                    <input type="email" id="customerEmail" name="email" placeholder="customer@example.com" 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Customer Type</label>
                    <select id="customerType" name="customer_type" 
                            class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <option value="Regular">Regular</option>
                        <option value="Wholesale">Wholesale</option>
                        <option value="VIP">VIP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Address</label>
                    <input type="text" id="customerAddress" name="address" placeholder="Customer address" 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div>
                <label class="block text-gray-400 text-sm mb-2">Notes</label>
                <textarea id="customerNotes" name="notes" placeholder="Additional notes about the customer" rows="3"
                          class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500"></textarea>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" id="cancelCustomerModal" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Save Customer
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
            <h3 class="text-xl font-bold text-white mb-2">Delete Customer</h3>
            <p class="text-gray-400 mb-6">Are you sure you want to delete this customer? This action cannot be undone.</p>
            
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
// Modal functionality
const customerModal = document.getElementById('customerModal');
const deleteModal = document.getElementById('deleteModal');
const addCustomerBtn = document.getElementById('addCustomerBtn');
const closeCustomerModal = document.getElementById('closeCustomerModal');
const cancelCustomerModal = document.getElementById('cancelCustomerModal');
const cancelDelete = document.getElementById('cancelDelete');
const confirmDelete = document.getElementById('confirmDelete');
const customerForm = document.getElementById('customerForm');
const modalTitle = document.getElementById('modalTitle');

const historyModal = document.getElementById('customerHistoryModal');
const closeHistoryModal = document.getElementById('closeHistoryModal');
const historyContent = document.getElementById('historyContent');

let deleteCustomerId = null;

// Open add customer modal
addCustomerBtn.addEventListener('click', () => {
    modalTitle.textContent = 'Add New Customer';
    customerForm.reset();
    document.getElementById('customerId').value = '';
    customerModal.classList.remove('hidden');
    feather.replace();
});

// View history
document.querySelectorAll('.view-history').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        if (!id) return;
        historyContent.innerHTML = '<p>Loading...</p>';
        historyModal.classList.remove('hidden');

        const url = `?page=customers&action=history&id=${encodeURIComponent(id)}&ajax=1`;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                historyContent.innerHTML = html;
                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(() => {
                historyContent.innerHTML = '<p class="text-red-400">Failed to load history.</p>';
            });
    });
});

// Close history modal
closeHistoryModal.addEventListener('click', () => historyModal.classList.add('hidden'));
historyModal.addEventListener('click', (e) => {
    if (e.target === historyModal) historyModal.classList.add('hidden');
});

// Close customer modal
closeCustomerModal.addEventListener('click', () => customerModal.classList.add('hidden'));
cancelCustomerModal.addEventListener('click', () => customerModal.classList.add('hidden'));

// Edit customer
document.querySelectorAll('.edit-customer').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const phone = btn.dataset.phone;
        const email = btn.dataset.email;
        const address = btn.dataset.address;
        const type = btn.dataset.type;
        const notes = btn.dataset.notes;
        
        modalTitle.textContent = 'Edit Customer';
        document.getElementById('customerId').value = id;
        document.getElementById('customerName').value = name;
        document.getElementById('customerPhone').value = phone;
        document.getElementById('customerEmail').value = email;
        document.getElementById('customerAddress').value = address;
        document.getElementById('customerType').value = type;
        document.getElementById('customerNotes').value = notes;
        
        customerModal.classList.remove('hidden');
        feather.replace();
    });
});

// Delete customer
document.querySelectorAll('.delete-customer').forEach(btn => {
    btn.addEventListener('click', () => {
        deleteCustomerId = btn.dataset.id;
        deleteModal.classList.remove('hidden');
        feather.replace();
    });
});

// Cancel delete
cancelDelete.addEventListener('click', () => {
    deleteModal.classList.add('hidden');
    deleteCustomerId = null;
});

// Confirm delete
confirmDelete.addEventListener('click', () => {
    if (deleteCustomerId) {
        window.location.href = `?page=customers&action=delete&id=${deleteCustomerId}`;
    }
});

// Close modals on outside click
customerModal.addEventListener('click', (e) => {
    if (e.target === customerModal) customerModal.classList.add('hidden');
});

deleteModal.addEventListener('click', (e) => {
    if (e.target === deleteModal) deleteModal.classList.add('hidden');
});

// Initialize feather icons
feather.replace();
</script>
