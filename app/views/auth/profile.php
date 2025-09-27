<?php
$title = $title ?? 'User Profile';
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Profile</h1>
            <p class="text-gray-600">Manage your account information</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= url('/dashboard') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (hasFlash('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= getFlash('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Profile Information</h2>
                
                <form method="POST" action="<?= url('/profile') ?>">
                    <?= csrfField() ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?= htmlspecialchars($user['full_name'] ?? '') ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <?php if (hasErrors('full_name')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= getError('full_name') ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="<?= htmlspecialchars($user['username'] ?? '') ?>"
                                   disabled
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-100">
                            <p class="mt-1 text-sm text-gray-500">Username cannot be changed</p>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <?php if (hasErrors('email')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= getError('email') ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <input type="text" 
                                   id="role" 
                                   name="role" 
                                   value="<?= htmlspecialchars(ucfirst($user['role'] ?? '')) ?>"
                                   disabled
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-100">
                        </div>
                        
                        <div>
                            <label for="last_login" class="block text-sm font-medium text-gray-700">Last Login</label>
                            <input type="text" 
                                   id="last_login" 
                                   name="last_login" 
                                   value="<?= htmlspecialchars($user['last_login'] ? formatDate($user['last_login'], 'F j, Y g:i A') : 'Never') ?>"
                                   disabled
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-100">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Change Password</h2>
                
                <form method="POST" action="<?= url('/profile/change-password') ?>">
                    <?= csrfField() ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <?php if (hasErrors('current_password')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= getError('current_password') ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="md:col-span-2"></div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <?php if (hasErrors('new_password')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= getError('new_password') ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <?php if (hasErrors('confirm_password')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= getError('confirm_password') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-key mr-2"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Profile Sidebar -->
        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Profile Picture</h2>
                
                <div class="flex flex-col items-center">
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-32 h-32 flex items-center justify-center mb-4">
                        <i class="fas fa-user text-4xl text-gray-400"></i>
                    </div>
                    
                    <button class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                        <i class="fas fa-upload mr-2"></i>Upload New
                    </button>
                    
                    <p class="mt-2 text-xs text-gray-500 text-center">
                        JPG, PNG or GIF<br>
                        Max size 2MB
                    </p>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Account Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Account Status</span>
                            <span class="text-sm font-medium text-green-600">Active</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Member Since</span>
                            <span class="text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($user['created_at'] ? formatDate($user['created_at'], 'F j, Y') : 'Unknown') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h2>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-blue-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Logged in</p>
                            <p class="text-xs text-gray-500">Today at 9:30 AM</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-edit text-green-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Updated profile</p>
                            <p class="text-xs text-gray-500">Yesterday</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-key text-purple-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Changed password</p>
                            <p class="text-xs text-gray-500">2 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>