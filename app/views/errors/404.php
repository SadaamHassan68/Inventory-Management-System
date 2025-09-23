<?php
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <div class="mx-auto h-20 w-20 flex items-center justify-center bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
            </div>
            <h2 class="mt-6 text-6xl font-extrabold text-gray-900">404</h2>
            <h3 class="mt-2 text-2xl font-bold text-gray-900">Page Not Found</h3>
            <p class="mt-4 text-gray-600">
                The page you're looking for doesn't exist or has been moved.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="<?= url('/dashboard') ?>" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-home mr-2"></i>
                Go to Dashboard
            </a>
            
            <button onclick="history.back()" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Go Back
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = '404 - Page Not Found';
include __DIR__ . '/../layouts/app.php';
?>