<?php
$title = $title ?? 'Reports Overview';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports Overview</h1>
            <p class="text-gray-600">View comprehensive reports and analytics</p>
        </div>
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

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Sales Report Card -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Sales Report</h3>
                        <p class="text-sm text-gray-500">Revenue and sales analytics</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Total Sales</p>
                            <p class="font-semibold"><?= number_format($sales_stats['total_sales'] ?? 0) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Revenue</p>
                            <p class="font-semibold">$<?= number_format($sales_stats['total_revenue'] ?? 0, 2) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= url('reports/sales') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-chart-line mr-2"></i>View Sales Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Report Card -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-boxes text-white"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Inventory Report</h3>
                        <p class="text-sm text-gray-500">Stock levels and valuation</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Total Products</p>
                            <p class="font-semibold"><?= number_format($inventory_stats['total_products'] ?? 0) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Inventory Value</p>
                            <p class="font-semibold">$<?= number_format($inventory_stats['total_value'] ?? 0, 2) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= url('reports/inventory') ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-boxes mr-2"></i>View Inventory Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debt Report Card -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-credit-card text-white"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Debt Report</h3>
                        <p class="text-sm text-gray-500">Outstanding debts and payments</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Total Debts</p>
                            <p class="font-semibold"><?= number_format($debt_stats['total_debts'] ?? 0) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Outstanding</p>
                            <p class="font-semibold">$<?= number_format($debt_stats['outstanding_amount'] ?? 0, 2) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= url('reports/debts') ?>" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-credit-card mr-2"></i>View Debt Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Report Card -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Customer Report</h3>
                        <p class="text-sm text-gray-500">Customer analytics and insights</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Total Customers</p>
                            <p class="font-semibold"><?= number_format($customer_stats['total_customers'] ?? 0) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Active Customers</p>
                            <p class="font-semibold"><?= number_format($customer_stats['active_customers'] ?? 0) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="<?= url('reports/customers') ?>" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-users mr-2"></i>View Customer Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Statistics</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600"><?= number_format($sales_stats['today_sales'] ?? 0) ?></div>
                    <div class="text-sm text-gray-500">Sales Today</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">$<?= number_format($sales_stats['today_revenue'] ?? 0, 2) ?></div>
                    <div class="text-sm text-gray-500">Revenue Today</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600"><?= number_format($inventory_stats['low_stock_count'] ?? 0) ?></div>
                    <div class="text-sm text-gray-500">Low Stock Items</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?= number_format($debt_stats['overdue_count'] ?? 0) ?></div>
                    <div class="text-sm text-gray-500">Overdue Debts</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>