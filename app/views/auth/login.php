<?php
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center bg-blue-600 rounded-full">
                <i class="fas fa-warehouse text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Inventory Management System
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="<?= url('login') ?>">
            <?= csrfField() ?>
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Username or Email</label>
                    <input 
                        id="username" 
                        name="username" 
                        type="text" 
                        required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?= hasErrors('username') ? 'border-red-500' : '' ?>" 
                        placeholder="Username or Email"
                        value="<?= old('username') ?>"
                    >
                    <?php if (hasErrors('username')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= getErrors('username')[0] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?= hasErrors('password') ? 'border-red-500' : '' ?>" 
                        placeholder="Password"
                    >
                    <?php if (hasErrors('password')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= getErrors('password')[0] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember-me" 
                        name="remember-me" 
                        type="checkbox" 
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock text-blue-500 group-hover:text-blue-400" aria-hidden="true"></i>
                    </span>
                    Sign in
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Default login: <strong>admin</strong> / <strong>admin123</strong>
                </p>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Login - Inventory Management System';
include __DIR__ . '/../layouts/app.php';
?>