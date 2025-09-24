<?php include '../app/views/layouts/app.php'; ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar"></i> <?= htmlspecialchars($title) ?>
            </h1>
            <p class="text-muted mb-0">Manage customer debts and payment tracking</p>
        </div>
        <div class="btn-group">
            <?php if (isAdmin() || isStaff()): ?>
                <a href="<?= url('/debts/create') ?>" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Record New Debt
                </a>
            <?php endif; ?>
            <a href="<?= url('/debts/overdue') ?>" class="btn btn-warning shadow-sm">
                <i class="fas fa-exclamation-triangle fa-sm"></i> Overdue
            </a>
            <a href="<?= url('/debts/export') ?>" class="btn btn-success shadow-sm">
                <i class="fas fa-download fa-sm"></i> Export
            </a>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Debts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['total_debts'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Outstanding Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats['total_outstanding_amount'] ?? 0, 2) ?>
                            </div>
                            <div class="text-xs text-warning">
                                <?= number_format($stats['outstanding_debts'] ?? 0) ?> debts
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Debts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['overdue_debts'] ?? 0) ?>
                            </div>
                            <div class="text-xs text-danger">
                                $<?= number_format($stats['overdue_amount'] ?? 0, 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="<?= url('/debts/overdue') ?>" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-eye fa-sm"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Paid This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['paid_this_month'] ?? 0) ?>
                            </div>
                            <div class="text-xs text-success">
                                $<?= number_format($stats['paid_amount_this_month'] ?? 0, 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Search & Filter Debts
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= url('/debts') ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search" class="small font-weight-bold">Search</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                   placeholder="Debt number, customer name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="small font-weight-bold">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="unpaid" <?= ($filters['status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                <option value="partially_paid" <?= ($filters['status'] ?? '') === 'partially_paid' ? 'selected' : '' ?>>Partially Paid</option>
                                <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="overdue" <?= ($filters['status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer" class="small font-weight-bold">Customer</label>
                            <select id="customer" name="customer_id" class="form-control">
                                <option value="">All Customers</option>
                                <?php if (!empty($customers)): ?>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?= $customer['id'] ?>" <?= ($filters['customer_id'] ?? '') == $customer['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($customer['full_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="small font-weight-bold d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <?php if (!empty(array_filter($filters ?? []))): ?>
                                <a href="<?= url('/debts') ?>" class="btn btn-secondary btn-sm btn-block mt-1">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Debts Table -->
    <?php if (empty($debts)): ?>
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-file-invoice fa-4x text-gray-300"></i>
                </div>
                <h3 class="h4 text-gray-900 mb-2">No debts found</h3>
                <p class="text-muted mb-4">Start by recording your first customer debt or adjust your search filters.</p>
                <?php if (isAdmin() || isStaff()): ?>
                    <a href="<?= url('/debts/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Record First Debt
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list"></i> Debts List 
                            <span class="badge badge-secondary ml-2"><?= count($debts) ?></span>
                        </h6>
                    </div>
                    <div class="col-auto">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-sort"></i> Sort
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= url('/debts?sort=due_date') ?>">By Due Date</a>
                                <a class="dropdown-item" href="<?= url('/debts?sort=amount') ?>">By Amount</a>
                                <a class="dropdown-item" href="<?= url('/debts?sort=customer') ?>">By Customer</a>
                                <a class="dropdown-item" href="<?= url('/debts?sort=status') ?>">By Status</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">
                                    <i class="fas fa-hashtag"></i> Debt Details
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-user"></i> Customer
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-dollar-sign"></i> Amount
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-calendar"></i> Due Date
                                </th>
                                <th class="border-0">
                                    <i class="fas fa-info-circle"></i> Status
                                </th>
                                <th class="border-0 text-center">
                                    <i class="fas fa-cogs"></i> Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
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
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> 
                        to <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_items']) ?> 
                        of <?= $pagination['total_items'] ?> debts
                    </small>
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/debts?page=' . ($pagination['current_page'] - 1)) ?>">
                                    <i class="fas fa-angle-left"></i> Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                            <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('/debts?page=' . $i) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/debts?page=' . ($pagination['current_page'] + 1)) ?>">
                                    Next <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Enhanced Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-credit-card"></i> Record Payment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="paymentForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Debt Number</label>
                                <input type="text" id="modalDebtNumber" readonly class="form-control-plaintext bg-light p-2 rounded">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Outstanding Amount</label>
                                <div class="h5 text-danger">$<span id="modalMaxAmount"></span></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modalPaymentAmount" class="font-weight-bold">Payment Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" 
                                           name="payment_amount" 
                                           id="modalPaymentAmount"
                                           step="0.01" 
                                           min="0" 
                                           required 
                                           class="form-control">
                                </div>
                                <small class="text-muted">Enter amount to pay (maximum outstanding amount)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="paymentMethod" class="font-weight-bold">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="paymentMethod" class="form-control" required>
                                    <option value="">Select Method</option>
                                    <option value="cash">💵 Cash</option>
                                    <option value="credit">💳 Credit Card</option>
                                    <option value="debit">💳 Debit Card</option>
                                    <option value="bank_transfer">🏦 Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="paymentNotes" class="font-weight-bold">Payment Notes</label>
                        <textarea name="notes" 
                                  id="paymentNotes"
                                  rows="3" 
                                  class="form-control"
                                  placeholder="Optional notes about this payment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showPaymentModal(debtId, debtNumber, remainingAmount) {
    document.getElementById('modalDebtNumber').value = debtNumber;
    document.getElementById('modalMaxAmount').textContent = remainingAmount.toFixed(2);
    document.getElementById('modalPaymentAmount').max = remainingAmount;
    document.getElementById('modalPaymentAmount').value = remainingAmount; // Pre-fill with full amount
    document.getElementById('paymentForm').action = '<?= url('/debts') ?>/' + debtId + '/payment';
    $('#paymentModal').modal('show');
}

// Form validation
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('modalPaymentAmount').value);
    const maxAmount = parseFloat(document.getElementById('modalMaxAmount').textContent);
    
    if (amount > maxAmount) {
        e.preventDefault();
        alert('Payment amount cannot exceed the outstanding debt amount.');
        return false;
    }
    
    if (amount <= 0) {
        e.preventDefault();
        alert('Payment amount must be greater than zero.');
        return false;
    }
});

// Auto-format currency input
document.getElementById('modalPaymentAmount').addEventListener('input', function() {
    let value = this.value;
    if (value.includes('.')) {
        let parts = value.split('.');
        if (parts[1].length > 2) {
            this.value = parts[0] + '.' + parts[1].substring(0, 2);
        }
    }
});
</script>