<?php
$title = $title ?? 'Edit Debt';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Debt</h1>
            <p class="text-gray-600">Update debt information for <?= htmlspecialchars($debt['debt_number']) ?></p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= url('/debts/' . $debt['id']) ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-eye mr-2"></i>View Details
            </a>
            <a href="<?= url('/debts') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back to Debts
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

    <!-- Debt Edit Form -->
    <div class="bg-white rounded-lg shadow">
        <form action="<?= url('/debts/' . $debt['id']) ?>" method="POST" class="space-y-6">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="_method" value="PUT">
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Debt Information</h3>
                <p class="text-sm text-gray-500 mt-1">Update the debt details below</p>
            </div>

            <div class="px-6 space-y-6">

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer *</label>
                        <select id="customer_id" 
                                name="customer_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="">Select a customer</option>
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" 
                                            <?= ($debt['customer_id'] == $customer['id']) ? 'selected' : '' ?>
                                            data-phone="<?= htmlspecialchars($customer['phone']) ?>"
                                            data-email="<?= htmlspecialchars($customer['email'] ?? '') ?>"
                                            data-credit-limit="<?= $customer['credit_limit'] ?? 0 ?>"
                                            data-current-balance="<?= $customer['current_balance'] ?? 0 ?>">
                                        <?= htmlspecialchars($customer['customer_code'] . ' - ' . $customer['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (hasErrors('customer_id')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('customer_id')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="debt_number" class="block text-sm font-medium text-gray-700">Debt Number *</label>
                        <input type="text" 
                               id="debt_number" 
                               name="debt_number" 
                               value="<?= htmlspecialchars($debt['debt_number']) ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               readonly>
                        <p class="mt-1 text-xs text-gray-500">Debt number cannot be changed</p>
                        <?php if (hasErrors('debt_number')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('debt_number')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="original_amount" class="block text-sm font-medium text-gray-700">Original Amount *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   id="original_amount" 
                                   name="original_amount" 
                                   value="<?= $debt['original_amount'] ?>"
                                   step="0.01" 
                                   min="0.01"
                                   required
                                   class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <?php if (hasErrors('original_amount')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('original_amount')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="remaining_amount" class="block text-sm font-medium text-gray-700">Remaining Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   id="remaining_amount" 
                                   name="remaining_amount" 
                                   value="<?= $debt['remaining_amount'] ?>"
                                   step="0.01" 
                                   min="0"
                                   max="<?= $debt['original_amount'] ?>"
                                   class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Must not exceed original amount</p>
                        <?php if (hasErrors('remaining_amount')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('remaining_amount')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date *</label>
                        <input type="date" 
                               id="due_date" 
                               name="due_date" 
                               value="<?= $debt['due_date'] ?>"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('due_date')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('due_date')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status and Sale Reference -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" 
                                name="status" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="unpaid" <?= $debt['status'] == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                            <option value="partially_paid" <?= $debt['status'] == 'partially_paid' ? 'selected' : '' ?>>Partially Paid</option>
                            <option value="paid" <?= $debt['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="overdue" <?= $debt['status'] == 'overdue' ? 'selected' : '' ?>>Overdue</option>
                        </select>
                        <?php if (hasErrors('status')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('status')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="sale_id" class="block text-sm font-medium text-gray-700">Related Sale ID</label>
                        <input type="number" 
                               id="sale_id" 
                               name="sale_id" 
                               value="<?= $debt['sale_id'] ?? '' ?>"
                               placeholder="Optional"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Reference to related sale</p>
                        <?php if (hasErrors('sale_id')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('sale_id')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Description and Notes -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="2" 
                              placeholder="Brief description of the debt..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($debt['description'] ?? '') ?></textarea>
                    <?php if (hasErrors('description')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('description')) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3" 
                              placeholder="Additional notes or payment terms..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($debt['notes'] ?? '') ?></textarea>
                    <?php if (hasErrors('notes')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('notes')) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <a href="<?= url('/debts/' . $debt['id']) ?>" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update Debt
                </button>
            </div>
        </form>
    </div>

    <!-- Current Debt Summary -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Current Debt Summary</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500">Original Amount</p>
                    <p class="text-lg font-bold text-gray-900">$<?= number_format($debt['original_amount'], 2) ?></p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500">Remaining Amount</p>
                    <p class="text-lg font-bold text-red-600">$<?= number_format($debt['remaining_amount'], 2) ?></p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500">Paid Amount</p>
                    <p class="text-lg font-bold text-green-600">$<?= number_format($debt['original_amount'] - $debt['remaining_amount'], 2) ?></p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 <?php
                                 switch($debt['status']) {
                                     case 'paid': echo 'bg-green-100 text-green-800'; break;
                                     case 'partially_paid': echo 'bg-yellow-100 text-yellow-800'; break;
                                     case 'unpaid': echo 'bg-blue-100 text-blue-800'; break;
                                     case 'overdue': echo 'bg-red-100 text-red-800'; break;
                                     default: echo 'bg-gray-100 text-gray-800';
                                 }
                                 ?>">
                        <?= ucfirst(str_replace('_', ' ', $debt['status'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information Panel -->
    <div id="customerInfoPanel" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-user mr-2 text-blue-500"></i>Customer Information
            </h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Phone:</p>
                    <p id="customerPhone" class="text-sm text-gray-900">-</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Email:</p>
                    <p id="customerEmail" class="text-sm text-gray-900">-</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Credit Limit:</p>
                    <p id="customerCreditLimit" class="text-sm text-green-600 font-medium">$0.00</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Current Balance:</p>
                    <p id="customerBalance" class="text-sm text-red-600 font-medium">$0.00</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');
    const customerInfoPanel = document.getElementById('customerInfoPanel');
    
    function updateCustomerInfo() {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        
        if (selectedOption.value) {
            const phone = selectedOption.dataset.phone || '-';
            const email = selectedOption.dataset.email || '-';
            const creditLimit = parseFloat(selectedOption.dataset.creditLimit) || 0;
            const currentBalance = parseFloat(selectedOption.dataset.currentBalance) || 0;
            
            document.getElementById('customerPhone').textContent = phone;
            document.getElementById('customerEmail').textContent = email;
            document.getElementById('customerCreditLimit').textContent = '$' + creditLimit.toFixed(2);
            document.getElementById('customerBalance').textContent = '$' + currentBalance.toFixed(2);
            
            customerInfoPanel.style.display = 'block';
        } else {
            customerInfoPanel.style.display = 'none';
        }
    }
    
    // Update customer info on page load
    updateCustomerInfo();
    
    // Update customer info when selection changes
    customerSelect.addEventListener('change', updateCustomerInfo);
    
    // Validate remaining amount
    const originalAmountInput = document.getElementById('original_amount');
    const remainingAmountInput = document.getElementById('remaining_amount');
    
    function validateRemainingAmount() {
        const originalAmount = parseFloat(originalAmountInput.value) || 0;
        const remainingAmount = parseFloat(remainingAmountInput.value) || 0;
        
        if (remainingAmount > originalAmount) {
            remainingAmountInput.setCustomValidity('Remaining amount cannot exceed original amount');
        } else {
            remainingAmountInput.setCustomValidity('');
        }
    }
    
    originalAmountInput.addEventListener('input', function() {
        remainingAmountInput.max = this.value;
        validateRemainingAmount();
    });
    
    remainingAmountInput.addEventListener('input', validateRemainingAmount);
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>