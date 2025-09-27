<?php
ob_start();
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-40 left-40 w-60 h-60 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
    </div>

    <!-- Login Container -->
    <div class="relative z-10 w-full max-w-md">
        <!-- Company Branding -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-warehouse text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Inventory Pro</h1>
            <p class="text-blue-200 text-sm">Professional Inventory Management System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl p-8 border border-white/20">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Welcome Back</h2>
                <p class="text-gray-600">Please sign in to your account</p>
            </div>

            <!-- Flash Messages -->
            <?php if (hasFlash('error')): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-red-700 text-sm"><?= getFlash('error') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('success')): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-green-700 text-sm"><?= getFlash('success') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('login') ?>" class="space-y-6">
                <?= csrfField() ?>
                
                <!-- Username Field -->
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-user text-gray-400 mr-2"></i>Username or Email
                    </label>
                    <div class="relative">
                        <input 
                            id="username" 
                            name="username" 
                            type="text" 
                            required 
                            autocomplete="username"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out <?= hasErrors('username') ? 'border-red-500 ring-red-500' : '' ?>" 
                            placeholder="Enter your username or email"
                            value="<?= old('username') ?>"
                        >
                        <?php if (hasErrors('username')): ?>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (hasErrors('username')): ?>
                        <p class="text-sm text-red-600 flex items-center">
                            <i class="fas fa-times-circle mr-1"></i><?= getErrors('username')[0] ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock text-gray-400 mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            autocomplete="current-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out <?= hasErrors('password') ? 'border-red-500 ring-red-500' : '' ?>" 
                            placeholder="Enter your password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition duration-200"
                        >
                            <i id="passwordIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (hasErrors('password')): ?>
                        <p class="text-sm text-red-600 flex items-center">
                            <i class="fas fa-times-circle mr-1"></i><?= getErrors('password')[0] ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember-me" 
                            name="remember-me" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition duration-200"
                        >
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition duration-200">
                            Forgot password?
                        </a>
                    </div>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 ease-in-out transform hover:scale-105"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>

                <!-- Demo Credentials -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="text-center">
                        <p class="text-sm text-blue-800 font-medium mb-2">
                            <i class="fas fa-info-circle mr-1"></i>Demo Credentials
                        </p>
                        <div class="text-xs text-blue-700 space-y-1">
                            <p><strong>Username:</strong> admin</p>
                            <p><strong>Password:</strong> admin123</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-blue-200 text-sm">
                © <?= date('Y') ?> Inventory Pro. All rights reserved.
            </p>
            <div class="flex justify-center space-x-4 mt-2">
                <a href="#" class="text-blue-300 hover:text-white transition duration-200">
                    <i class="fab fa-github"></i>
                </a>
                <a href="#" class="text-blue-300 hover:text-white transition duration-200">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="#" class="text-blue-300 hover:text-white transition duration-200">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

// Add loading state to login button
document.querySelector('form').addEventListener('submit', function(e) {
    const button = e.target.querySelector('button[type="submit"]');
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing In...';
    button.disabled = true;
});

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    const loginCard = document.querySelector('.bg-white\/95');
    loginCard.style.opacity = '0';
    loginCard.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        loginCard.style.transition = 'all 0.6s ease-out';
        loginCard.style.opacity = '1';
        loginCard.style.transform = 'translateY(0)';
    }, 100);
});
</script>

<?php
$content = ob_get_clean();
$title = 'Login - Inventory Management System';
include __DIR__ . '/../layouts/app.php';
?>