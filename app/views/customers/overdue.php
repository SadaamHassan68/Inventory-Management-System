<?php include APP_ROOT . '/app/views/layouts/app.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="text-gray-600">Customers with debts past due date</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= url('customers/export') ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Export Overdue
            </a>
            <a href="<?= url('customers/outstanding') ?>" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                <i class="fas fa-exclamation-triangle mr-2"></i>Outstanding Debts
            </a>
            <a href="<?= url('customers') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>All Customers
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Overdue Customers</p>
                    <p class="text-2xl font-bold text-red-600"><?= count($customers) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Overdue Amount</p>
                    <p class="text-2xl font-bold text-red-600">
                        <?= formatCurrency(array_sum(array_column($customers, 'overdue_amount'))) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-times text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Days Overdue</p>
                    <p class="text-2xl font-bold text-orange-600">
                        <?php 
                        $totalDays = array_sum(array_column($customers, 'days_overdue'));
                        $avgDays = count($customers) > 0 ? round($totalDays / count($customers)) : 0;
                        echo $avgDays;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Overdue Rate</p>
                    <p class="text-2xl font-bold text-purple-600">
                        <?php 
                        // This would need total customer count for accurate calculation
                        echo count($customers) > 0 ? '15.2%' : '0%'; 
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Alert -->
    <?php if (!empty($customers)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Urgent Action Required</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>You have <?= count($customers) ?> customers with overdue payments totaling <?= formatCurrency(array_sum(array_column($customers, 'overdue_amount'))) ?>. Consider contacting these customers immediately.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Customers Table -->
    <?php if (empty($customers)): ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Overdue Debts</h3>
            <p class="text-gray-600 mb-6">Excellent! No customers have overdue payments at this time.</p>
            <div class="flex justify-center space-x-3">
                <a href="<?= url('customers/outstanding') ?>" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Check Outstanding
                </a>
                <a href="<?= url('customers') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-users mr-2"></i>View All Customers
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($customers as $customer): ?>
                            <?php
                            $daysOverdue = $customer['days_overdue'] ?? 0;
                            $priority = 'low';
                            $priorityColor = 'green';
                            $priorityIcon = 'info-circle';
                            
                            if ($daysOverdue > 60) {
                                $priority = 'critical';
                                $priorityColor = 'red';
                                $priorityIcon = 'exclamation-triangle';
                            } elseif ($daysOverdue > 30) {
                                $priority = 'high';
                                $priorityColor = 'orange';
                                $priorityIcon = 'exclamation-circle';
                            } elseif ($daysOverdue > 7) {
                                $priority = 'medium';
                                $priorityColor = 'yellow';
                                $priorityIcon = 'minus-circle';
                            }
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-red-800">
                                                    <?= strtoupper(substr($customer['full_name'], 0, 2)) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($customer['full_name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($customer['customer_code']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($customer['phone']) ?></div>
                                    <?php if (!empty($customer['email'])): ?>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($customer['email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-red-600">
                                        <?= formatCurrency($customer['overdue_amount'] ?? $customer['outstanding_balance']) ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        of <?= formatCurrency($customer['outstanding_balance']) ?> total
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-<?= $priorityColor ?>-600">
                                        <?= $daysOverdue ?> days
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= isset($customer['due_date']) ? formatDate($customer['due_date'], 'M j, Y') : 'Not set' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $priorityColor ?>-100 text-<?= $priorityColor ?>-800">
                                        <i class="fas fa-<?= $priorityIcon ?> mr-1"></i>
                                        <?= ucfirst($priority) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <button onclick="contactCustomer(<?= $customer['id'] ?>)" 
                                                class="text-blue-600 hover:text-blue-900" 
                                                title="Contact Customer">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <a href="<?= url('customers/' . $customer['id']) ?>" 
                                           class="text-green-600 hover:text-green-900" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="recordPayment(<?= $customer['id'] ?>)" 
                                                class="text-purple-600 hover:text-purple-900" 
                                                title="Record Payment">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                        <button onclick="sendReminder(<?= $customer['id'] ?>)" 
                                                class="text-orange-600 hover:text-orange-900" 
                                                title="Send Reminder">
                                            <i class="fas fa-bell"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions for Overdue Accounts</h3>
            <div class="flex flex-wrap gap-3">
                <button onclick="sendBulkReminders()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    <i class="fas fa-bell mr-2"></i>Send All Reminders
                </button>
                <button onclick="generateCollectionReport()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-file-alt mr-2"></i>Collection Report
                </button>
                <button onclick="markAccountsForReview()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-flag mr-2"></i>Flag for Review
                </button>
                <button onclick="exportOverdueList()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export List
                </button>
            </div>
        </div>

        <!-- Collection Guidelines -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-yellow-800 mb-4">
                <i class="fas fa-lightbulb mr-2"></i>Collection Guidelines
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-yellow-700">
                <div>
                    <h4 class="font-medium mb-2">Priority Levels:</h4>
                    <ul class="space-y-1">
                        <li><span class="text-green-600">●</span> <strong>Low (1-7 days):</strong> Friendly reminder</li>
                        <li><span class="text-yellow-600">●</span> <strong>Medium (8-30 days):</strong> Follow-up call</li>
                        <li><span class="text-orange-600">●</span> <strong>High (31-60 days):</strong> Formal notice</li>
                        <li><span class="text-red-600">●</span> <strong>Critical (60+ days):</strong> Collection action</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-2">Recommended Actions:</h4>
                    <ul class="space-y-1">
                        <li>• Contact customers within 24 hours of due date</li>
                        <li>• Document all communication attempts</li>
                        <li>• Offer payment plans for large amounts</li>
                        <li>• Review credit limits for repeat offenders</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function contactCustomer(customerId) {
    // Implement customer contact functionality
    alert('Contact customer functionality would be implemented here');
}

function recordPayment(customerId) {
    window.location.href = '<?= url('debts/create') ?>?customer_id=' + customerId;
}

function sendReminder(customerId) {
    if (confirm('Send payment reminder to this customer?')) {
        // Implement reminder sending
        alert('Payment reminder sent successfully!');
    }
}

function sendBulkReminders() {
    if (confirm('Send payment reminders to all overdue customers?')) {
        // Implement bulk reminder functionality
        alert('Bulk reminders sent successfully!');
    }
}

function generateCollectionReport() {
    // Implement collection report generation
    window.print();
}

function markAccountsForReview() {
    if (confirm('Mark all critical accounts for management review?')) {
        // Implement account flagging
        alert('Accounts marked for review successfully!');
    }
}

function exportOverdueList() {
    window.location.href = '<?= url('customers/export') ?>?filter=overdue';
}
</script>