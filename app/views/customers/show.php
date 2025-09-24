<?php
$title = $title ?? 'Customer Details';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($customer['full_name']) ?></h1>
            <p class="text-gray-600">Customer details and transaction history</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= url('customers') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back to Customers
            </a>
            <?php if (isAdmin()): ?>
                <a href="<?= url('customers/' . $customer['id'] . '/edit') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit Customer
                </a>
            <?php endif; ?>
            <a href="<?= url('debts/create?customer_id=' . $customer['id']) ?>" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                <i class="fas fa-plus mr-2"></i>Record Debt
            </a>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Customer Avatar -->
                <div class="text-center mb-6">
                    <div class="mx-auto h-24 w-24 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        <?= strtoupper(substr($customer['full_name'], 0, 2)) ?>
                    </div>
                    <h3 class="mt-4 text-xl font-medium text-gray-900"><?= htmlspecialchars($customer['full_name']) ?></h3>
                    <p class="text-gray-500"><?= htmlspecialchars($customer['customer_code']) ?></p>
                    
                    <!-- Status Badge -->
                    <div class="mt-2">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                     <?= ($customer['is_active'] ?? 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <i class="fas <?= ($customer['is_active'] ?? 1) ? 'fa-check-circle' : 'fa-times-circle' ?> mr-1"></i>
                            <?= ($customer['is_active'] ?? 1) ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Contact Information</h4>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-phone text-gray-400 w-4 mr-2"></i>
                                <span><?= htmlspecialchars($customer['phone']) ?></span>
                            </div>
                            <?php if (!empty($customer['email'])): ?>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-envelope text-gray-400 w-4 mr-2"></i>
                                    <a href="mailto:<?= htmlspecialchars($customer['email']) ?>" class="text-blue-600 hover:text-blue-800">
                                        <?= htmlspecialchars($customer['email']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Address -->
                    <?php if (!empty($customer['address']) || !empty($customer['city']) || !empty($customer['state'])): ?>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Address</h4>
                            <div class="mt-2 text-sm text-gray-900">
                                <?php if (!empty($customer['address'])): ?>
                                    <div><?= htmlspecialchars($customer['address']) ?></div>
                                <?php endif; ?>
                                <div>
                                    <?= htmlspecialchars($customer['city'] ?? '') ?>
                                    <?= !empty($customer['state']) ? ', ' . htmlspecialchars($customer['state']) : '' ?>
                                    <?= !empty($customer['postal_code']) ? ' ' . htmlspecialchars($customer['postal_code']) : '' ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Additional Info -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Additional Information</h4>
                        <div class="mt-2 space-y-2 text-sm">
                            <?php if (!empty($customer['date_of_birth'])): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date of Birth:</span>
                                    <span><?= formatDate($customer['date_of_birth'], 'M j, Y') ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Customer Since:</span>
                                <span><?= formatDate($customer['created_at'] ?? 'N/A', 'M j, Y') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 space-y-3">
                    <a href="<?= url('debts/create?customer_id=' . $customer['id']) ?>" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-center block">
                        <i class="fas fa-plus mr-2"></i>Record New Debt
                    </a>
                    <a href="<?= url('sales/create?customer_id=' . $customer['id']) ?>" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center block">
                        <i class="fas fa-shopping-cart mr-2"></i>New Sale
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Details and History -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Financial Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Credit Limit</p>
                            <p class="text-2xl font-bold text-gray-900"><?= formatCurrency($customer['credit_limit'] ?? 0) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-wallet text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Current Balance</p>
                            <p class="text-2xl font-bold text-gray-900"><?= formatCurrency($customer['current_balance'] ?? 0) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Outstanding Debt</p>
                            <?php 
                            $outstandingTotal = 0;
                            foreach ($debts as $debt) {
                                if ($debt['status'] !== 'paid') {
                                    $outstandingTotal += $debt['remaining_amount'] ?? 0;
                                }
                            }
                            ?>
                            <p class="text-2xl font-bold <?= $outstandingTotal > 0 ? 'text-red-600' : 'text-gray-900' ?>">
                                <?= formatCurrency($outstandingTotal) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Debt History -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Debt History</h3>
                    <a href="<?= url('debts?customer_id=' . $customer['id']) ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                </div>
                <div class="p-6">
                    <?php if (empty($debts)): ?>
                        <p class="text-gray-500 text-center py-4">No debt records found</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach (array_slice($debts, 0, 5) as $debt): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            <?= htmlspecialchars($debt['debt_number'] ?? 'N/A') ?>
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <?= htmlspecialchars($debt['description'] ?? 'No description') ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Due: <?= formatDate($debt['due_date'] ?? 'N/A', 'M j, Y') ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium <?= ($debt['status'] ?? '') === 'paid' ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= formatCurrency($debt['remaining_amount'] ?? 0) ?>
                                        </p>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                     <?= ($debt['status'] ?? '') === 'paid' ? 'bg-green-100 text-green-800' : 
                                                         (($debt['status'] ?? '') === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                            <?= ucfirst($debt['status'] ?? 'unpaid') ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sales History -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Recent Sales</h3>
                    <a href="<?= url('sales?customer_id=' . $customer['id']) ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                </div>
                <div class="p-6">
                    <?php if (empty($sales)): ?>
                        <p class="text-gray-500 text-center py-4">No sales records found</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach (array_slice($sales, 0, 5) as $sale): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            Sale #<?= htmlspecialchars($sale['sale_number'] ?? $sale['id']) ?>
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <?= formatDate($sale['sale_date'] ?? $sale['created_at'], 'M j, Y g:i A') ?>
                                        </p>
                                        <?php if (!empty($sale['notes'])): ?>
                                            <p class="text-xs text-gray-500">
                                                <?= htmlspecialchars($sale['notes']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-green-600">
                                            <?= formatCurrency($sale['total_amount'] ?? 0) ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?= ucfirst($sale['payment_method'] ?? 'cash') ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes -->
            <?php if (!empty($customer['notes'])): ?>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($customer['notes'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>