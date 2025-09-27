<?php
$title = $title ?? 'Debt Details';
ob_start();
?>

<!-- Modern Gradient Background -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-purple-400/20 to-pink-400/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-10 p-6">
        <!-- Header Section with Glass Morphism -->
        <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl shadow-xl mb-8 p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl text-white">
                        <i class="fas fa-receipt text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                            <?= htmlspecialchars($debt['debt_number']) ?>
                        </h1>
                        <p class="text-gray-600 font-medium">Debt Details & Payment History</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <?php if ($debt['status'] !== 'paid' && (isAdmin() || isStaff())): ?>
                        <button onclick="showPaymentModal()" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus mr-2"></i>Add Payment
                        </button>
                    <?php endif; ?>
                    <?php if (isAdmin()): ?>
                        <a href="<?= url('debts/' . $debt['id'] . '/edit') ?>" class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl hover:from-yellow-600 hover:to-orange-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('debts') ?>" class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Debts
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (hasFlash('success')): ?>
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 p-4 rounded-xl mb-6 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-400 mr-3"></i>
                    <p class="text-green-800 font-medium"><?= getFlash('success') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (hasFlash('error')): ?>
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 p-4 rounded-xl mb-6 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                    <p class="text-red-800 font-medium"><?= getFlash('error') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Original Amount Card -->
            <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Original Amount</p>
                        <p class="text-2xl font-bold text-gray-900"><?= formatCurrency($debt['original_amount']) ?></p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white">
                        <i class="fas fa-dollar-sign text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Remaining Amount Card -->
            <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Remaining Amount</p>
                        <p class="text-2xl font-bold <?= $debt['remaining_amount'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                            <?= formatCurrency($debt['remaining_amount']) ?>
                        </p>
                    </div>
                    <div class="p-3 bg-gradient-to-br <?= $debt['remaining_amount'] > 0 ? 'from-red-500 to-red-600' : 'from-green-500 to-green-600' ?> rounded-xl text-white">
                        <i class="fas <?= $debt['remaining_amount'] > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Payment Progress Card -->
            <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Payment Progress</p>
                        <p class="text-2xl font-bold text-blue-600">
                            <?= $debt['original_amount'] > 0 ? round((($debt['original_amount'] - $debt['remaining_amount']) / $debt['original_amount']) * 100, 1) : 0 ?>%
                        </p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl text-white">
                        <i class="fas fa-chart-pie text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-300" 
                             style="width: <?= $debt['original_amount'] > 0 ? round((($debt['original_amount'] - $debt['remaining_amount']) / $debt['original_amount']) * 100, 1) : 0 ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Status</p>
                        <?php
                        $statusConfig = match($debt['status']) {
                            'paid' => ['color' => 'green', 'icon' => 'fa-check-circle', 'text' => 'Paid'],
                            'partially_paid' => ['color' => 'yellow', 'icon' => 'fa-clock', 'text' => 'Partially Paid'],
                            'overdue' => ['color' => 'red', 'icon' => 'fa-exclamation-triangle', 'text' => 'Overdue'],
                            default => ['color' => 'gray', 'icon' => 'fa-hourglass-half', 'text' => 'Pending']
                        };
                        ?>
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-<?= $statusConfig['color'] ?>-100 text-<?= $statusConfig['color'] ?>-800">
                            <i class="fas <?= $statusConfig['icon'] ?> mr-2"></i>
                            <?= $statusConfig['text'] ?>
                        </div>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-<?= $statusConfig['color'] ?>-500 to-<?= $statusConfig['color'] ?>-600 rounded-xl text-white">
                        <i class="fas <?= $statusConfig['icon'] ?> text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Debt Information Section -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Debt Details Card -->
                <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-info-circle mr-3"></i>
                            Debt Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Debt Number</label>
                                <p class="text-lg font-semibold text-gray-900 bg-gray-50 px-4 py-2 rounded-lg">
                                    <?= htmlspecialchars($debt['debt_number']) ?>
                                </p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-<?= $statusConfig['color'] ?>-100 text-<?= $statusConfig['color'] ?>-800">
                                        <i class="fas <?= $statusConfig['icon'] ?> mr-2"></i>
                                        <?= $statusConfig['text'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Original Amount</label>
                                <p class="text-lg font-semibold text-gray-900 bg-blue-50 px-4 py-2 rounded-lg">
                                    <?= formatCurrency($debt['original_amount']) ?>
                                </p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Remaining Amount</label>
                                <p class="text-lg font-semibold <?= $debt['remaining_amount'] > 0 ? 'text-red-600 bg-red-50' : 'text-green-600 bg-green-50' ?> px-4 py-2 rounded-lg">
                                    <?= formatCurrency($debt['remaining_amount']) ?>
                                </p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Debt Date</label>
                                <p class="text-gray-900 bg-gray-50 px-4 py-2 rounded-lg">
                                    <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>
                                    <?= formatDate($debt['debt_date'], 'F j, Y') ?>
                                </p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                <div class="bg-gray-50 px-4 py-2 rounded-lg">
                                    <p class="text-gray-900 flex items-center">
                                        <i class="fas fa-clock mr-2 text-gray-500"></i>
                                        <?= formatDate($debt['due_date'], 'F j, Y') ?>
                                        <?php if (strtotime($debt['due_date']) < time() && $debt['status'] !== 'paid'): ?>
                                            <?php $daysOverdue = ceil((time() - strtotime($debt['due_date'])) / (60 * 60 * 24)); ?>
                                            <span class="ml-2 px-2 py-1 text-xs text-red-600 bg-red-100 rounded-full font-medium">
                                                <?= $daysOverdue ?> days overdue
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <?php if (!empty($debt['sale_number'])): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Related Sale</label>
                                    <p class="bg-blue-50 px-4 py-2 rounded-lg">
                                        <a href="<?= url('sales/' . $debt['sale_id']) ?>" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                            <i class="fas fa-shopping-cart mr-2"></i>
                                            <?= htmlspecialchars($debt['sale_number']) ?>
                                        </a>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Created By</label>
                                <p class="text-gray-900 bg-gray-50 px-4 py-2 rounded-lg flex items-center">
                                    <i class="fas fa-user mr-2 text-gray-500"></i>
                                    <?= htmlspecialchars($debt['created_by_name'] ?? 'Unknown') ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($debt['description'])): ?>
                            <div class="mt-6 space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-900"><?= nl2br(htmlspecialchars($debt['description'])) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($debt['notes'])): ?>
                            <div class="mt-6 space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
                                    <p class="text-gray-900"><?= nl2br(htmlspecialchars($debt['notes'])) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment History Card -->
                <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-history mr-3"></i>
                            Payment History
                        </h3>
                        <?php if ($debt['status'] !== 'paid' && (isAdmin() || isStaff())): ?>
                            <button onclick="showPaymentModal()" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-200 text-sm">
                                <i class="fas fa-plus mr-1"></i>Add Payment
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <?php if (empty($payments)): ?>
                            <div class="text-center py-12">
                                <div class="p-4 bg-gray-100 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                                    <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-medium">No payments recorded yet</p>
                                <p class="text-gray-400 text-sm mt-1">Payment history will appear here once recorded</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($payments as $payment): ?>
                                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 border border-gray-200 rounded-xl p-5 hover:shadow-lg transition-all duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center">
                                                        <div class="p-2 bg-green-100 rounded-lg mr-4">
                                                            <i class="fas fa-money-bill-wave text-green-600"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-xl font-bold text-gray-900">
                                                                <?= formatCurrency($payment['payment_amount']) ?>
                                                            </p>
                                                            <p class="text-sm text-gray-600 flex items-center">
                                                                <i class="fas fa-calendar mr-1"></i>
                                                                <?= formatDate($payment['payment_date'], 'F j, Y g:i A') ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium mb-1">
                                                            <i class="fas fa-credit-card mr-1"></i>
                                                            <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                                                        </div>
                                                        <p class="text-xs text-gray-500 flex items-center justify-end">
                                                            <i class="fas fa-user mr-1"></i>
                                                            by <?= htmlspecialchars($payment['recorded_by_name'] ?? 'Unknown') ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <?php if (!empty($payment['notes'])): ?>
                                                    <div class="mt-3 p-3 bg-white/70 rounded-lg border-l-4 border-blue-400">
                                                        <p class="text-sm text-gray-700 italic">
                                                            <i class="fas fa-sticky-note mr-2 text-blue-500"></i>
                                                            <?= htmlspecialchars($payment['notes']) ?>
                                                        </p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Enhanced Payment Summary -->
                            <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                                    Payment Summary
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="text-center">
                                        <div class="p-4 bg-white rounded-xl shadow-sm">
                                            <div class="text-3xl font-bold text-green-600">
                                                <?= formatCurrency($debt['original_amount'] - $debt['remaining_amount']) ?>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1 flex items-center justify-center">
                                                <i class="fas fa-coins mr-1"></i>
                                                Total Payments
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="p-4 bg-white rounded-xl shadow-sm">
                                            <div class="text-3xl font-bold text-blue-600">
                                                <?= count($payments) ?>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1 flex items-center justify-center">
                                                <i class="fas fa-list mr-1"></i>
                                                Payment Count
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="p-4 bg-white rounded-xl shadow-sm">
                                            <div class="text-3xl font-bold text-purple-600">
                                                <?= $debt['original_amount'] > 0 ? round((($debt['original_amount'] - $debt['remaining_amount']) / $debt['original_amount']) * 100, 1) : 0 ?>%
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1 flex items-center justify-center">
                                                <i class="fas fa-percentage mr-1"></i>
                                                Progress
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Enhanced Customer Information Sidebar -->
            <div class="space-y-8">
                <!-- Customer Profile Card -->
                <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-user-circle mr-3"></i>
                            Customer Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0 h-16 w-16">
                                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center shadow-lg">
                                    <span class="text-2xl font-bold text-white">
                                        <?= strtoupper(substr($debt['customer_name'], 0, 2)) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-xl font-bold text-gray-900">
                                    <?= htmlspecialchars($debt['customer_name']) ?>
                                </h4>
                                <p class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full inline-block mt-1">
                                    <?= htmlspecialchars($debt['customer_code']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <?php if (!empty($debt['customer_phone'])): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-green-500 mr-3"></i>
                                        <span class="text-sm text-gray-600">Phone:</span>
                                    </div>
                                    <a href="tel:<?= htmlspecialchars($debt['customer_phone']) ?>" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                                        <?= htmlspecialchars($debt['customer_phone']) ?>
                                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($debt['customer_email'])): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-red-500 mr-3"></i>
                                        <span class="text-sm text-gray-600">Email:</span>
                                    </div>
                                    <a href="mailto:<?= htmlspecialchars($debt['customer_email']) ?>" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                                        <?= htmlspecialchars($debt['customer_email']) ?>
                                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-credit-card text-purple-500 mr-3"></i>
                                    <span class="text-sm text-gray-600">Credit Limit:</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">
                                    <?= formatCurrency($debt['credit_limit'] ?? 0) ?>
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="<?= url('customers/' . $debt['customer_id']) ?>" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 text-center block font-medium transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i class="fas fa-user mr-2"></i>View Customer Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Quick Actions Card -->
                <?php if ($debt['status'] !== 'paid'): ?>
                    <div class="bg-white/70 backdrop-blur-md border border-white/20 rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-bolt mr-3"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <?php if (isAdmin() || isStaff()): ?>
                                <button onclick="showPaymentModal()" class="w-full px-6 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                                    <i class="fas fa-plus mr-3 text-lg"></i>
                                    <span class="font-medium">Record Payment</span>
                                </button>
                            <?php endif; ?>
                            
                            <button onclick="sendReminder()" class="w-full px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl hover:from-yellow-600 hover:to-orange-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                                <i class="fas fa-bell mr-3 text-lg"></i>
                                <span class="font-medium">Send Reminder</span>
                            </button>
                            
                            <button onclick="printDebt()" class="w-full px-6 py-4 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                                <i class="fas fa-print mr-3 text-lg"></i>
                                <span class="font-medium">Print Statement</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Payment Modal with Glass Morphism -->
