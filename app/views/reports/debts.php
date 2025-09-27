<?php
$title = $title ?? 'Debt Report';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Debt Report</h1>
            <p class="text-gray-600">Monitor outstanding debts and payment status</p>
        </div>
        <a href="<?= url('reports') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Reports
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Options</h3>
        <form method="GET" action="<?= url('reports/debts') ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="partial" <?= $filters['status'] === 'partial' ? 'selected' : '' ?>>Partial</option>
                        <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                    </select>
                </div>
                
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                    <select id="customer_id" name="customer_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Customers</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>" 
                                    <?= ($filters['customer_id'] == $customer['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($customer['customer_code'] . ' - ' . $customer['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="overdue_only" class="block text-sm font-medium text-gray-700">Overdue Status</label>
                    <select id="overdue_only" name="overdue_only" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Debts</option>
                        <option value="1" <?= $filters['overdue_only'] === '1' ? 'selected' : '' ?>>Overdue Only</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-between items-center">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <button type="button" onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-credit-card text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Debts</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($summary['total_debts'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900">$<?= number_format($summary['total_debt_amount'] ?? 0, 2) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Outstanding</p>
                    <p class="text-2xl font-bold text-gray-900">$<?= number_format($summary['outstanding_amount'] ?? 0, 2) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Overdue Amount</p>
                    <p class="text-2xl font-bold text-gray-900">$<?= number_format($summary['overdue_amount'] ?? 0, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Debts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Debt Details</h3>
        </div>
        
        <?php if (empty($debts)): ?>
            <div class="p-12 text-center">
                <i class="fas fa-credit-card text-4xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-500">No debts found</p>
                <p class="text-gray-400">Try adjusting your filter criteria</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debt #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($debts as $debt): ?>
                            <tr class="hover:bg-gray-50 <?= $debt['is_overdue'] ? 'bg-red-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="<?= url('debts/' . $debt['id']) ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <?= htmlspecialchars($debt['debt_number']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="<?= url('customers/' . $debt['customer_id']) ?>" class="text-blue-600 hover:text-blue-800">
                                        <?= htmlspecialchars($debt['customer_name']) ?>
                                    </a>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($debt['customer_code']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    $<?= number_format($debt['original_amount'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    $<?= number_format($debt['original_amount'] - $debt['remaining_amount'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    $<?= number_format($debt['remaining_amount'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="<?= $debt['is_overdue'] ? 'text-red-600 font-medium' : '' ?>">
                                        <?= date('M j, Y', strtotime($debt['due_date'])) ?>
                                        <?php if ($debt['is_overdue']): ?>
                                            <div class="text-xs text-red-500"><?= $debt['days_overdue'] ?> days overdue</div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                 <?php
                                                 if ($debt['is_overdue']) {
                                                     echo 'bg-red-100 text-red-800';
                                                 } else {
                                                     switch($debt['status']) {
                                                         case 'paid': echo 'bg-green-100 text-green-800'; break;
                                                         case 'partial': echo 'bg-yellow-100 text-yellow-800'; break;
                                                         case 'pending': echo 'bg-blue-100 text-blue-800'; break;
                                                         default: echo 'bg-gray-100 text-gray-800';
                                                     }
                                                 }
                                                 ?>">
                                        <?= $debt['is_overdue'] ? 'Overdue' : ucfirst($debt['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>