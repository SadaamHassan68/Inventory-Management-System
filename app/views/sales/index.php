<?php include '../app/views/layouts/app.php'; ?>
<?php
$title = $title ?? 'Sales';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($title) ?></h1>
                <a href="<?= url('/sales/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Sale
                </a>
            </div>

            <?php if ($error = getFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($success = getFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Sales
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= number_format($stats['total_sales']) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
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
                                        Total Revenue
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?= number_format($stats['total_amount'], 2) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Sales Today
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= number_format($stats['sales_today']) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                                        Average Sale
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?= number_format($stats['average_sale'], 2) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Sales</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('/sales') ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="<?= htmlspecialchars($filters['search']) ?>" 
                                           placeholder="Sale number, customer name...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select class="form-control" id="payment_status" name="payment_status">
                                        <option value="">All Status</option>
                                        <option value="paid" <?= $filters['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="partial" <?= $filters['payment_status'] === 'partial' ? 'selected' : '' ?>>Partial</option>
                                        <option value="pending" <?= $filters['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="customer_id">Customer</label>
                                    <select class="form-control" id="customer_id" name="customer_id">
                                        <option value="">All Customers</option>
                                        <!-- Note: You might want to load customers for filtering -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales List</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($sales)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No sales found.</p>
                            <a href="<?= url('/sales/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Record First Sale
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Sale #</th>
                                        <th>Customer</th>
                                        <th>Sale Date</th>
                                        <th>Total Amount</th>
                                        <th>Payment Method</th>
                                        <th>Payment Status</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sales as $sale): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= url('/sales/' . $sale['id']) ?>" class="text-primary">
                                                    <?= htmlspecialchars($sale['sale_number']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($sale['customer_name']): ?>
                                                    <?= htmlspecialchars($sale['customer_code'] . ' - ' . $sale['customer_name']) ?>
                                                    <?php if ($sale['customer_phone']): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($sale['customer_phone']) ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Walk-in Customer</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= date('M j, Y g:i A', strtotime($sale['sale_date'])) ?>
                                            </td>
                                            <td>
                                                <strong>$<?= number_format($sale['total_amount'], 2) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $sale['payment_method'] === 'cash' ? 'success' : 
                                                    ($sale['payment_method'] === 'debt' ? 'warning' : 'info') 
                                                ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $sale['payment_method'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $sale['payment_status'] === 'paid' ? 'success' : 
                                                    ($sale['payment_status'] === 'partial' ? 'warning' : 'danger') 
                                                ?>">
                                                    <?= ucfirst($sale['payment_status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($sale['created_by_name'] ?? 'Unknown') ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= url('/sales/' . $sale['id']) ?>" 
                                                       class="btn btn-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if (isAdmin()): ?>
                                                        <a href="<?= url('/sales/' . $sale['id'] . '/edit') ?>" 
                                                           class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                onclick="deleteSale(<?= $sale['id'] ?>, '<?= htmlspecialchars($sale['sale_number']) ?>')" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pagination['total_pages'] > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php if ($pagination['current_page'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('/sales?page=' . ($pagination['current_page'] - 1) . '&' . http_build_query($filters)) ?>">
                                                Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= url('/sales?page=' . $i . '&' . http_build_query($filters)) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('/sales?page=' . ($pagination['current_page'] + 1) . '&' . http_build_query($filters)) ?>">
                                                Next
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>

                            <div class="text-center">
                                <small class="text-muted">
                                    Showing <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> to 
                                    <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_items']) ?> 
                                    of <?= $pagination['total_items'] ?> sales
                                </small>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete sale <strong id="deleteSaleNumber"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone and will restore the stock quantities.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Delete Sale</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteSale(saleId, saleNumber) {
    document.getElementById('deleteSaleNumber').textContent = saleNumber;
    document.getElementById('deleteForm').action = '<?= url('/sales/') ?>' + saleId;
    $('#deleteModal').modal('show');
}
</script>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>