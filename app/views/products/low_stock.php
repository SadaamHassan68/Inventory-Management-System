<?php
$title = $title ?? 'Low Stock Products';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Low Stock Alert</h1>
            <p class="text-gray-600">Products that need restocking</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= url('products') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
            <?php if (isAdmin()): ?>
                <a href="<?= url('products/create') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Add Product
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

    <!-- Low Stock Alert Summary -->
    <div class="bg-orange-50 border-l-4 border-orange-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-orange-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-orange-700">
                    <span class="font-medium"><?= count($products) ?> product(s)</span> are currently at or below their minimum stock levels.
                    Consider restocking these items to avoid stockouts.
                </p>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <?php if (empty($products)): ?>
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-check-circle text-4xl text-green-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Great! No Low Stock Items</h3>
                    <p class="text-gray-500">All products are above their minimum stock levels.</p>
                    <a href="<?= url('products') ?>" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-box mr-2"></i>View All Products
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shortage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($products as $product): ?>
                            <?php 
                            $stockLevel = $product['quantity_in_stock'] ?? 0;
                            $minLevel = $product['min_stock_level'] ?? 0;
                            $shortage = max(0, $minLevel - $stockLevel);
                            $urgencyLevel = $stockLevel == 0 ? 'critical' : ($stockLevel <= $minLevel * 0.5 ? 'urgent' : 'warning');
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?= asset('uploads/' . $product['image']) ?>" 
                                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                                 class="h-10 w-10 rounded object-cover mr-3">
                                        <?php else: ?>
                                            <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($product['name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                SKU: <?= htmlspecialchars($product['sku']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($product['category_name'] ?? 'No Category') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium 
                                        <?= $urgencyLevel == 'critical' ? 'text-red-600' : 
                                            ($urgencyLevel == 'urgent' ? 'text-orange-600' : 'text-yellow-600') ?>">
                                        <?= formatNumber($stockLevel) ?>
                                        <i class="fas fa-exclamation-triangle ml-1"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= formatNumber($minLevel) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-red-600">
                                        <?= $shortage > 0 ? formatNumber($shortage) : '0' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $urgencyLevel == 'critical' ? 'bg-red-100 text-red-800' : 
                                            ($urgencyLevel == 'urgent' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                        <?= $stockLevel == 0 ? 'Out of Stock' : 
                                            ($urgencyLevel == 'urgent' ? 'Very Low' : 'Low Stock') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="<?= url('products/' . $product['id']) ?>" 
                                       class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (isAdmin()): ?>
                                        <button onclick="openRestockModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $stockLevel ?>, <?= $minLevel ?>)" 
                                                class="text-green-600 hover:text-green-900" title="Restock">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                        <a href="<?= url('products/' . $product['id'] . '/edit') ?>" 
                                           class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            <?php if (isAdmin()): ?>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Total shortage value: 
                            <span class="font-medium text-red-600">
                                <?php 
                                $totalShortageValue = 0;
                                foreach ($products as $product) {
                                    $shortage = max(0, ($product['min_stock_level'] ?? 0) - ($product['quantity_in_stock'] ?? 0));
                                    $totalShortageValue += $shortage * ($product['price'] ?? 0);
                                }
                                echo formatCurrency($totalShortageValue);
                                ?>
                            </span>
                        </div>
                        <div class="flex space-x-3">
                            <a href="<?= url('products/export?filter=low_stock') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                <i class="fas fa-download mr-2"></i>Export List
                            </a>
                            <button onclick="generatePurchaseOrder()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-shopping-cart mr-2"></i>Generate Purchase Order
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Restock Modal -->
<?php if (isAdmin()): ?>
<div id="restockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Restock Product</h3>
        <form id="restockForm" method="POST">
            <?= csrfField() ?>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Product: <span id="restockProductName" class="font-medium"></span></p>
                    <p class="text-sm text-gray-600">Current Stock: <span id="restockCurrentStock" class="font-medium"></span></p>
                    <p class="text-sm text-gray-600">Minimum Level: <span id="restockMinLevel" class="font-medium"></span></p>
                </div>
                <div>
                    <label for="restock_quantity" class="block text-sm font-medium text-gray-700">Quantity to Add</label>
                    <input type="number" 
                           id="restock_quantity" 
                           name="quantity" 
                           min="1"
                           required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Recommended: <span id="recommendedQuantity"></span></p>
                </div>
                <div>
                    <label for="restock_notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea id="restock_notes" 
                              name="notes" 
                              rows="2" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Restocking notes..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeRestockModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Add Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRestockModal(productId, productName, currentStock, minLevel) {
    document.getElementById('restockProductName').textContent = productName;
    document.getElementById('restockCurrentStock').textContent = currentStock;
    document.getElementById('restockMinLevel').textContent = minLevel;
    
    // Calculate recommended quantity (bring to 150% of min level)
    const recommended = Math.max(1, Math.ceil(minLevel * 1.5) - currentStock);
    document.getElementById('recommendedQuantity').textContent = recommended;
    document.getElementById('restock_quantity').value = recommended;
    
    document.getElementById('restockForm').action = '<?= url('products/') ?>' + productId + '/adjust-stock';
    document.getElementById('restockModal').classList.remove('hidden');
}

function closeRestockModal() {
    document.getElementById('restockModal').classList.add('hidden');
    document.getElementById('restockForm').reset();
}

function generatePurchaseOrder() {
    // This would typically redirect to a purchase order creation page
    // For now, we'll show an alert
    alert('Purchase order generation feature coming soon!');
}

// Close modal when clicking outside
document.getElementById('restockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRestockModal();
    }
});
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>