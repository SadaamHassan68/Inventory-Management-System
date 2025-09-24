<?php include APP_ROOT . '/app/views/layouts/app.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="text-gray-600">Debt details and payment history</p>
        </div>
        <div class="flex space-x-3">
            <?php if ($debt['status'] !== 'paid' && (isAdmin() || isStaff())): ?>
                <button onclick="showPaymentModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Add Payment
                </button>
            <?php endif; ?>
            <?php if (isAdmin()): ?>
                <a href="<?= url('debts/' . $debt['id'] . '/edit') ?>" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            <?php endif; ?>
            <a href="<?= url('debts') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Debts
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (hasFlash('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            <?= getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (hasFlash('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <?= getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Debt Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Debt Details Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Debt Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Debt Number</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900"><?= htmlspecialchars($debt['debt_number']) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-1">
                                <?php
                                $statusColor = match($debt['status']) {
                                    'paid' => 'green',
                                    'partially_paid' => 'yellow',
                                    'overdue' => 'red',
                                    default => 'gray'
                                };
                                ?>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800">
                                    <?= ucfirst(str_replace('_', ' ', $debt['status'])) ?>
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Original Amount</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900"><?= formatCurrency($debt['original_amount']) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Remaining Amount</label>
                            <p class="mt-1 text-lg font-semibold <?= $debt['remaining_amount'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                <?= formatCurrency($debt['remaining_amount']) ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Debt Date</label>
                            <p class="mt-1 text-gray-900"><?= formatDate($debt['debt_date'], 'F j, Y') ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Due Date</label>
                            <p class="mt-1 text-gray-900">
                                <?= formatDate($debt['due_date'], 'F j, Y') ?>
                                <?php if (strtotime($debt['due_date']) < time() && $debt['status'] !== 'paid'): ?>
                                    <?php $daysOverdue = ceil((time() - strtotime($debt['due_date'])) / (60 * 60 * 24)); ?>
                                    <span class="ml-2 text-sm text-red-600 font-medium">
                                        (<?= $daysOverdue ?> days overdue)
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php if (!empty($debt['sale_number'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Related Sale</label>
                                <p class="mt-1 text-blue-600 hover:text-blue-800">
                                    <a href="<?= url('sales/' . $debt['sale_id']) ?>"><?= htmlspecialchars($debt['sale_number']) ?></a>
                                </p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created By</label>
                            <p class="mt-1 text-gray-900"><?= htmlspecialchars($debt['created_by_name'] ?? 'Unknown') ?></p>
                        </div>
                    </div>

                    <?php if (!empty($debt['description'])): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <p class="mt-1 text-gray-900"><?= nl2br(htmlspecialchars($debt['description'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($debt['notes'])): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <p class="mt-1 text-gray-900"><?= nl2br(htmlspecialchars($debt['notes'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Payment History</h3>
                    <?php if ($debt['status'] !== 'paid' && (isAdmin() || isStaff())): ?>
                        <button onclick="showPaymentModal()" class="text-sm text-blue-600 hover:text-blue-800">
                            <i class="fas fa-plus mr-1"></i>Add Payment
                        </button>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <?php if (empty($payments)): ?>
                        <p class="text-gray-500 text-center py-8">No payments recorded yet</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($payments as $payment): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    <?= formatCurrency($payment['payment_amount']) ?>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <?= formatDate($payment['payment_date'], 'F j, Y g:i A') ?>
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-gray-600">
                                                    via <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    by <?= htmlspecialchars($payment['recorded_by_name'] ?? 'Unknown') ?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php if (!empty($payment['notes'])): ?>
                                            <p class="mt-2 text-sm text-gray-600">
                                                <?= htmlspecialchars($payment['notes']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Payment Summary -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-sm text-gray-600">Total Payments</p>
                                    <p class="text-lg font-semibold text-green-600">
                                        <?= formatCurrency($debt['original_amount'] - $debt['remaining_amount']) ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Payment Count</p>
                                    <p class="text-lg font-semibold text-gray-900"><?= count($payments) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Progress</p>
                                    <p class="text-lg font-semibold text-blue-600">
                                        <?= $debt['original_amount'] > 0 ? round((($debt['original_amount'] - $debt['remaining_amount']) / $debt['original_amount']) * 100, 1) : 0 ?>%
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Customer Information Sidebar -->
        <div class="space-y-6">
            <!-- Customer Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Customer Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-lg font-medium text-blue-800">
                                    <?= strtoupper(substr($debt['customer_name'], 0, 2)) ?>
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">
                                <?= htmlspecialchars($debt['customer_name']) ?>
                            </h4>
                            <p class="text-sm text-gray-600">
                                <?= htmlspecialchars($debt['customer_code']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <?php if (!empty($debt['customer_phone'])): ?>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Phone:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    <a href="tel:<?= htmlspecialchars($debt['customer_phone']) ?>" class="text-blue-600 hover:text-blue-800">
                                        <?= htmlspecialchars($debt['customer_phone']) ?>
                                    </a>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($debt['customer_email'])): ?>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Email:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    <a href="mailto:<?= htmlspecialchars($debt['customer_email']) ?>" class="text-blue-600 hover:text-blue-800">
                                        <?= htmlspecialchars($debt['customer_email']) ?>
                                    </a>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Credit Limit:</span>
                            <span class="text-sm font-medium text-gray-900">
                                <?= formatCurrency($debt['credit_limit'] ?? 0) ?>
                            </span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="<?= url('customers/' . $debt['customer_id']) ?>" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center block">
                            <i class="fas fa-user mr-2"></i>View Customer Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if ($debt['status'] !== 'paid'): ?>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <?php if (isAdmin() || isStaff()): ?>
                            <button onclick="showPaymentModal()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i>Record Payment
                            </button>
                        <?php endif; ?>
                        
                        <button onclick="sendReminder()" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-bell mr-2"></i>Send Reminder
                        </button>
                        
                        <button onclick="printDebt()" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            <i class="fas fa-print mr-2"></i>Print Debt Statement
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Record Payment</h3>
            <form method="POST" action="<?= url('debts/' . $debt['id'] . '/payment') ?>">
                <?= csrfField() ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   name="payment_amount" 
                                   step="0.01" 
                                   min="0.01" 
                                   max="<?= $debt['remaining_amount'] ?>"
                                   required 
                                   class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Maximum: <?= formatCurrency($debt['remaining_amount']) ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select name="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="cash">Cash</option>
                            <option value="credit">Credit Card</option>
                            <option value="debit">Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" 
                                  rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Optional payment notes..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closePaymentModal()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function sendReminder() {
    if (confirm('Send payment reminder to <?= htmlspecialchars($debt['customer_name']) ?>?')) {
        alert('Payment reminder sent successfully!');
    }
}

function printDebt() {
    window.print();
}

// Close modal when clicking outside
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});
</script>