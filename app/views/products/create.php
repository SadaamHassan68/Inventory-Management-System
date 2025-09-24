<?php
$title = $title ?? 'Add Product';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
            <p class="text-gray-600">Create a new product in your inventory</p>
        </div>
        <div>
            <a href="<?= url('products') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
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

    <!-- Product Form -->
    <div class="bg-white rounded-lg shadow">
        <form action="<?= url('products') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrfField() ?>
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Product Information</h3>
            </div>

            <div class="px-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Magaca Alaabta *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?= old('name') ?>"
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('name')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('name')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700">Lambarka Alaabta (SKU) *</label>
                        <input type="text" 
                               id="sku" 
                               name="sku" 
                               value="<?= old('sku') ?>"
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Lambar gaar ah oo alaabta lagu aqoonsado</p>
                        <?php if (hasErrors('sku')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('sku')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Sharaxaad</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= old('description') ?></textarea>
                    <?php if (hasErrors('description')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('description')) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Category and Supplier -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Qaybta</label>
                        <select id="category_id" 
                                name="category_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Dooro qaybta</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (hasErrors('category_id')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('category_id')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700">Bixiyaha</label>
                        <select id="supplier_id" 
                                name="supplier_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Dooro bixiyaha</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>" 
                                        <?= old('supplier_id') == $supplier['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($supplier['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (hasErrors('supplier_id')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('supplier_id')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700">Qiimaha Kharashka</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   id="cost_price" 
                                   name="cost_price" 
                                   value="<?= old('cost_price') ?>"
                                   step="0.01" 
                                   min="0"
                                   class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <?php if (hasErrors('cost_price')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('cost_price')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="selling_price" class="block text-sm font-medium text-gray-700">Qiimaha Iibka *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   id="selling_price" 
                                   name="selling_price" 
                                   value="<?= old('selling_price') ?>"
                                   step="0.01" 
                                   min="0"
                                   required
                                   class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <?php if (hasErrors('selling_price')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('selling_price')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Tirada Bilaabeed ee Bakhaarka *</label>
                        <input type="number" 
                               id="stock_quantity" 
                               name="stock_quantity" 
                               value="<?= old('stock_quantity', 0) ?>"
                               min="0"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('stock_quantity')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('stock_quantity')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="min_stock_level" class="block text-sm font-medium text-gray-700">Heerka Ugu Yar ee Bakhaarka *</label>
                        <input type="number" 
                               id="min_stock_level" 
                               name="min_stock_level" 
                               value="<?= old('min_stock_level', 10) ?>"
                               min="0"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Digniin markii bakhaarka uu ka yar yahay heerkan</p>
                        <?php if (hasErrors('min_stock_level')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('min_stock_level')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Koodka Baarkoodka</label>
                        <input type="text" 
                               id="barcode" 
                               name="barcode" 
                               value="<?= old('barcode') ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('barcode')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('barcode')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Xaalada</label>
                        <select id="status" 
                                name="status" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Firfircoon</option>
                            <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Ma Firfircoon</option>
                        </select>
                        <?php if (hasErrors('status')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('status')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Sawirka Alaabta</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Soo geli fayl</span>
                                    <input id="image" name="image" type="file" accept="image/*" class="sr-only">
                                </label>
                                <p class="pl-1">ama jiid oo dhig</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF ilaa 5MB</p>
                        </div>
                    </div>
                    <?php if (hasErrors('image')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('image')) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <a href="<?= url('products') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Create Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Create image preview if it doesn't exist
            let preview = document.getElementById('imagePreview');
            if (!preview) {
                preview = document.createElement('div');
                preview.id = 'imagePreview';
                preview.className = 'mt-4';
                e.target.closest('.space-y-1').appendChild(preview);
            }
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="h-32 w-32 object-cover rounded-lg mx-auto">
                <p class="text-sm text-gray-500 text-center mt-2">${file.name}</p>
            `;
        };
        reader.readAsDataURL(file);
    }
});

// Auto-generate SKU based on product name (optional)
document.getElementById('name').addEventListener('blur', function() {
    const skuField = document.getElementById('sku');
    if (!skuField.value && this.value) {
        // Simple SKU generation - convert to uppercase and replace spaces with dashes
        const sku = this.value.toUpperCase().replace(/\s+/g, '-').replace(/[^A-Z0-9-]/g, '');
        skuField.value = sku.substring(0, 20); // Limit length
    }
});
</script>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>