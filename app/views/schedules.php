<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="calendar" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $total_schedules ?></h3>
        <p class="text-gray-400">Total Schedules</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="clock" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= $upcoming_schedules ?></h3>
        <p class="text-gray-400">Upcoming</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="play" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $in_progress_schedules ?></h3>
        <p class="text-gray-400">In Progress</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="check" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= $completed_schedules ?></h3>
        <p class="text-gray-400">Completed</p>
    </div>
</div>

<!-- Alerts -->
<?php if ($message): ?>
    <div class="mb-6 p-4 bg-green-900 border border-green-700 rounded-lg text-green-300">
        <?= $message ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="mb-6 p-4 bg-red-900 border border-red-700 rounded-lg text-red-300">
        <?= $error ?>
    </div>
<?php endif; ?>

<!-- Schedule Controls -->
<div class="bg-gray-800 rounded-xl p-6 mb-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
        <div class="flex items-center space-x-4">
            <a href="<?= $prev_link ?>" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors">
                <i data-feather="chevron-left" class="w-4 h-4"></i>
            </a>
            <h3 class="text-xl font-bold text-lime-400"><?= $display_text ?></h3>
            <a href="<?= $next_link ?>" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors">
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="flex space-x-2">
            <!-- New Shift Button -->
            <button onclick="openNewShiftModal()" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                <span>New Shift</span>
            </button>
            
            <!-- View Type Buttons -->
            <a href="?page=schedules&view=week&date=<?= $current_date ?>" class="<?= $view_type == 'week' ? 'bg-lime-500 text-white' : 'bg-gray-700 hover:bg-gray-600' ?> px-4 py-2 rounded-lg transition-colors">
                Week
            </a>
            <a href="?page=schedules&view=month&date=<?= $current_date ?>" class="<?= $view_type == 'month' ? 'bg-lime-500 text-white' : 'bg-gray-700 hover:bg-gray-600' ?> px-4 py-2 rounded-lg transition-colors">
                Month
            </a>
            <a href="?page=schedules&view=day&date=<?= $current_date ?>" class="<?= $view_type == 'day' ? 'bg-lime-500 text-white' : 'bg-gray-700 hover:bg-gray-600' ?> px-4 py-2 rounded-lg transition-colors">
                Day
            </a>
        </div>
    </div>
</div>

