<!-- Employee Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="users" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $total_employees ?></h3>
        <p class="text-gray-400">Total Employees</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="clock" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= $current_working ?></h3>
        <p class="text-gray-400">Currently Working</p>
    </div>
    <!-- <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="award" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $avg_rating ?></h3>
        <p class="text-gray-400">Avg. Rating</p>
    </div> -->
</div>

<!-- Employee Table -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">Employee List</h3>
        <div class="flex space-x-4">
            <div class="relative">
                <input type="text" id="employeeSearch" placeholder="Search employees..." class="bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500 w-64">
                <i data-feather="search" class="absolute right-3 top-2.5 text-gray-400"></i>
            </div>
            <button id="addEmployeeBtn" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i data-feather="plus" class="w-4 h-4"></i>
                <span>Add Employee</span>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 px-4 text-lime-400">Employee</th>
                    <th class="text-left py-3 px-4 text-lime-400">Position</th>
                    <th class="text-left py-3 px-4 text-lime-400">Status</th>
                    <th class="text-left py-3 px-4 text-lime-400">Attendance</th>
                    <th class="text-left py-3 px-4 text-lime-400">Contact</th>
                    <th class="text-left py-3 px-4 text-lime-400">Actions</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
                <?php foreach ($employees as $emp): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-700" data-employee-id="<?= $emp['id'] ?>">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-3">
                            <img src="<?= htmlspecialchars($emp['profile_pic'] ?? 'https://static.photos/people/200x200/1') ?>" 
                                class="w-10 h-10 rounded-full" alt="<?= htmlspecialchars($emp['first_name']) ?> <?= htmlspecialchars($emp['last_name']) ?>">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($emp['first_name']) ?> <?= htmlspecialchars($emp['last_name']) ?></p>
                                <p class="text-gray-400 text-sm"><?= htmlspecialchars($emp['email']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4"><?= htmlspecialchars($emp['position']) ?></td>
                    <td class="py-3 px-4">
                        <?php
                        $statusColor = $emp['status'] === 'Active' ? 'green' : ($emp['status'] === 'On Leave' ? 'yellow' : 'red');
                        ?>
                        <span class="bg-<?= $statusColor ?>-500 text-white px-2 py-1 rounded-full text-xs">
                            <?= htmlspecialchars($emp['status']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <button 
                            id="present-<?= $emp['id'] ?>" 
                            data-id="<?= $emp['id'] ?>" 
                            data-status="present"
                            class="attendance-btn bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded mr-2 transition-colors">
                            Present
                        </button>
                        <button 
                            id="absent-<?= $emp['id'] ?>" 
                            data-id="<?= $emp['id'] ?>" 
                            data-status="absent"
                            class="attendance-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition-colors">
                            Absent
                        </button>
                    </td>
                    <td class="py-3 px-4"><?= htmlspecialchars($emp['phone']) ?></td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <button class="edit-employee text-lime-400 hover:text-lime-300" data-id="<?= $emp['id'] ?>">
                                <i data-feather="edit" class="w-4 h-4"></i>
                            </button>
                            <button class="delete-employee text-fuchsia-400 hover:text-fuchsia-300" data-id="<?= $emp['id'] ?>">
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

<!-- Add/Edit Employee Modal -->
<div id="employeeModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalTitle" class="text-xl font-bold text-lime-400">Add Employee</h3>
            <button id="closeModal" class="text-gray-400 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="employeeForm" class="space-y-4">
            <input type="hidden" id="employeeId" name="id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">First Name</label>
                    <input type="text" id="firstName" name="first_name" required 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Last Name</label>
                    <input type="text" id="lastName" name="last_name" required 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Email</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Position</label>
                    <input type="text" id="position" name="position" required 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Status</label>
                    <select id="status" name="status" required 
                            class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <option value="Active">Active</option>
                        <option value="On Leave">On Leave</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Salary Type</label>
                    <select id="salaryType" name="salary_type" required 
                            class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <option value="monthly">Monthly</option>
                        <option value="hourly">Hourly</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Salary Amount</label>
                    <input type="number" id="salaryAmount" name="salary_amount" step="0.01" required 
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" id="cancelModal" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" id="saveEmployee" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg transition-colors">
                    Save Employee
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
            <h3 class="text-xl font-bold text-white mb-2">Delete Employee</h3>
            <p class="text-gray-400 mb-6">Are you sure you want to delete this employee? This action cannot be undone.</p>
            
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

<!-- Attendance Message -->
<div id="attendanceMessage" class="fixed top-4 right-4 p-4 rounded-lg text-white z-50 hidden"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Search functionality
    const searchInput = document.getElementById("employeeSearch");
    const tableBody = document.getElementById("employeeTableBody");
    const originalTableHTML = tableBody.innerHTML;
    let searchTimeout;

    searchInput.addEventListener("input", (e) => {
        const searchValue = e.target.value.trim();
        clearTimeout(searchTimeout);
        
        if (searchValue === '') {
            tableBody.innerHTML = originalTableHTML;
            feather.replace();
            attachEventListeners();
            return;
        }
        
        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`/bms/bakery-management-system/app/controllers/search_employees.php?search=${encodeURIComponent(searchValue)}`);
                const data = await response.json();
                
                tableBody.innerHTML = "";
                
                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-400">No employees found.</td></tr>`;
                    return;
                }
                
                data.forEach(emp => {
                    const fullName = `${emp.first_name || ''} ${emp.last_name || ''}`.trim();
                    const photo = emp.profile_pic || "https://static.photos/people/200x200/1";
                    const statusColor = emp.status === "Active" ? "green" : (emp.status === "On Leave" ? "yellow" : "red");
                    const joinDate = emp.created_at ? emp.created_at.split(' ')[0] : '—';
                    
                    const row = `
                        <tr class="border-b border-gray-700 hover:bg-gray-700" data-employee-id="${emp.id}">
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <img src="${photo}" class="w-10 h-10 rounded-full" alt="${fullName}">
                                    <div>
                                        <p class="font-medium">${fullName}</p>
                                        <p class="text-gray-400 text-sm">${emp.email || ''}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">${emp.position || 'N/A'}</td>
                            <td class="py-3 px-4">
                                <span class="bg-${statusColor}-500 text-white px-2 py-1 rounded-full text-xs">${emp.status || 'Unknown'}</span>
                            </td>
                            <td class="py-3 px-4">
                                <button 
                                    id="present-${emp.id}" 
                                    data-id="${emp.id}" 
                                    data-status="present"
                                    class="attendance-btn bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded mr-2 transition-colors">
                                    Present
                                </button>
                                <button 
                                    id="absent-${emp.id}" 
                                    data-id="${emp.id}" 
                                    data-status="absent"
                                    class="attendance-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition-colors">
                                    Absent
                                </button>
                            </td>
                            <td class="py-3 px-4">${joinDate}</td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <button class="edit-employee text-lime-400 hover:text-lime-300" data-id="${emp.id}">
                                        <i data-feather="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="delete-employee text-fuchsia-400 hover:text-fuchsia-300" data-id="${emp.id}">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML("beforeend", row);
                });
                
                feather.replace();
                attachEventListeners();

                // Update attendance button states for the newly rendered rows
                try {
                    const ids = data.map(d => d.id);
                    updateAttendanceButtonsForIds(ids);
                } catch (e) {
                    // ignore
                }
            } catch (error) {
                console.error('Search error:', error);
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-red-400">Search failed: ${error.message}</td></tr>`;
            }
        }, 300);
    });

    // Modal elements
    const employeeModal = document.getElementById('employeeModal');
    const deleteModal = document.getElementById('deleteModal');
    const employeeForm = document.getElementById('employeeForm');
    const modalTitle = document.getElementById('modalTitle');
    let currentEmployeeId = null;

    // Show Add Employee Modal
    document.getElementById('addEmployeeBtn').addEventListener('click', () => {
        modalTitle.textContent = 'Add Employee';
        employeeForm.reset();
        document.getElementById('employeeId').value = '';
        employeeModal.classList.remove('hidden');
    });

    // Close modals
    document.getElementById('closeModal').addEventListener('click', () => {
        employeeModal.classList.add('hidden');
    });

    document.getElementById('cancelModal').addEventListener('click', () => {
        employeeModal.classList.add('hidden');
    });

    document.getElementById('cancelDelete').addEventListener('click', () => {
        deleteModal.classList.add('hidden');
    });

    // Attendance function
    async function handleAttendance(employeeId, status) {
        const msg = document.getElementById('attendanceMessage');
        
        try {
            const formData = new FormData();
            formData.append('employee_id', employeeId);
            formData.append('status', status);

            const res = await fetch('/bms/bakery-management-system/app/controllers/mark_attendance.php', {
                method: 'POST',
                body: formData
            });

            // Check HTTP status first
            if (!res.ok) {
                // Try to read response body for details
                let txt = '';
                try { txt = await res.text(); } catch (e) { /* ignore */ }
                console.error('Server returned non-OK status for mark_attendance:', res.status, txt);
                msg.textContent = 'Server error. See console for details.';
                msg.className = "fixed top-4 right-4 p-4 rounded-lg text-white z-50 bg-red-500";
                msg.classList.remove('hidden');
                return;
            }

            let data;
            try {
                data = await res.json();
            } catch (e) {
                // Response was not JSON (likely stray PHP output). Log raw body for debugging.
                const raw = await res.text();
                console.error('Failed to parse JSON from mark_attendance.php response:', e, raw);
                msg.textContent = 'Server returned unexpected response. Check console.';
                msg.className = "fixed top-4 right-4 p-4 rounded-lg text-white z-50 bg-red-500";
                msg.classList.remove('hidden');
                return;
            }

            if (data && data.success) {
                msg.textContent = data.message;
                msg.className = "fixed top-4 right-4 p-4 rounded-lg text-white z-50 bg-green-500";
                msg.classList.remove('hidden');

                const presentBtn = document.getElementById(`present-${employeeId}`);
                const absentBtn = document.getElementById(`absent-${employeeId}`);

                // Safely toggle disabled state and visual classes if buttons exist
                if (status === 'present') {
                    if (presentBtn) {
                        presentBtn.disabled = true;
                        presentBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    }
                    if (absentBtn) {
                        absentBtn.disabled = false;
                        absentBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                } else {
                    if (absentBtn) {
                        absentBtn.disabled = true;
                        absentBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    }
                    if (presentBtn) {
                        presentBtn.disabled = false;
                        presentBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                }
            } else {
                msg.textContent = data.error || 'Error updating attendance.';
                msg.className = "fixed top-4 right-4 p-4 rounded-lg text-white z-50 bg-red-500";
                msg.classList.remove('hidden');
            }
        } catch (error) {
            msg.textContent = "Network or server error.";
            msg.className = "fixed top-4 right-4 p-4 rounded-lg text-white z-50 bg-red-500";
            msg.classList.remove('hidden');
        }

        // Hide message after 3 seconds
        setTimeout(() => {
            msg.classList.add('hidden');
        }, 3000);
    }

    function attachAttendanceListeners() {
        document.querySelectorAll('.attendance-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const empId = this.dataset.id;
                const status = this.dataset.status;
                handleAttendance(empId, status);
            });
        });
    }

    // Fetch attendance status for a set of employee IDs and update button disabled states
    async function updateAttendanceButtonsForIds(ids) {
        if (!ids || ids.length === 0) return;

        try {
            const query = ids.join(',');
            const res = await fetch(`/bms/bakery-management-system/app/controllers/get_attendance_status.php?employee_ids=${encodeURIComponent(query)}`);
            const json = await res.json();
            if (!json.success) return;

            const map = json.data || {};

            ids.forEach(id => {
                const presentBtn = document.getElementById(`present-${id}`);
                const absentBtn = document.getElementById(`absent-${id}`);
                const status = map[id] || null; // 'present' or 'absent' or null

                if (status === 'present') {
                    if (presentBtn) {
                        presentBtn.disabled = true;
                        presentBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    }
                    if (absentBtn) {
                        absentBtn.disabled = false;
                        absentBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                } else if (status === 'absent') {
                    if (absentBtn) {
                        absentBtn.disabled = true;
                        absentBtn.classList.add('opacity-60', 'cursor-not-allowed');
                    }
                    if (presentBtn) {
                        presentBtn.disabled = false;
                        presentBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                } else {
                    // No attendance yet: enable both
                    if (presentBtn) {
                        presentBtn.disabled = false;
                        presentBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                    if (absentBtn) {
                        absentBtn.disabled = false;
                        absentBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                }
            });
        } catch (e) {
            console.error('Failed to load attendance statuses', e);
        }
    }

    // Attach event listeners to edit and delete buttons
    function attachEventListeners() {
        // Edit buttons
        document.querySelectorAll('.edit-employee').forEach(button => {
            button.addEventListener('click', (e) => {
                const employeeId = e.currentTarget.getAttribute('data-id');
                editEmployee(employeeId);
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-employee').forEach(button => {
            button.addEventListener('click', (e) => {
                const employeeId = e.currentTarget.getAttribute('data-id');
                showDeleteModal(employeeId);
            });
        });
        
        // Attendance listeners
        attachAttendanceListeners();
    }

    // Initial attachment
    attachEventListeners();

    // On initial load, collect displayed employee IDs and set button states
    (function initAttendanceStates() {
        const ids = Array.from(document.querySelectorAll('tr[data-employee-id]'))
            .map(tr => tr.getAttribute('data-employee-id'))
            .filter(Boolean)
            .map(id => parseInt(id, 10));

        updateAttendanceButtonsForIds(ids);
    })();

    // Edit employee function
    async function editEmployee(id) {
        try {
            const response = await fetch(`/bms/bakery-management-system/app/controllers/get_employee.php?id=${id}`);
            const employee = await response.json();
            
            if (employee) {
                modalTitle.textContent = 'Edit Employee';
                document.getElementById('employeeId').value = employee.id;
                document.getElementById('firstName').value = employee.first_name || '';
                document.getElementById('lastName').value = employee.last_name || '';
                document.getElementById('email').value = employee.email || '';
                document.getElementById('phone').value = employee.phone || '';
                document.getElementById('position').value = employee.position || '';
                document.getElementById('status').value = employee.status || 'Active';
                document.getElementById('salaryType').value = employee.salary_type || 'monthly';
                document.getElementById('salaryAmount').value = employee.salary_amount || '';
                
                employeeModal.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error fetching employee:', error);
            alert('Error loading employee data');
        }
    }

    // Show delete confirmation modal
    function showDeleteModal(id) {
        currentEmployeeId = id;
        deleteModal.classList.remove('hidden');
    }

    // Handle form submission
    employeeForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(employeeForm);
        const employeeId = document.getElementById('employeeId').value;
        const url = employeeId 
            ? '/bms/bakery-management-system/app/controllers/update_employee.php'
            : '/bms/bakery-management-system/app/controllers/add_employee.php';

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                employeeModal.classList.add('hidden');
                location.reload(); // Reload to show updated data
            } else {
                alert('Error: ' + (result.message || 'Something went wrong'));
            }
        } catch (error) {
            console.error('Error saving employee:', error);
            alert('Error saving employee data');
        }
    });

    // Handle delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', async () => {
        try {
            const response = await fetch('/bms/bakery-management-system/app/controllers/delete_employee.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: currentEmployeeId })
            });

            const result = await response.json();

            if (result.success) {
                deleteModal.classList.add('hidden');
                location.reload(); // Reload to show updated data
            } else {
                alert('Error: ' + (result.message || 'Something went wrong'));
            }
        } catch (error) {
            console.error('Error deleting employee:', error);
            alert('Error deleting employee');
        }
    });

    // Initial feather icons render
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>