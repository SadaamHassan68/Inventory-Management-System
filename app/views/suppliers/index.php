<?php
$title = $title ?? 'Bixiyayaasha';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bixiyayaasha</h1>
            <p class="text-gray-600">Maamul bixiyayaasha iyo xiriirkooda</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="openAddSupplierModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Ku dar Bixiye
            </button>
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

    <!-- Supplier Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-truck text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Wadarta Bixiyayaasha</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format(count($suppliers)) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Firfircoon</p>
                    <p class="text-2xl font-bold text-green-600">
                        <?= number_format(array_reduce($suppliers, function($count, $supplier) {
                            return $count + ($supplier['is_active'] ? 1 : 0);
                        }, 0)) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Email-ka Leh</p>
                    <p class="text-2xl font-bold text-purple-600">
                        <?= number_format(array_reduce($suppliers, function($count, $supplier) {
                            return $count + (!empty($supplier['email']) ? 1 : 0);
                        }, 0)) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-phone text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Telefanka Leh</p>
                    <p class="text-2xl font-bold text-orange-600">
                        <?= number_format(array_reduce($suppliers, function($count, $supplier) {
                            return $count + (!empty($supplier['phone']) ? 1 : 0);
                        }, 0)) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="<?= url('/suppliers') ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700">Raadi</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="<?= htmlspecialchars($search ?? '') ?>"
                           placeholder="Ku raadi magaca bixiyaha, email-ka, ama telefanka..." 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Raadi
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="<?= url('/suppliers') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i>Nadiifi
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bixiye</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qofka Xiriirka</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Xiriir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cinwaanka</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Xaalada</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ficilada</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-truck text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium">Bixiyayaal ma jiraan</p>
                                    <p class="text-sm">Ku bilow ku daridda bixiyaha koowaad</p>
                                    <button onclick="openAddSupplierModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-plus mr-2"></i>Ku dar Bixiye
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-medium">
                                                <?= strtoupper(substr($supplier['name'], 0, 2)) ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($supplier['name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: #<?= $supplier['id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($supplier['contact_person'] ?? '-') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($supplier['email']): ?>
                                        <div class="text-sm text-gray-900 mb-1">
                                            <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                            <a href="mailto:<?= htmlspecialchars($supplier['email']) ?>" class="hover:text-blue-600">
                                                <?= htmlspecialchars($supplier['email']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($supplier['phone']): ?>
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-phone text-gray-400 mr-1"></i>
                                            <a href="tel:<?= htmlspecialchars($supplier['phone']) ?>" class="hover:text-blue-600">
                                                <?= htmlspecialchars($supplier['phone']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($supplier['email']) && empty($supplier['phone'])): ?>
                                        <span class="text-sm text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        <?= htmlspecialchars($supplier['address'] ?? '-') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($supplier['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Firfircoon
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-pause-circle mr-1"></i>Joojin
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <button onclick="editSupplier(<?= htmlspecialchars(json_encode($supplier)) ?>)" 
                                                class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($supplier['is_active']): ?>
                                            <button onclick="toggleSupplierStatus(<?= $supplier['id'] ?>, false)" 
                                                    class="text-yellow-600 hover:text-yellow-900">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        <?php else: ?>
                                            <button onclick="toggleSupplierStatus(<?= $supplier['id'] ?>, true)" 
                                                    class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button onclick="deleteSupplier(<?= $supplier['id'] ?>, '<?= htmlspecialchars($supplier['name']) ?>')" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Supplier Modal -->
<div id="supplierModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Ku dar Bixiye Cusub</h3>
                <button onclick="closeSupplierModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="supplierForm" method="POST" action="<?= url('/suppliers') ?>">
                <input type="hidden" id="supplierId" name="supplier_id">
                <input type="hidden" id="formMethod" name="_method">
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Magaca Bixiyaha <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">Qofka Xiriirka</label>
                            <input type="text" id="contact_person" name="contact_person" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telefan</label>
                            <input type="tel" id="phone" name="phone" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Cinwaanka</label>
                        <textarea id="address" name="address" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Firfircoon</label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeSupplierModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Ka noqo
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Kaydi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddSupplierModal() {
    document.getElementById('modalTitle').textContent = 'Ku dar Bixiye Cusub';
    document.getElementById('supplierForm').action = '<?= url('/suppliers') ?>';
    document.getElementById('formMethod').value = '';
    document.getElementById('supplierForm').reset();
    document.getElementById('is_active').checked = true;
    document.getElementById('supplierModal').classList.remove('hidden');
}

function editSupplier(supplier) {
    document.getElementById('modalTitle').textContent = 'Wax ka beddel Bixiyaha';
    document.getElementById('supplierForm').action = '<?= url('/suppliers') ?>/' + supplier.id;
    document.getElementById('formMethod').value = 'PUT';
    
    document.getElementById('supplierId').value = supplier.id;
    document.getElementById('name').value = supplier.name || '';
    document.getElementById('contact_person').value = supplier.contact_person || '';
    document.getElementById('email').value = supplier.email || '';
    document.getElementById('phone').value = supplier.phone || '';
    document.getElementById('address').value = supplier.address || '';
    document.getElementById('is_active').checked = supplier.is_active == 1;
    
    document.getElementById('supplierModal').classList.remove('hidden');
}

function closeSupplierModal() {
    document.getElementById('supplierModal').classList.add('hidden');
}

function toggleSupplierStatus(id, status) {
    if (confirm(status ? 'Ma hubtaa inaad doonayso in bixiyahan la firfirciyo?' : 'Ma hubtaa inaad doonayso in bixiyahan la joojin?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= url('/suppliers') ?>/' + id;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'is_active';
        statusField.value = status ? '1' : '0';
        
        form.appendChild(methodField);
        form.appendChild(statusField);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSupplier(id, name) {
    if (confirm('Ma hubtaa inaad doonayso in bixiyaha "' + name + '" la tirtiro? Tallaabadan lama celin karo.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= url('/suppliers') ?>/' + id;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('supplierModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSupplierModal();
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>