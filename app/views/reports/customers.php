<?php
$title = $title ?? 'Customer Report';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customer Report</h1>
            <p class="text-gray-600">Analyze customer performance and activity</p>
        </div>
        <a href="<?= url('reports') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Reports
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (hasFlash('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <?= getFlash('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?= getFlash('error') ?>
        </div>
    <?php endif; ?>

    <!-- Print Button -->
    <div class="flex justify-end">
        <button type="button" onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-print mr-2"></i>Print Report
        </button>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($summary['total_customers'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-check text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Active Customers</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($summary['customers_with_sales'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Average Spent</p>
                    <p class="text-2xl font-bold text-gray-900">$<?= number_format($summary['average_spent'] ?? 0, 2) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Outstanding Debt</p>
                    <p class="text-2xl font-bold text-gray-900">$<?= number_format($summary['total_outstanding'] ?? 0, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Customer Performance</h3>
        </div>
        
        <?php if (empty($customers)): ?>
            <div class="p-12 text-center">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-500">No customers found</p>
                <p class="text-gray-400">Add your first customer to get started</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding Debt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Sale</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">
                                                    <?= strtoupper(substr($customer['full_name'], 0, 2)) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="<?= url('customers/' . $customer['id']) ?>" class="text-blue-600 hover:text-blue-800">
                                                    <?= htmlspecialchars($customer['full_name']) ?>
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($customer['customer_code']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($customer['phone']): ?>
                                        <div><?= htmlspecialchars($customer['phone']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($customer['email']): ?>
                                        <div class="text-gray-500"><?= htmlspecialchars($customer['email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold"><?= number_format($customer['total_sales']) ?></div>
                                        <div class="text-xs text-gray-500">transactions</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    $<?= number_format($customer['total_spent'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($customer['outstanding_debt'] > 0): ?>
                                        <span class="text-red-600 font-medium">
                                            $<?= number_format($customer['outstanding_debt'], 2) ?>
                                        </span>
                                        <?php if ($customer['total_debts'] > 0): ?>
                                            <div class="text-xs text-gray-500"><?= $customer['total_debts'] ?> debt(s)</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-green-600">$0.00</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($customer['last_sale_date']): ?>
                                        <?= date('M j, Y', strtotime($customer['last_sale_date'])) ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="<?= url('customers/' . $customer['id']) ?>" 
                                       class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= url('sales/create?customer_id=' . $customer['id']) ?>" 
                                       class="text-green-600 hover:text-green-900" title="New Sale">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <?php if ($customer['outstanding_debt'] > 0): ?>
                                        <a href="<?= url('debts?customer_id=' . $customer['id']) ?>" 
                                           class="text-yellow-600 hover:text-yellow-900" title="View Debts">
                                            <i class="fas fa-credit-card"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Customer Performance Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Customers by Spending -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Top Customers by Spending</h3>
            </div>
            <div class="p-6">
                <?php 
                $topCustomers = array_slice(array_filter($customers, function($c) { return $c['total_spent'] > 0; }), 0, 5);
                usort($topCustomers, function($a, $b) { return $b['total_spent'] - $a['total_spent']; });
                ?>
                <?php if (empty($topCustomers)): ?>
                    <p class="text-gray-500 text-center py-4">No customer spending data available</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($topCustomers as $index => $customer): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-medium">
                                        <?= $index + 1 ?>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($customer['full_name']) ?></p>
                                        <p class="text-xs text-gray-500"><?= $customer['total_sales'] ?> sales</p>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    $<?= number_format($customer['total_spent'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Customers with Outstanding Debts -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Customers with Outstanding Debts</h3>
            </div>
            <div class="p-6">
                <?php 
                $debtCustomers = array_filter($customers, function($c) { return $c['outstanding_debt'] > 0; });
                usort($debtCustomers, function($a, $b) { return $b['outstanding_debt'] - $a['outstanding_debt']; });
                $debtCustomers = array_slice($debtCustomers, 0, 5);
                ?>
                <?php if (empty($debtCustomers)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                        <p class="text-green-500 font-medium">No outstanding debts!</p>
                        <p class="text-sm text-gray-500">All customers have cleared their debts</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($debtCustomers as $customer): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center">
                                        <i class="fas fa-exclamation text-xs"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($customer['full_name']) ?></p>
                                        <p class="text-xs text-gray-500"><?= $customer['total_debts'] ?> debt(s)</p>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-red-600">
                                    $<?= number_format($customer['outstanding_debt'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="<?= url('debts') ?>" class="text-sm text-blue-600 hover:text-blue-800">
                            View all debts →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>