<?php include APP_ROOT . '/app/views/layouts/app.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="text-gray-600">Import customer data from CSV file</p>
        </div>
        <a href="<?= url('customers') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (hasFlash('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            <?= getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (hasFlash('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <?= getFlash('error') ?>
        </div>
    <?php endif; ?>

    <?php if (hasFlash('warning')): ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
            <?= getFlash('warning') ?>
        </div>
    <?php endif; ?>

    <!-- Import Errors -->
    <?php if (isset($_SESSION['import_errors']) && !empty($_SESSION['import_errors'])): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-red-800 font-medium mb-2">Import Errors:</h3>
            <ul class="text-red-700 text-sm space-y-1">
                <?php foreach ($_SESSION['import_errors'] as $error): ?>
                    <li>• <?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['import_errors']); ?>
    <?php endif; ?>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h2 class="text-lg font-medium text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>CSV Import Instructions
        </h2>
        
        <div class="text-blue-800 space-y-3">
            <p><strong>Required CSV Format:</strong></p>
            <p>Your CSV file should contain the following columns in this exact order:</p>
            
            <div class="bg-white border border-blue-200 rounded p-3 font-mono text-sm">
                customer_code,full_name,phone,email,address,city,state,postal_code,date_of_birth,credit_limit,notes
            </div>
            
            <ul class="space-y-1 text-sm">
                <li>• <strong>customer_code:</strong> Optional - will be auto-generated if empty</li>
                <li>• <strong>full_name:</strong> Required - Customer's full name</li>
                <li>• <strong>phone:</strong> Required - Phone number</li>
                <li>• <strong>email:</strong> Optional - Email address</li>
                <li>• <strong>address:</strong> Optional - Street address</li>
                <li>• <strong>city:</strong> Optional - City name</li>
                <li>• <strong>state:</strong> Optional - State or province</li>
                <li>• <strong>postal_code:</strong> Optional - ZIP or postal code</li>
                <li>• <strong>date_of_birth:</strong> Optional - Format: YYYY-MM-DD</li>
                <li>• <strong>credit_limit:</strong> Optional - Numeric value (default: 0)</li>
                <li>• <strong>notes:</strong> Optional - Additional notes</li>
            </ul>
            
            <p class="text-sm"><strong>Note:</strong> The first row should contain column headers.</p>
        </div>
    </div>

    <!-- Sample Download -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Download Sample Template</h3>
        <p class="text-gray-600 mb-4">Download a sample CSV template to get started with the correct format.</p>
        <a href="data:text/csv;charset=utf-8,customer_code,full_name,phone,email,address,city,state,postal_code,date_of_birth,credit_limit,notes%0A,John Doe,555-0123,john@example.com,123 Main St,Anytown,CA,12345,1985-06-15,1000.00,Sample customer%0A,Jane Smith,555-0124,jane@example.com,456 Oak Ave,Otherville,NY,67890,1990-03-22,1500.00,Another sample" 
           download="customer_import_template.csv"
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-download mr-2"></i>Download Template
        </a>
    </div>

    <!-- Import Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Upload CSV File</h3>
        </div>
        
        <form method="POST" action="<?= url('customers/import') ?>" enctype="multipart/form-data" class="p-6">
            <?= csrfField() ?>
            
            <div class="space-y-6">
                <div>
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                        CSV File <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="csv_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Upload a CSV file</span>
                                    <input id="csv_file" name="csv_file" type="file" accept=".csv" required class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">CSV files only, up to 10MB</p>
                        </div>
                    </div>
                    <div id="file-info" class="mt-2 text-sm text-gray-600 hidden">
                        <span id="file-name"></span> (<span id="file-size"></span>)
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Important Notes:</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Duplicate customer codes will be skipped</li>
                                    <li>Invalid phone numbers will cause rows to be skipped</li>
                                    <li>Make sure your CSV file uses UTF-8 encoding</li>
                                    <li>Large files may take some time to process</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <a href="<?= url('customers') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            id="import-btn"
                            disabled>
                        <i class="fas fa-upload mr-2"></i>Import Customers
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('csv_file');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const importBtn = document.getElementById('import-btn');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
                alert('Please select a valid CSV file.');
                e.target.value = '';
                fileInfo.classList.add('hidden');
                importBtn.disabled = true;
                return;
            }

            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                alert('File size must be less than 10MB.');
                e.target.value = '';
                fileInfo.classList.add('hidden');
                importBtn.disabled = true;
                return;
            }

            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('hidden');
            importBtn.disabled = false;
        } else {
            fileInfo.classList.add('hidden');
            importBtn.disabled = true;
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>