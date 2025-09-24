<?php include APP_ROOT . '/app/views/layouts/app.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="text-gray-600">Update customer information</p>
        </div>
        <a href="<?= url('customers') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Customer Information</h2>
        </div>
        
        <form method="POST" action="<?= url('customers/' . $customer['id']) ?>" class="p-6 space-y-6">
            <?= csrfField() ?>
            <input type="hidden" name="_method" value="PUT">
            
            <!-- Flash Messages -->
            <?php if (hasFlash('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <?= getFlash('error') ?>
                </div>
            <?php endif; ?>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_code" class="block text-sm font-medium text-gray-700">Customer Code</label>
                    <input type="text" 
                           id="customer_code" 
                           name="customer_code" 
                           value="<?= htmlspecialchars($customer['customer_code'] ?? '') ?>"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Auto-generated if empty">
                    <?php if (hasErrors('customer_code')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('customer_code')) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?= htmlspecialchars($customer['full_name'] ?? '') ?>"
                           required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <?php if (hasErrors('full_name')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('full_name')) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= htmlspecialchars($customer['phone'] ?? '') ?>"
                           required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <?php if (hasErrors('phone')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('phone')) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($customer['email'] ?? '') ?>"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <?php if (hasErrors('email')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('email')) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" 
                           id="date_of_birth" 
                           name="date_of_birth" 
                           value="<?= htmlspecialchars($customer['date_of_birth'] ?? '') ?>"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <?php if (hasErrors('date_of_birth')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('date_of_birth')) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="credit_limit" class="block text-sm font-medium text-gray-700">Credit Limit</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" 
                               id="credit_limit" 
                               name="credit_limit" 
                               value="<?= htmlspecialchars($customer['credit_limit'] ?? '0') ?>"
                               step="0.01" 
                               min="0"
                               class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <?php if (hasErrors('credit_limit')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('credit_limit')) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Address Information -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Street Address</label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter full address"><?= htmlspecialchars($customer['address'] ?? '') ?></textarea>
                        <?php if (hasErrors('address')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('address')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="<?= htmlspecialchars($customer['city'] ?? '') ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <?php if (hasErrors('city')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('city')) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State/Province</label>
                            <input type="text" 
                                   id="state" 
                                   name="state" 
                                   value="<?= htmlspecialchars($customer['state'] ?? '') ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <?php if (hasErrors('state')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('state')) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                            <input type="text" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="<?= htmlspecialchars($customer['postal_code'] ?? '') ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <?php if (hasErrors('postal_code')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('postal_code')) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                <div class="space-y-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Additional notes about the customer..."><?= htmlspecialchars($customer['notes'] ?? '') ?></textarea>
                        <?php if (hasErrors('notes')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('notes')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                       name="status" 
                                       value="active" 
                                       <?= ($customer['is_active'] ?? 1) ? 'checked' : '' ?>
                                       class="form-radio h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            <label class="inline-flex items-center ml-6">
                                <input type="radio" 
                                       name="status" 
                                       value="inactive" 
                                       <?= !($customer['is_active'] ?? 1) ? 'checked' : '' ?>
                                       class="form-radio h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Inactive</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="<?= url('customers') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Phone number formatting
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 10) {
        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 6) {
        value = value.replace(/(\d{3})(\d{3})/, '($1) $2-');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{3})/, '($1) ');
    }
    e.target.value = value;
});
</script>