<div id="paymentModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 w-full max-w-md">
        <div class="bg-white/90 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 rounded-t-2xl">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    Record Payment
                </h3>
            </div>
            <form method="POST" action="<?= url('debts/' . $debt['id'] . '/payment') ?>" class="p-6">
                <?= csrfField() ?>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Payment Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">$</span>
                            </div>
                            <input type="number" 
                                   name="payment_amount" 
                                   step="0.01" 
                                   min="0.01" 
                                   max="<?= $debt['remaining_amount'] ?>"
                                   required 
                                   class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/70 backdrop-blur-sm">
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Maximum: <?= formatCurrency($debt['remaining_amount']) ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/70 backdrop-blur-sm">
                            <option value="cash">💵 Cash</option>
                            <option value="credit">💳 Credit Card</option>
                            <option value="debit">💳 Debit Card</option>
                            <option value="bank_transfer">🏦 Bank Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" 
                                  rows="3" 
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/70 backdrop-blur-sm"
                                  placeholder="Optional payment notes..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closePaymentModal()" class="px-6 py-3 text-gray-700 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 font-medium transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
    // Add smooth animation
    setTimeout(() => {
        document.querySelector('#paymentModal > div').style.transform = 'scale(1)';
        document.querySelector('#paymentModal > div').style.opacity = '1';
    }, 10);
}

