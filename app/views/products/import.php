<?php
$title = $title ?? 'Import Products';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Import Products</h1>
            <p class="text-gray-600">Bulk upload products from CSV file</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= url('products') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
            <a href="<?= url('products/export?template=1') ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Download Template
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

    <?php if (hasFlash('warning')): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <?= getFlash('warning') ?>
        </div>
    <?php endif; ?>

    <!-- Import Errors -->
    <?php if (isset($_SESSION['import_errors']) && !empty($_SESSION['import_errors'])): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Import Errors</h3>
                    <div class="mt-2">
                        <ul class="list-disc list-inside text-sm text-red-700">
                            <?php foreach ($_SESSION['import_errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['import_errors']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Import Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Upload CSV File</h3>
            </div>
            <form action="<?= url('products/import') ?>" method="POST" enctype="multipart/form-data" class="p-6">
                <?= csrfField() ?>
                
                <div class="space-y-6">
                    <!-- File Upload -->
                    <div>
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">Select CSV File</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="csv_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a CSV file</span>
                                        <input id="csv_file" name="csv_file" type="file" accept=".csv" required class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">CSV files only, up to 10MB</p>
                            </div>
                        </div>
                        <div id="file-info" class="mt-2 hidden">
                            <p class="text-sm text-gray-600">Selected: <span id="file-name"></span></p>
                        </div>
                    </div>

                    <!-- Import Options -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Import Options</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="skip_duplicates" value="1" checked 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Skip products with duplicate SKUs</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="update_existing" value="1" 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Update existing products (by SKU)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="validate_only" value="1" 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Validate only (don't import)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed" id="submit-btn" disabled>
                            <i class="fas fa-upload mr-2"></i>Import Products
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Instructions -->
        <div class="space-y-6">
            <!-- CSV Format Instructions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">CSV Format Requirements</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Required Columns (in order):</h4>
                            <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                                <li><strong>Name</strong> - Product name</li>
                                <li><strong>Description</strong> - Product description</li>
                                <li><strong>SKU</strong> - Unique product code</li>
                                <li><strong>Barcode</strong> - Product barcode (optional)</li>
                                <li><strong>Category ID</strong> - Category ID number</li>
                                <li><strong>Supplier ID</strong> - Supplier ID number</li>
                                <li><strong>Cost Price</strong> - Purchase cost</li>
                                <li><strong>Selling Price</strong> - Sale price</li>
                                <li><strong>Stock Quantity</strong> - Initial stock (optional, default: 0)</li>
                                <li><strong>Min Stock Level</strong> - Minimum stock alert (optional, default: 10)</li>
                                <li><strong>Status</strong> - active/inactive (optional, default: active)</li>
                            </ol>
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Important:</strong> The first row should contain column headers. Make sure your CSV follows the exact column order specified above.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sample Data -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sample CSV Data</h3>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-700">Name,Description,SKU,Barcode,Category ID,Supplier ID,Cost Price,Selling Price,Stock Quantity,Min Stock Level,Status
"Wireless Mouse","Ergonomic wireless mouse",WM001,123456789,1,1,15.00,25.00,50,10,active
"USB Cable","USB Type-C cable 6ft",USB001,987654321,2,1,5.00,12.00,100,20,active
"Laptop Stand","Adjustable laptop stand",LS001,,1,2,25.00,45.00,25,5,active</pre>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Import Tips</h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                            Download the template file to ensure correct formatting
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                            Make sure SKUs are unique across all products
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                            Use existing Category and Supplier IDs (create them first if needed)
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                            Use "Validate only" option to check for errors before importing
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                            Keep file size under 10MB for better performance
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// File upload handling
document.getElementById('csv_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const submitBtn = document.getElementById('submit-btn');
    
    if (file) {
        fileName.textContent = file.name;
        fileInfo.classList.remove('hidden');
        submitBtn.disabled = false;
        
        // Validate file type
        if (!file.name.toLowerCase().endsWith('.csv')) {
            alert('Please select a CSV file.');
            e.target.value = '';
            fileInfo.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }
        
        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB.');
            e.target.value = '';
            fileInfo.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }
    } else {
        fileInfo.classList.add('hidden');
        submitBtn.disabled = true;
    }
});

// Drag and drop functionality
const dropZone = document.querySelector('.border-dashed');

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-blue-400', 'bg-blue-50');
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400', 'bg-blue-50');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400', 'bg-blue-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('csv_file').files = files;
        // Trigger change event
        const event = new Event('change', { bubbles: true });
        document.getElementById('csv_file').dispatchEvent(event);
    }
});

// Form submission handling
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
});

// Checkbox logic for update existing vs skip duplicates
document.querySelector('input[name="update_existing"]').addEventListener('change', function() {
    const skipDuplicates = document.querySelector('input[name="skip_duplicates"]');
    if (this.checked) {
        skipDuplicates.checked = false;
        skipDuplicates.disabled = true;
    } else {
        skipDuplicates.disabled = false;
    }
});

document.querySelector('input[name="skip_duplicates"]').addEventListener('change', function() {
    const updateExisting = document.querySelector('input[name="update_existing"]');
    if (this.checked) {
        updateExisting.checked = false;
        updateExisting.disabled = true;
    } else {
        updateExisting.disabled = false;
    }
});
</script>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>