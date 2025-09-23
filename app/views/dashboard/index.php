<?php
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Welcome back, <?= auth()['full_name'] ?>!</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="refreshDashboard()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-sync-alt mr-2"></i>Refresh
            </button>
            <?php if (isAdmin()): ?>
                <a href="<?= url('dashboard/export?type=summary') ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900"><?= formatNumber($stats['total_products']) ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= url('products') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all →</a>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Low Stock</p>
                    <p class="text-2xl font-bold text-orange-600"><?= formatNumber($stats['low_stock_products']) ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= url('products/low-stock') ?>" class="text-sm text-orange-600 hover:text-orange-800">View details →</a>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-900"><?= formatNumber($stats['total_customers']) ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= url('customers') ?>" class="text-sm text-green-600 hover:text-green-800">View all →</a>
            </div>
        </div>

        <!-- Outstanding Debts -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-credit-card text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Outstanding Debts</p>
                    <p class="text-2xl font-bold text-red-600"><?= formatCurrency($stats['total_outstanding_debts']) ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= url('debts') ?>" class="text-sm text-red-600 hover:text-red-800">View all →</a>
            </div>
        </div>
    </div>

    <!-- Sales Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Sales -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Today's Sales</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Number of Sales</p>
                        <p class="text-3xl font-bold text-blue-600"><?= formatNumber($stats['today_sales_count']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Amount</p>
                        <p class="text-3xl font-bold text-green-600"><?= formatCurrency($stats['today_sales_amount']) ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= url('sales/create') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>New Sale
                    </a>
                </div>
            </div>
        </div>

        <!-- This Month's Sales -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">This Month's Sales</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Number of Sales</p>
                        <p class="text-3xl font-bold text-blue-600"><?= formatNumber($stats['month_sales_count']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Amount</p>
                        <p class="text-3xl font-bold text-green-600"><?= formatCurrency($stats['month_sales_amount']) ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= url('reports/sales') ?>" class="text-sm text-blue-600 hover:text-blue-800">View detailed report →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Monthly Sales Trend</h3>
            </div>
            <div class="p-6">
                <canvas id="salesChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Low Stock Alert</h3>
                <a href="<?= url('products/low-stock') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
            </div>
            <div class="p-6">
                <?php if (empty($lowStockProducts)): ?>
                    <p class="text-gray-500 text-center py-4">No low stock products</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($lowStockProducts as $product): ?>
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?></p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($product['sku']) ?> • <?= htmlspecialchars($product['category_name'] ?? 'No Category') ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-orange-600"><?= formatNumber($product['quantity_in_stock']) ?> left</p>
                                    <p class="text-xs text-gray-500">Min: <?= formatNumber($product['min_stock_level']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Overdue Debts and Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Overdue Debts -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Overdue Debts</h3>
                <a href="<?= url('debts/overdue') ?>" class="text-sm text-red-600 hover:text-red-800">View all</a>
            </div>
            <div class="p-6">
                <?php if (empty($overdueDebts)): ?>
                    <p class="text-gray-500 text-center py-4">No overdue debts</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($overdueDebts as $debt): ?>
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($debt['customer_name']) ?></p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($debt['debt_number']) ?> • <?= htmlspecialchars($debt['customer_phone']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-red-600"><?= formatCurrency($debt['remaining_amount']) ?></p>
                                    <p class="text-xs text-gray-500"><?= $debt['days_overdue'] ?> days overdue</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            </div>
            <div class="p-6">
                <?php if (empty($recentActivities)): ?>
                    <p class="text-gray-500 text-center py-4">No recent activities</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">
                                        <span class="font-medium"><?= htmlspecialchars($activity['full_name'] ?? $activity['username'] ?? 'Unknown') ?></span>
                                        <?= htmlspecialchars($activity['action']) ?>
                                        <?php if ($activity['table_name']): ?>
                                            in <?= htmlspecialchars($activity['table_name']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?= formatDate($activity['created_at'], 'M d, Y H:i') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for sales chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = <?= json_encode($monthlySales) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(item => item.month),
            datasets: [{
                label: 'Sales Amount',
                data: salesData.map(item => item.total_amount),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Sales: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Refresh dashboard function
    function refreshDashboard() {
        location.reload();
    }

    // Auto-refresh every 5 minutes
    setInterval(refreshDashboard, 300000);
</script>

<?php
$content = ob_get_clean();
$title = 'Dashboard - Inventory Management System';
include __DIR__ . '/../layouts/app.php';
?>