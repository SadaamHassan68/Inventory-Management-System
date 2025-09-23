<?php
$title = $title ?? 'Product Details';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-gray-600">Product details and information</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= url('products') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
            <?php if (isAdmin()): ?>
                <a href="<?= url('products/' . $product['id'] . '/edit') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit Product
                </a>
            <?php endif; ?>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Image and Basic Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Product Image -->
                <div class="mb-6">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= asset('uploads/' . $product['image']) ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             class="w-full h-64 object-cover rounded-lg">
                    <?php else: ?>
                        <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-gray-400 text-6xl"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Status Badge -->
                <div class="mb-4">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                 <?= ($product['is_active'] ?? 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <i class="fas <?= ($product['is_active'] ?? 1) ? 'fa-check-circle' : 'fa-times-circle' ?> mr-1"></i>
                        <?= ($product['is_active'] ?? 1) ? 'Active' : 'Inactive' ?>
                    </span>
                </div>

                <!-- Quick Actions -->
                <?php if (isAdmin()): ?>
                    <div class="space-y-3">
                        <button onclick="openStockModal()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-warehouse mr-2"></i>Adjust Stock
                        </button>
                        <a href="<?= url('products/' . $product['id'] . '/edit') ?>" class="block w-full px-4 py-2 border border-gray-300 text-center text-gray-700 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-edit mr-2"></i>Edit Product
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Product Information</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Product Name</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($product['name']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">SKU</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono"><?= htmlspecialchars($product['sku']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($product['category_name'] ?? 'No Category') ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($product['supplier_name'] ?? 'No Supplier') ?></dd>
                        </div>
                        <?php if (!empty($product['barcode'])): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono"><?= htmlspecialchars($product['barcode']) ?></dd>
                            </div>
                        <?php endif; ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= formatDate($product['created_at'] ?? 'N/A', 'M j, Y g:i A') ?></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($product['description'])): ?>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Description</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pricing and Stock Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pricing -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Pricing</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Cost Price:</span>
                            <span class="text-sm text-gray-900"><?= formatCurrency($product['cost_price'] ?? 0) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Selling Price:</span>
                            <span class="text-lg font-bold text-green-600"><?= formatCurrency($product['price'] ?? 0) ?></span>
                        </div>
                        <?php if (!empty($product['cost_price']) && !empty($product['price'])): ?>
                            <?php $margin = (($product['price'] - $product['cost_price']) / $product['price']) * 100; ?>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Profit Margin:</span>
                                <span class="text-sm font-medium <?= $margin > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= number_format($margin, 1) ?>%
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Stock Information</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <?php 
                        $stockLevel = $product['quantity_in_stock'] ?? 0;
                        $minLevel = $product['min_stock_level'] ?? 0;
                        $isLowStock = $stockLevel <= $minLevel;
                        ?>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Current Stock:</span>
                            <span class="text-lg font-bold <?= $isLowStock ? 'text-red-600' : 'text-green-600' ?>">
                                <?= formatNumber($stockLevel) ?>
                                <?php if ($isLowStock): ?>
                                    <i class="fas fa-exclamation-triangle text-red-500 ml-1"></i>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Minimum Level:</span>
                            <span class="text-sm text-gray-900"><?= formatNumber($minLevel) ?></span>
                        </div>
                        <?php if (!empty($product['price'])): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Stock Value:</span>
                                <span class="text-sm font-medium text-blue-600">
                                    <?= formatCurrency($stockLevel * $product['price']) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if ($isLowStock): ?>
                            <div class="p-3 bg-red-50 rounded-lg">
                                <p class="text-sm text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Low stock alert! Consider restocking soon.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<?php if (isAdmin()): ?>
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Adjust Stock Level</h3>
        <form id="stockForm" method="POST" action="<?= url('products/' . $product['id'] . '/adjust-stock') ?>">
            <?= csrfField() ?>
            <div class="space-y-4">
                <div>
                    <label for="adjustment_type" class="block text-sm font-medium text-gray-700">Adjustment Type</label>
                    <select id="adjustment_type" name="adjustment_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="add">Add Stock (Increase)</option>
                        <option value="remove">Remove Stock (Decrease)</option>
                        <option value="set">Set Exact Amount</option>
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" 
                           id="quantity" 
                           name="quantity" 
                           min="0"
                           required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Current stock: <?= formatNumber($stockLevel) ?></p>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="2" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Reason for adjustment..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeStockModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openStockModal() {
    document.getElementById('stockModal').classList.remove('hidden');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
    document.getElementById('stockForm').reset();
}

// Close modal when clicking outside
document.getElementById('stockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStockModal();
    }
});
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>