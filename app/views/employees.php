<!-- Employee Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="award" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $avg_rating ?></h3>
        <p class="text-gray-400">Avg. Rating</p>
    </div>
</div>

<!-- Employee Table -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">Employee List</h3>
        <div class="relative">
            <input type="text" id="employeeSearch" placeholder="Search employees..." class="bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500 w-64">
            <i data-feather="search" class="absolute right-3 top-2.5 text-gray-400"></i>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 px-4 text-lime-400">Employee</th>
                    <th class="text-left py-3 px-4 text-lime-400">Position</th>
                    <th class="text-left py-3 px-4 text-lime-400">Status</th>
                    <th class="text-left py-3 px-4 text-lime-400">Join Date</th>
                    <th class="text-left py-3 px-4 text-lime-400">Actions</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
                <?php foreach ($employees as $emp): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-700">
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
                    <td class="py-3 px-4"><?= htmlspecialchars($emp['created_at']) ?></td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <button class="text-lime-400 hover:text-lime-300"><i data-feather="edit" class="w-4 h-4"></i></button>
                            <button class="text-fuchsia-400 hover:text-fuchsia-300"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- <script>
    document.addEventListener("DOMContentLoaded", () => {
        const searchInput = document.getElementById("employeeSearch");
        const tableBody = document.getElementById("employeeTableBody");
        let searchTimeout;

        // Function to perform search
        const performSearch = async (searchValue) => {
            try {
                const response = await fetch(`app/controllers/search_employees.php?search=${encodeURIComponent(searchValue)}`);
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();

                // Clear existing table rows
                tableBody.innerHTML = "";

                if (data.length === 0) {
                    tableBody.innerHTML = `
                        <tr><td colspan="5" class="text-center py-4 text-gray-400">No employees found.</td></tr>
                    `;
                    return;
                }

                // Populate table with search results
                data.forEach(emp => {
                    const fullName = `${emp.first_name || ''} ${emp.last_name || ''}`.trim();
                    const photo = emp.profile_pic && emp.profile_pic !== "" ? emp.profile_pic : "https://static.photos/people/200x200/1";
                    const statusColor = emp.status === "Active" ? "green" : (emp.status === "On Leave" ? "yellow" : "red");
                    const joinDate = emp.created_at ? emp.created_at.split(' ')[0] : '—';

                    const row = `
                        <tr class="border-b border-gray-700 hover:bg-gray-700">
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
                            <td class="py-3 px-4">${joinDate}</td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <button class="text-lime-400 hover:text-lime-300"><i data-feather="edit" class="w-4 h-4"></i></button>
                                    <button class="text-fuchsia-400 hover:text-fuchsia-300"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML("beforeend", row);
                });

                // Re-render Feather icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            } catch (error) {
                console.error('Error searching employees:', error);
                tableBody.innerHTML = `
                    <tr><td colspan="5" class="text-center py-4 text-red-400">Error loading employees. Please try again.</td></tr>
                `;
            }
        };

        // Search input event listener with debouncing
        searchInput.addEventListener("input", (e) => {
            const searchValue = e.target.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Set new timeout for debouncing (300ms delay)
            searchTimeout = setTimeout(() => {
                performSearch(searchValue);
            }, 300);
        });

        // Initial feather icons render
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script> -->

<!-- <script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("employeeSearch");
    const tableBody = document.getElementById("employeeTableBody");
    let searchTimeout;

    // Store original employees HTML for quick reset
    const originalTableHTML = tableBody.innerHTML;

    // Function to perform search
    const performSearch = async (searchValue) => {
        try {
            // If search is empty, restore original table immediately
            if (!searchValue) {
                tableBody.innerHTML = originalTableHTML;
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
                return;
            }

            const response = await fetch(`app/controllers/search_employees.php?search=${encodeURIComponent(searchValue)}`);
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();

            // Clear existing table rows
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = `
                    <tr><td colspan="5" class="text-center py-4 text-gray-400">No employees found.</td></tr>
                `;
                return;
            }

            // Populate table with search results
            data.forEach(emp => {
                const fullName = `${emp.first_name || ''} ${emp.last_name || ''}`.trim();
                const photo = emp.profile_pic && emp.profile_pic !== "" ? emp.profile_pic : "https://static.photos/people/200x200/1";
                const statusColor = emp.status === "Active" ? "green" : (emp.status === "On Leave" ? "yellow" : "red");
                const joinDate = emp.created_at ? emp.created_at.split(' ')[0] : '—';

                const row = `
                    <tr class="border-b border-gray-700 hover:bg-gray-700">
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
                        <td class="py-3 px-4">${joinDate}</td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <button class="text-lime-400 hover:text-lime-300"><i data-feather="edit" class="w-4 h-4"></i></button>
                                <button class="text-fuchsia-400 hover:text-fuchsia-300"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML("beforeend", row);
            });

            // Re-render Feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        } catch (error) {
            console.error('Error searching employees:', error);
            tableBody.innerHTML = `
                <tr><td colspan="5" class="text-center py-4 text-red-400">Error loading employees. Please try again.</td></tr>
            `;
        }
    };

    // Search input event listener with debouncing
    searchInput.addEventListener("input", (e) => {
        const searchValue = e.target.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // If search is empty, restore immediately without delay
        if (!searchValue) {
            tableBody.innerHTML = originalTableHTML;
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            return;
        }
        
        // Set new timeout for debouncing (300ms delay) only for non-empty searches
        searchTimeout = setTimeout(() => {
            performSearch(searchValue);
        }, 300);
    });

    // Also handle when user clears using backspace/delete quickly
    searchInput.addEventListener("keydown", (e) => {
        if (e.key === 'Backspace' || e.key === 'Delete') {
            const currentValue = searchInput.value;
            // If pressing backspace/delete will make the field empty
            if (currentValue.length === 1) {
                clearTimeout(searchTimeout);
                // Small delay to ensure the input is cleared first
                setTimeout(() => {
                    tableBody.innerHTML = originalTableHTML;
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                }, 10);
            }
        }
    });

    // Initial feather icons render
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script> -->

<!-- <script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("employeeSearch");
    const tableBody = document.getElementById("employeeTableBody");
    const originalTableHTML = tableBody.innerHTML;
    let searchTimeout;

    searchInput.addEventListener("input", (e) => {
        const searchValue = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        // Immediate reset for empty search
        if (searchValue === '') {
            tableBody.innerHTML = originalTableHTML;
            feather.replace();
            return;
        }
        
        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`app/controllers/search_employees.php?search=${encodeURIComponent(searchValue)}`);
                const data = await response.json();
                
                tableBody.innerHTML = "";
                
                if (data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-400">No employees found.</td></tr>`;
                    return;
                }
                
                data.forEach(emp => {
                    const fullName = `${emp.first_name || ''} ${emp.last_name || ''}`.trim();
                    const photo = emp.profile_pic || "https://static.photos/people/200x200/1";
                    const statusColor = emp.status === "Active" ? "green" : (emp.status === "On Leave" ? "yellow" : "red");
                    const joinDate = emp.created_at ? emp.created_at.split(' ')[0] : '—';
                    
                    const row = `
                        <tr class="border-b border-gray-700 hover:bg-gray-700">
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
                            <td class="py-3 px-4">${joinDate}</td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <button class="text-lime-400 hover:text-lime-300"><i data-feather="edit" class="w-4 h-4"></i></button>
                                    <button class="text-fuchsia-400 hover:text-fuchsia-300"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML("beforeend", row);
                });
                
                feather.replace();
            } catch (error) {
                console.error('Search error:', error);
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-400">Search failed. Please try again.</td></tr>`;
            }
        }, 300);
    });
});
</script> -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        console.log("Script loaded - DOM ready");
        
        const searchInput = document.getElementById("employeeSearch");
        const tableBody = document.getElementById("employeeTableBody");
        const originalTableHTML = tableBody.innerHTML;

        let searchTimeout;

        searchInput.addEventListener("input", (e) => {
            const searchValue = e.target.value.trim();
            console.log("Search input:", searchValue);
            
            clearTimeout(searchTimeout);
            
            // Immediate reset for empty search
            if (searchValue === '') {
                console.log("Empty search - restoring original");
                tableBody.innerHTML = originalTableHTML;
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
                return;
            }
            
            searchTimeout = setTimeout(async () => {
                console.log("Making AJAX request for:", searchValue);
                try {
                    // USE ABSOLUTE PATH based on your URL structure
                    const response = await fetch(`/bms/bakery-management-system-1/app/controllers/search_employees.php?search=${encodeURIComponent(searchValue)}`);
                    console.log("Response status:", response.status);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    console.log("Received data:", data);
                    
                    tableBody.innerHTML = "";
                    
                    if (data.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-400">No employees found.</td></tr>`;
                        return;
                    }
                    
                    data.forEach(emp => {
                        const fullName = `${emp.first_name || ''} ${emp.last_name || ''}`.trim();
                        const photo = emp.profile_pic || "https://static.photos/people/200x200/1";
                        const statusColor = emp.status === "Active" ? "green" : (emp.status === "On Leave" ? "yellow" : "red");
                        const joinDate = emp.created_at ? emp.created_at.split(' ')[0] : '—';
                        
                        const row = `
                            <tr class="border-b border-gray-700 hover:bg-gray-700">
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
                                <td class="py-3 px-4">${joinDate}</td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button class="text-lime-400 hover:text-lime-300"><i data-feather="edit" class="w-4 h-4"></i></button>
                                        <button class="text-fuchsia-400 hover:text-fuchsia-300"><i data-feather="trash-2" class="w-4 h-4"></i></button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML("beforeend", row);
                    });
                    
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-400">Search failed: ${error.message}</td></tr>`;
                }
            }, 300);
        });
    });
</script>