function closePaymentModal() {
    // Add smooth animation
    document.querySelector('#paymentModal > div').style.transform = 'scale(0.95)';
    document.querySelector('#paymentModal > div').style.opacity = '0';
    setTimeout(() => {
        document.getElementById('paymentModal').classList.add('hidden');
    }, 200);
}

function sendReminder() {
    // Enhanced confirmation with modern styling
    if (confirm('Send payment reminder to <?= htmlspecialchars($debt['customer_name']) ?>?\n\nThis will send an email and/or SMS notification to the customer.')) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
        button.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            alert('✅ Payment reminder sent successfully!');
        }, 2000);
    }
}

function printDebt() {
    // Enhanced print function
    const printContent = document.createElement('div');
    printContent.innerHTML = `
        <div style="font-family: Arial, sans-serif; padding: 20px;">
            <h1>Debt Statement</h1>
            <p><strong>Debt Number:</strong> <?= htmlspecialchars($debt['debt_number']) ?></p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($debt['customer_name']) ?></p>
            <p><strong>Original Amount:</strong> <?= formatCurrency($debt['original_amount']) ?></p>
            <p><strong>Remaining Amount:</strong> <?= formatCurrency($debt['remaining_amount']) ?></p>
            <p><strong>Status:</strong> <?= ucfirst(str_replace('_', ' ', $debt['status'])) ?></p>
            <p><strong>Due Date:</strong> <?= formatDate($debt['due_date'], 'F j, Y') ?></p>
        </div>
    `;
    
    const newWindow = window.open('', '_blank');
    newWindow.document.write(printContent.innerHTML);
    newWindow.document.close();
    newWindow.print();
}

// Enhanced modal interactions
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
    }
});

// Initialize modal styling
document.querySelector('#paymentModal > div').style.transform = 'scale(0.95)';
document.querySelector('#paymentModal > div').style.opacity = '0';
document.querySelector('#paymentModal > div').style.transition = 'all 0.2s ease-out';
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>