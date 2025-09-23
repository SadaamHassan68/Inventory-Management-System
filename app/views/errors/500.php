<?php
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <div class="mx-auto h-20 w-20 flex items-center justify-center bg-red-100 rounded-full">
                <i class="fas fa-server text-red-600 text-3xl"></i>
            </div>
            <h2 class="mt-6 text-6xl font-extrabold text-gray-900">500</h2>
            <h3 class="mt-2 text-2xl font-bold text-gray-900">Server Error</h3>
            <p class="mt-4 text-gray-600">
                Something went wrong on our end. Please try again later.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="<?= url('/dashboard') ?>" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-home mr-2"></i>
                Go to Dashboard
            </a>
            
            <button onclick="location.reload()" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-sync-alt mr-2"></i>
                Try Again
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = '500 - Server Error';
include __DIR__ . '/../layouts/app.php';
?>