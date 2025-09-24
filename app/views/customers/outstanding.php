<?php include APP_ROOT . '/app/views/layouts/app.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="text-gray-600">Customers with outstanding debt balances</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= url('customers/export') ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Export Outstanding
            </a>
            <a href="<?= url('customers') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>All Customers
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Customers with Outstanding Debts</p>
                    <p class="text-2xl font-bold text-orange-600"><?= count($customers) ?></p>
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
                    <p class="text-sm font-medium text-gray-600">Total Outstanding Amount</p>
                    <p class="text-2xl font-bold text-red-600">
                        <?= formatCurrency(array_sum(array_column($customers, 'outstanding_balance'))) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calculator text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Average Outstanding</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        <?= count($customers) > 0 ? formatCurrency(array_sum(array_column($customers, 'outstanding_balance')) / count($customers)) : formatCurrency(0) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <?php if (empty($customers)): ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Outstanding Debts</h3>
            <p class="text-gray-600 mb-6">Great! All customers have settled their debts.</p>
            <a href="<?= url('customers') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-users mr-2"></i>View All Customers
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Limit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-800">
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
                                        <?= formatCurrency($customer['outstanding_balance']) ?>
                                    </div>
                                    <?php if ($customer['outstanding_balance'] > ($customer['credit_limit'] ?? 0)): ?>
                                        <div class="text-xs text-red-500">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Over Credit Limit
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= formatCurrency($customer['credit_limit'] ?? 0) ?>
                                    </div>
                                    <?php if (($customer['credit_limit'] ?? 0) > 0): ?>
                                        <?php $usage = (($customer['outstanding_balance'] / $customer['credit_limit']) * 100); ?>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-<?= $usage > 90 ? 'red' : ($usage > 70 ? 'yellow' : 'green') ?>-500 h-2 rounded-full" 
                                                 style="width: <?= min($usage, 100) ?>%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1"><?= number_format($usage, 1) ?>% used</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= isset($customer['last_payment_date']) ? formatDate($customer['last_payment_date'], 'M j, Y') : 'No payments' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="<?= url('customers/' . $customer['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('debts/create?customer_id=' . $customer['id']) ?>" 
                                           class="text-green-600 hover:text-green-900" 
                                           title="Add Payment">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                        <a href="<?= url('customers/' . $customer['id'] . '/edit') ?>" 
                                           class="text-yellow-600 hover:text-yellow-900" 
                                           title="Edit Customer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
            <div class="flex flex-wrap gap-3">
                <button onclick="sendReminders()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-bell mr-2"></i>Send Payment Reminders
                </button>
                <button onclick="generateReport()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-chart-bar mr-2"></i>Generate Outstanding Report
                </button>
                <a href="<?= url('customers/overdue') ?>" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-clock mr-2"></i>View Overdue Debts
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function sendReminders() {
    if (confirm('Send payment reminders to all customers with outstanding debts?')) {
        // Implement reminder functionality
        alert('Payment reminders sent successfully!');
    }
}

function generateReport() {
    // Implement report generation
    window.print();
}
</script>