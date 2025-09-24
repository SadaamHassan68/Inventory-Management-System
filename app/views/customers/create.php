<?php
$title = $title ?? 'Ku dar Macmiil';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ku dar Macmiil Cusub</h1>
            <p class="text-gray-600">Samee macmiil cusub oo ku dar xog kaydigaaga</p>
        </div>
        <div>
            <a href="<?= url('customers') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Ku noqo Macaamiisha
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

    <!-- Customer Form -->
    <div class="bg-white rounded-lg shadow">
        <form action="<?= url('customers') ?>" method="POST" class="space-y-6">
            <?= csrfField() ?>
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Macluumaadka Macmiilka</h3>
            </div>

            <div class="px-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Magaca Buuxa *</label>
                        <input type="text" 
                               id="full_name" 
                               name="full_name" 
                               value="<?= old('full_name') ?>"
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('full_name')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('full_name')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="customer_code" class="block text-sm font-medium text-gray-700">Koodka Macmiilka</label>
                        <input type="text" 
                               id="customer_code" 
                               name="customer_code" 
                               value="<?= old('customer_code') ?>"
                               placeholder="Si toos ah ayaa loo dhalayaa haddii aan wax la gelin"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Aqoonsiga gaarka ah ee macmiilka (si toos ah ayaa loo dhalayaa haddii aan wax la gelin)</p>
                        <?php if (hasErrors('customer_code')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('customer_code')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Lambarka Taleefanka *</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone') ?>"
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('phone')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('phone')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Ciwaanka Emailka</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= old('email') ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('email')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('email')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Address Information -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Ciwaanka</label>
                    <textarea id="address" 
                              name="address" 
                              rows="2" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= old('address') ?></textarea>
                    <?php if (hasErrors('address')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('address')) ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Magaalada</label>
                        <input type="text" 
                               id="city" 
                               name="city" 
                               value="<?= old('city') ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('city')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('city')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">Gobolka/Degmada</label>
                        <input type="text" 
                               id="state" 
                               name="state" 
                               value="<?= old('state') ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('state')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('state')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Koodka Boostada</label>
                        <input type="text" 
                               id="postal_code" 
                               name="postal_code" 
                               value="<?= old('postal_code') ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('postal_code')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('postal_code')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Taariikhda Dhalashada</label>
                        <input type="date" 
                               id="date_of_birth" 
                               name="date_of_birth" 
                               value="<?= old('date_of_birth') ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('date_of_birth')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('date_of_birth')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="credit_limit" class="block text-sm font-medium text-gray-700">Xadka Deynta</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   id="credit_limit" 
                                   name="credit_limit" 
                                   value="<?= old('credit_limit', 0) ?>"
                                   step="0.01" 
                                   min="0"
                                   class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Heerka ugu sarreeya ee deynta la oggolaanayo</p>
                        <?php if (hasErrors('credit_limit')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('credit_limit')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Xaalada</label>
                    <select id="status" 
                            name="status" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Firfircoon</option>
                        <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Ma Firfircoon</option>
                    </select>
                    <?php if (hasErrors('status')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('status')) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Faallooyinka</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3" 
                              placeholder="Faallooyinka dheeraad ah oo ku saabsan macmiilka..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= old('notes') ?></textarea>
                    <?php if (hasErrors('notes')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('notes')) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <a href="<?= url('customers') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Jooji
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Samee Macmiil
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-generate customer code based on name
document.getElementById('full_name').addEventListener('blur', function() {
    const codeField = document.getElementById('customer_code');
    if (!codeField.value && this.value) {
        // Simple code generation - use initials + current year + random number
        const names = this.value.split(' ');
        const initials = names.map(name => name.charAt(0).toUpperCase()).join('');
        const year = new Date().getFullYear();
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        codeField.value = `CUST${year}${initials}${random}`.substring(0, 20);
    }
});

// Phone number formatting (simple)
document.getElementById('phone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length >= 6) {
        value = value.replace(/(\d{3})(\d{3})(\d+)/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{3})(\d+)/, '($1) $2');
    }
    this.value = value;
});

// Email validation
document.getElementById('email').addEventListener('blur', function() {
    if (this.value && !this.value.includes('@')) {
        this.setCustomValidity('Please enter a valid email address');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>