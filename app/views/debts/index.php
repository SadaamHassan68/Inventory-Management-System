<?php
$title = $title ?? 'Deynta';
ob_start();
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Deynta</h1>
            <p class="text-gray-600">Maamul deynta macaamiisha iyo la socodka lacag bixinta</p>
        </div>
        <div class="flex space-x-3">
            <?php if (isAdmin() || isStaff()): ?>
                <a href="<?= url('/debts/create') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Diiwaan geli Deyn Cusub
                </a>
            <?php endif; ?>
            <a href="<?= url('/debts/overdue') ?>" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                <i class="fas fa-exclamation-triangle mr-2"></i>Dhacay
            </a>
            <a href="<?= url('/debts/export') ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Soo deji
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

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Wadarta Deynta</p>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_debts'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Lacagta aan la bixin</p>
                    <p class="text-2xl font-bold text-orange-600">$<?= number_format($stats['total_outstanding_amount'] ?? 0, 2) ?></p>
                    <p class="text-xs text-orange-600"><?= number_format($stats['outstanding_debts'] ?? 0) ?> deyn</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Deynta Dhacday</p>
                    <p class="text-2xl font-bold text-red-600"><?= number_format($stats['overdue_debts'] ?? 0) ?></p>
                    <p class="text-xs text-red-600">$<?= number_format($stats['overdue_amount'] ?? 0, 2) ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= url('/debts/overdue') ?>" class="text-sm text-red-600 hover:text-red-800">Eeg faahfaahinta →</a>
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
                    <p class="text-sm font-medium text-gray-600">La bixiyay Bishaan</p>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($stats['paid_this_month'] ?? 0) ?></p>
                    <p class="text-xs text-green-600">$<?= number_format($stats['paid_amount_this_month'] ?? 0, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-filter mr-2"></i>Raadi & Kala Sooc Deynta
        </h3>
        <form method="GET" action="<?= url('/debts') ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Raadi</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                           placeholder="Lambarka deynta, magaca macmiilka..."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Xaalada</label>
                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Dhammaan Xaaladaha</option>
                        <option value="unpaid" <?= ($filters['status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Aan la bixin</option>
                        <option value="partially_paid" <?= ($filters['status'] ?? '') === 'partially_paid' ? 'selected' : '' ?>>Qayb la bixiyay</option>
                        <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>La bixiyay</option>
                        <option value="overdue" <?= ($filters['status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Dhacay</option>
                    </select>
                </div>
                <div>
                    <label for="customer" class="block text-sm font-medium text-gray-700">Macmiilka</label>
                    <select id="customer" name="customer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Dhammaan Macaamiisha</option>
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>" <?= ($filters['customer_id'] ?? '') == $customer['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($customer['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Raadi
                    </button>
                    <?php if (!empty(array_filter($filters ?? []))): ?>
                        <a href="<?= url('/debts') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i>Nadiifi
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Debts Table -->
    <?php if (empty($debts)): ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="mb-4">
                <i class="fas fa-file-invoice text-4xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Deyn ma jiro</h3>
            <p class="text-gray-500 mb-4">Ku bilow diiwaangelinta deynta macmiilka koowaad ama ku hagaaji sharuudaha raadinta.</p>
            <?php if (isAdmin() || isStaff()): ?>
                <a href="<?= url('/debts/create') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Diiwaan geli Deynta Kowaad
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-list mr-2"></i>Liiska Deynta 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 ml-2"><?= count($debts) ?></span>
                </h3>
                <div class="relative">
                    <select onchange="window.location.href=this.value" class="appearance-none bg-white border border-gray-300 rounded-md px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="<?= url('/debts?sort=due_date') ?>">Ku kala sooc taariikhda</option>
                        <option value="<?= url('/debts?sort=amount') ?>">Ku kala sooc lacagta</option>
                        <option value="<?= url('/debts?sort=customer') ?>">Ku kala sooc macmiilka</option>
                        <option value="<?= url('/debts?sort=status') ?>">Ku kala sooc xaalada</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-2 top-3 text-gray-400 text-xs"></i>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-hashtag mr-1"></i>Faahfaahinta Deynta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-1"></i>Macmiilka
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-dollar-sign mr-1"></i>Lacagta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-1"></i>Taariikhda Bixinta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-1"></i>Xaalada
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-1"></i>Ficilada
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($debts as $debt): ?>
                            <?php
                            $isOverdue = strtotime($debt['due_date']) < time() && $debt['status'] !== 'paid';
                            $remainingAmount = $debt['remaining_amount'] ?? ($debt['original_amount'] - ($debt['paid_amount'] ?? 0));
                            $progressPercent = $debt['original_amount'] > 0 ? (($debt['original_amount'] - $remainingAmount) / $debt['original_amount']) * 100 : 0;
                            ?>
                            <tr class="hover:bg-gray-50 <?= $isOverdue ? 'bg-red-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 <?= $isOverdue ? 'bg-red-600' : 'bg-blue-600' ?> rounded-full flex items-center justify-center text-white">
                                                <i class="fas fa-file-invoice"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-blue-600">
                                                <a href="<?= url('/debts/' . $debt['id']) ?>" class="hover:text-blue-800">
                                                    <?= htmlspecialchars($debt['debt_number']) ?>
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                La sameeyay: <?= date('M j, Y', strtotime($debt['debt_date'])) ?>
                                            </div>
                                            <?php if (!empty($debt['description'])): ?>
                                                <div class="text-xs text-gray-400 mt-1" title="<?= htmlspecialchars($debt['description']) ?>">
                                                    <i class="fas fa-comment mr-1"></i>
                                                    <?= htmlspecialchars(substr($debt['description'], 0, 30)) ?>
                                                    <?= strlen($debt['description']) > 30 ? '...' : '' ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="<?= url('/customers/' . $debt['customer_id']) ?>" class="hover:text-blue-600">
                                            <?= htmlspecialchars($debt['customer_name']) ?>
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <i class="fas fa-id-badge mr-1"></i><?= htmlspecialchars($debt['customer_code']) ?>
                                    </div>
                                    <?php if (!empty($debt['customer_phone'])): ?>
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-phone mr-1"></i>
                                            <a href="tel:<?= htmlspecialchars($debt['customer_phone']) ?>" class="hover:text-blue-600">
                                                <?= htmlspecialchars($debt['customer_phone']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold <?= $remainingAmount > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                        $<?= number_format($remainingAmount, 2) ?>
                                    </div>
                                    <?php if ($remainingAmount < $debt['original_amount']): ?>
                                        <div class="text-sm text-gray-500">
                                            oo ka mid ah $<?= number_format($debt['original_amount'], 2) ?>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="<?= $progressPercent == 100 ? 'bg-green-500' : 'bg-yellow-500' ?> h-2 rounded-full" style="width: <?= $progressPercent ?>%"></div>
                                        </div>
                                        <div class="text-xs text-green-600 mt-1">
                                            <?= number_format($progressPercent, 1) ?>% la bixiyay
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= date('M j, Y', strtotime($debt['due_date'])) ?>
                                    </div>
                                    <?php if ($isOverdue): ?>
                                        <div class="text-sm text-red-600">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <?= ceil(abs((strtotime($debt['due_date']) - time()) / (24 * 60 * 60))) ?> maalin dhacay
                                        </div>
                                    <?php else: ?>
                                        <?php 
                                        $daysUntilDue = ceil((strtotime($debt['due_date']) - time()) / (24 * 60 * 60));
                                        if ($daysUntilDue <= 7 && $daysUntilDue > 0 && $debt['status'] !== 'paid'): 
                                        ?>
                                            <div class="text-sm text-orange-600">
                                                <i class="fas fa-clock mr-1"></i>
                                                Dhawaan <?= $daysUntilDue ?> maalin
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($isOverdue): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Dhacay
                                        </span>
                                    <?php elseif ($debt['status'] === 'paid'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>La bixiyay
                                        </span>
                                    <?php elseif ($debt['status'] === 'partially_paid'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Qayb la bixiyay
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-minus-circle mr-1"></i>Aan la bixin
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="<?= url('/debts/' . $debt['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900" 
                                           title="Eeg Faahfaahinta">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($debt['status'] !== 'paid' && (isAdmin() || isStaff())): ?>
                                            <button onclick="showPaymentModal(<?= $debt['id'] ?>, '<?= htmlspecialchars($debt['debt_number']) ?>', <?= $remainingAmount ?>)" 
                                                    class="text-green-600 hover:text-green-900" 
                                                    title="Ku dar Lacag bixin">
                                                <i class="fas fa-plus-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (isAdmin()): ?>
                                            <a href="<?= url('/debts/' . $debt['id'] . '/edit') ?>" 
                                               class="text-orange-600 hover:text-orange-900" 
                                               title="Wax ka beddel">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                            <?php foreach ($debts as $debt): ?>
                                <?php
                                $isOverdue = strtotime($debt['due_date']) < time() && $debt['status'] !== 'paid';
                                $statusColors = [
                                    'paid' => 'success',
                                    'partially_paid' => 'warning', 
                                    'unpaid' => 'secondary',
                                    'overdue' => 'danger'
                                ];
                                $statusColor = $isOverdue ? 'danger' : ($statusColors[$debt['status']] ?? 'secondary');
                                
                                $remainingAmount = $debt['remaining_amount'] ?? ($debt['original_amount'] - ($debt['paid_amount'] ?? 0));
                                $progressPercent = $debt['original_amount'] > 0 ? (($debt['original_amount'] - $remainingAmount) / $debt['original_amount']) * 100 : 0;
                                ?>
                                <tr class="<?= $isOverdue ? 'table-warning' : '' ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-<?= $statusColor ?>" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-file-invoice text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-primary">
                                                    <a href="<?= url('/debts/' . $debt['id']) ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($debt['debt_number']) ?>
                                                    </a>
                                                </div>
                                                <div class="small text-muted">
                                                    Created: <?= date('M j, Y', strtotime($debt['debt_date'])) ?>
                                                </div>
                                                <?php if (!empty($debt['description'])): ?>
                                                    <div class="small text-gray-600 mt-1" title="<?= htmlspecialchars($debt['description']) ?>">
                                                        <i class="fas fa-comment text-muted"></i> 
                                                        <?= htmlspecialchars(substr($debt['description'], 0, 30)) ?>
                                                        <?= strlen($debt['description']) > 30 ? '...' : '' ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-weight-bold">
                                                <a href="<?= url('/customers/' . $debt['customer_id']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($debt['customer_name']) ?>
                                                </a>
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas fa-id-badge"></i> <?= htmlspecialchars($debt['customer_code']) ?>
                                            </div>
                                            <?php if (!empty($debt['customer_phone'])): ?>
                                                <div class="small text-muted">
                                                    <i class="fas fa-phone"></i> 
                                                    <a href="tel:<?= htmlspecialchars($debt['customer_phone']) ?>" class="text-muted">
                                                        <?= htmlspecialchars($debt['customer_phone']) ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-weight-bold text-<?= $remainingAmount > 0 ? 'danger' : 'success' ?>">
                                                $<?= number_format($remainingAmount, 2) ?>
                                            </div>
                                            <?php if ($remainingAmount < $debt['original_amount']): ?>
                                                <div class="small text-muted">
                                                    of $<?= number_format($debt['original_amount'], 2) ?>
                                                </div>
                                                <div class="progress mt-1" style="height: 6px;">
                                                    <div class="progress-bar bg-<?= $progressPercent == 100 ? 'success' : 'warning' ?>" 
                                                         style="width: <?= $progressPercent ?>%"></div>
                                                </div>
                                                <div class="small text-success">
                                                    <?= number_format($progressPercent, 1) ?>% paid
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-weight-bold">
                                                <?= date('M j, Y', strtotime($debt['due_date'])) ?>
                                            </div>
                                            <?php if ($isOverdue): ?>
                                                <div class="small text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <?= ceil(abs((strtotime($debt['due_date']) - time()) / (24 * 60 * 60))) ?> days overdue
                                                </div>
                                            <?php else: ?>
                                                <?php 
                                                $daysUntilDue = ceil((strtotime($debt['due_date']) - time()) / (24 * 60 * 60));
                                                if ($daysUntilDue <= 7 && $daysUntilDue > 0 && $debt['status'] !== 'paid'): 
                                                ?>
                                                    <div class="small text-warning">
                                                        <i class="fas fa-clock"></i>
                                                        Due in <?= $daysUntilDue ?> days
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $statusColor ?> px-3 py-2">
                                            <?php if ($isOverdue): ?>
                                                Overdue
                                            <?php else: ?>
                                                <?= ucfirst(str_replace('_', ' ', $debt['status'])) ?>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?= url('/debts/' . $debt['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($debt['status'] !== 'paid' && (isAdmin() || isStaff())): ?>
                                                <button onclick="showPaymentModal(<?= $debt['id'] ?>, '<?= htmlspecialchars($debt['debt_number']) ?>', <?= $remainingAmount ?>)" 
                                                        class="btn btn-sm btn-outline-success" 
                                                        title="Add Payment">
                                                    <i class="fas fa-plus-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (isAdmin()): ?>
                                                <a href="<?= url('/debts/' . $debt['id'] . '/edit') ?>" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>