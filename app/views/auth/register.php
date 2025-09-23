<?php
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Register New User</h3>
            <p class="mt-1 text-sm text-gray-600">Create a new user account for the system.</p>
        </div>
        
        <form method="POST" action="<?= url('register') ?>" class="px-6 py-4 space-y-6">
            <?= csrfField() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= hasErrors('username') ? 'border-red-500' : '' ?>"
                        value="<?= old('username') ?>"
                    >
                    <?php if (hasErrors('username')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= getErrors('username')[0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= hasErrors('email') ? 'border-red-500' : '' ?>"
                        value="<?= old('email') ?>"
                    >
                    <?php if (hasErrors('email')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= getErrors('email')[0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input 
                        type="text" 
                        name="full_name" 
                        id="full_name" 
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= hasErrors('full_name') ? 'border-red-500' : '' ?>"
                        value="<?= old('full_name') ?>"
                    >
                    <?php if (hasErrors('full_name')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= getErrors('full_name')[0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        value="<?= old('phone') ?>"
                    >
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                    <select 
                        name="role" 
                        id="role" 
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= hasErrors('role') ? 'border-red-500' : '' ?>"
                    >
                        <option value="">Select Role</option>
                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                        <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>Staff</option>
                    </select>
                    <?php if (hasErrors('role')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= getErrors('role')[0] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= hasErrors('password') ? 'border-red-500' : '' ?>"
                    >
                    <?php if (hasErrors('password')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= getErrors('password')[0] ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-sm text-gray-500">Minimum 6 characters</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                    <input 
                        type="password" 
                        name="password_confirm" 
                        id="password_confirm" 
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= hasErrors('password_confirm') ? 'border-red-500' : '' ?>"
                    >
                    <?php if (hasErrors('password_confirm')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= getErrors('password_confirm')[0] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="<?= url('users') ?>" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Register User - Inventory Management System';
include __DIR__ . '/../layouts/app.php';
?>