<!-- Schedule Grid -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <?php if ($view_type == 'week'): ?>
        <!-- Week View -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-3 px-4 text-lime-400">Employee</th>
                        <?php foreach ($week_days as $day): ?>
                            <th class="text-center py-3 px-4 text-lime-400">
                                <?= $day['day_name'] ?> <?= $day['day_number'] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr class="border-b border-gray-700 hover:bg-gray-750 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <?php if ($employee['profile_pic']): ?>
                                        <img src="<?= htmlspecialchars($employee['profile_pic']) ?>" class="w-8 h-8 rounded-full" alt="<?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?>">
                                    <?php else: ?>
                                        <div class="w-8 h-8 bg-lime-500 rounded-full flex items-center justify-center">
                                            <i data-feather="user" class="w-4 h-4 text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <span class="font-medium"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></span>
                                        <p class="text-gray-400 text-xs"><?= htmlspecialchars($employee['position']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <?php foreach ($week_days as $day): ?>
                                <td class="py-3 px-4 text-center">
                                    <?php
                                    $shift = $schedule_data[$employee['id']][$day['date']] ?? null;
                                    if ($shift) {
                                        $shift_color = $shift['shift_type'] == 'afternoon' ? 'bg-fuchsia-500' : 'bg-lime-500';
                                        $shift_time = $shift['start_time'] . '-' . $shift['end_time'];
                                        $shift_status = $shift['status'] ?? 'scheduled';
                                        
                                        echo "<div class='relative group'>";
                                        echo "<div class='{$shift_color} text-white px-2 py-1 rounded text-sm cursor-pointer hover:opacity-90' onclick='editShift(\"{$employee['id']}\", \"{$day['date']}\")'>";
                                        echo $shift_time;
                                        
                                        // Add status icons
                                        if ($shift_status === 'in_progress') {
                                            echo "<div class='absolute -top-1 -right-1 bg-yellow-500 rounded-full p-1'>";
                                            echo "<i data-feather='play' class='w-3 h-3 text-white'></i>";
                                            echo "</div>";
                                        } elseif ($shift_status === 'completed') {
                                            echo "<div class='absolute -top-1 -right-1 bg-green-500 rounded-full p-1'>";
                                            echo "<i data-feather='check' class='w-3 h-3 text-white'></i>";
                                            echo "</div>";
                                        }
                                        
                                        echo "</div>";
                                        echo "</div>";
                                    } else {
                                        echo "<span class='text-gray-500 cursor-pointer hover:text-gray-300' onclick='openNewShiftModal(\"{$employee['id']}\", \"{$day['date']}\")'>OFF</span>";
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- List View for Other Views -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-3 px-4 text-lime-400">Schedule</th>
                        <th class="text-left py-3 px-4 text-lime-400">Type</th>
                        <th class="text-left py-3 px-4 text-lime-400">Time</th>
                        <th class="text-left py-3 px-4 text-lime-400">Assigned To</th>
                        <th class="text-left py-3 px-4 text-lime-400">Status</th>
                        <th class="text-left py-3 px-4 text-lime-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($schedules as $schedule): 
                        $statusColor = [
                            'scheduled' => 'bg-blue-500',
                            'in_progress' => 'bg-yellow-500',
                            'completed' => 'bg-green-500',
                            'cancelled' => 'bg-red-500'
                        ][$schedule['status']] ?? 'bg-gray-500';
                        
                        $typeColor = [
                            'shift' => 'text-blue-400',
                            'production' => 'text-green-400',
                            'maintenance' => 'text-yellow-400',
                            'delivery' => 'text-purple-400'
                        ][$schedule['schedule_type']] ?? 'text-gray-400';
                    ?>
                    <tr class="border-b border-gray-700 hover:bg-gray-700">
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                                    <i data-feather="calendar" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($schedule['title']) ?></p>
                                    <p class="text-gray-400 text-sm"><?= htmlspecialchars($schedule['description']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="<?= $typeColor ?> font-semibold capitalize">
                                <?= $schedule['schedule_type'] ?>
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="text-gray-300">
                                <div class="font-medium text-sm"><?= date('M j, Y', strtotime($schedule['start_time'])) ?></div>
                                <div class="text-gray-400 text-xs">
                                    <?= date('g:i A', strtotime($schedule['start_time'])) ?> - 
                                    <?= date('g:i A', strtotime($schedule['end_time'])) ?>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <?php if($schedule['assigned_to']): ?>
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-fuchsia-500 rounded-full flex items-center justify-center">
                                    <i data-feather="user" class="w-3 h-3 text-white"></i>
                                </div>
                                <span class="text-gray-300">
                                    <?= htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']) ?>
                                </span>
                            </div>
                            <?php else: ?>
                            <span class="text-gray-500 text-sm">Not assigned</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <span class="<?= $statusColor ?> text-white px-2 py-1 rounded-full text-xs capitalize">
                                <?= str_replace('_', ' ', $schedule['status']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <?php if($schedule['status'] === 'scheduled'): ?>
                                <button onclick="updateStatus(<?= $schedule['id'] ?>, 'in_progress')" 
                                        class="text-yellow-400 hover:text-yellow-300" title="Start">
                                    <i data-feather="play" class="w-4 h-4"></i>
                                </button>
                                <?php elseif($schedule['status'] === 'in_progress'): ?>
                                <button onclick="updateStatus(<?= $schedule['id'] ?>, 'completed')" 
                                        class="text-green-400 hover:text-green-300" title="Complete">
                                    <i data-feather="check" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
                                
                                <button onclick="editSchedule(<?= $schedule['id'] ?>)" 
                                        class="text-lime-400 hover:text-lime-300" title="Edit">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </button>
                                
                                <button onclick="deleteSchedule(<?= $schedule['id'] ?>)" 
                                        class="text-fuchsia-400 hover:text-fuchsia-300" title="Delete">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($schedules)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i data-feather="calendar" class="w-16 h-16 mb-4"></i>
                                <h4 class="text-lg font-semibold mb-2">No Schedules Found</h4>
                                <p>Create your first schedule to get started.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Schedule Summary (Only for Week View) -->
<?php if ($view_type == 'week'): ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
    <!-- Shift Coverage -->
    <div class="bg-gray-800 rounded-xl p-6">
        <h4 class="text-lg font-bold text-lime-400 mb-4">Shift Coverage</h4>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span>Morning Shift (6AM-2PM)</span>
                <span class="bg-lime-500 text-white px-2 py-1 rounded text-sm">
                    <?= $shift_coverage['morning']['assigned'] ?>/<?= $shift_coverage['morning']['total'] ?>
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span>Afternoon Shift (2PM-10PM)</span>
                <span class="bg-fuchsia-500 text-white px-2 py-1 rounded text-sm">
                    <?= $shift_coverage['afternoon']['assigned'] ?>/<?= $shift_coverage['afternoon']['total'] ?>
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span>Weekend Coverage</span>
                <span class="bg-lime-500 text-white px-2 py-1 rounded text-sm">
                    <?= $shift_coverage['weekend']['assigned'] ?>/<?= $shift_coverage['weekend']['total'] ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Time Off Requests -->
    <div class="bg-gray-800 rounded-xl p-6">
        <h4 class="text-lg font-bold text-fuchsia-400 mb-4">Time Off Requests</h4>
        <div class="space-y-3">
            <?php if (!empty($time_off_requests)): ?>
                <?php foreach ($time_off_requests as $request): ?>
                    <div class="flex justify-between items-center p-2 bg-gray-700 rounded">
                        <div>
                            <p class="font-medium">
                                <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?>
                            </p>
                            <p class="text-gray-400 text-sm">
                                <?= date('M j', strtotime($request['start_date'])) ?>
                                <?php if ($request['start_date'] != $request['end_date']): ?>
                                    - <?= date('j', strtotime($request['end_date'])) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php
                        $status_color = $request['status'] == 'approved' ? 'bg-green-500' : 'bg-yellow-500';
                        $status_text = ucfirst($request['status']);
                        ?>
                        <span class="<?= $status_color ?> text-white px-2 py-1 rounded text-sm">
                            <?= $status_text ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">No time off requests this week.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gray-800 rounded-xl p-6">
        <h4 class="text-lg font-bold text-lime-400 mb-4">Quick Actions</h4>
        <div class="space-y-2">
            <button class="w-full bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg text-left flex items-center space-x-2 transition-colors">
                <i data-feather="rotate-cw" class="w-4 h-4"></i>
                <span>Copy Last Week</span>
            </button>
            <button class="w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-left flex items-center space-x-2 transition-colors">
                <i data-feather="download" class="w-4 h-4"></i>
                <span>Export Schedule</span>
            </button>
            <button class="w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-left flex items-center space-x-2 transition-colors">
                <i data-feather="send" class="w-4 h-4"></i>
                <span>Notify Team</span>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- New Shift Modal -->
<div id="newShiftModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-lime-400">Create New Shift</h3>
            <button onclick="closeNewShiftModal()" class="text-gray-400 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form method="POST" id="shiftForm">
            <input type="hidden" name="create_shift" value="1">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Employee</label>
                    <select name="employee_id" required class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500" id="modalEmployee">
                        <option value="">Select Employee</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?= $employee['id'] ?>">
                                <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Date</label>
                    <input type="date" name="shift_date" required class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500" id="modalDate">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Shift Type</label>
                    <select name="shift_type" required class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500" id="shiftType">
                        <option value="morning">Morning (6AM-2PM)</option>
                        <option value="afternoon">Afternoon (2PM-10PM)</option>
                        <option value="custom">Custom Hours</option>
                    </select>
                </div>
                
                <div id="customHours" class="hidden">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">Start Time</label>
                            <input type="time" name="start_time" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        </div>
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">End Time</label>
                            <input type="time" name="end_time" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">Description</label>
                    <textarea name="description" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500" placeholder="Shift description or notes..." rows="3"></textarea>
                </div>
            </div>
            
            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closeNewShiftModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Save Shift
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functions
function openNewShiftModal(employeeId = '', date = '') {
    const modal = document.getElementById('newShiftModal');
    if (employeeId) document.getElementById('modalEmployee').value = employeeId;
    if (date) document.getElementById('modalDate').value = date;
    modal.classList.remove('hidden');
}

function closeNewShiftModal() {
    document.getElementById('newShiftModal').classList.add('hidden');
}

function editShift(employeeId, date) {
    openNewShiftModal(employeeId, date);
    // Here you would pre-fill the form with existing shift data
    // For now, it opens the same modal for editing
}

function editSchedule(scheduleId) {
    // Open edit modal for specific schedule
    alert('Edit schedule ' + scheduleId + ' - This would open an edit modal with pre-filled data');
    // You would implement this to fetch schedule data and populate the form
}

function updateStatus(scheduleId, status) {
    if (confirm('Are you sure you want to update this schedule status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="schedule_id" value="${scheduleId}">
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSchedule(scheduleId) {
    if (confirm('Are you sure you want to delete this schedule?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_schedule">
            <input type="hidden" name="schedule_id" value="${scheduleId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Show/hide custom hours based on shift type
document.getElementById('shiftType').addEventListener('change', function() {
    const customHours = document.getElementById('customHours');
    if (this.value === 'custom') {
        customHours.classList.remove('hidden');
    } else {
        customHours.classList.add('hidden');
    }
});

// Close modal when clicking outside
document.getElementById('newShiftModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNewShiftModal();
    }
});

// Feather icons
document.addEventListener("DOMContentLoaded", function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>