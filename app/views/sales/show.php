<?php include '../app/views/layouts/app.php'; ?>
<?php
$title = $title ?? 'Sale Details';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($title) ?></h1>
                <div>
                    <a href="<?= url('/sales') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sales
                    </a>
                    <?php if (isAdmin()): ?>
                        <a href="<?= url('/sales/' . $sale['id'] . '/edit') ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Sale
                        </a>
                    <?php endif; ?>
                </div>
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

            <div class="row">
                <!-- Sale Information -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Sale Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Sale Number:</strong></td>
                                            <td><?= htmlspecialchars($sale['sale_number']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sale Date:</strong></td>
                                            <td><?= date('M j, Y g:i A', strtotime($sale['sale_date'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $sale['payment_method'] === 'cash' ? 'success' : 
                                                    ($sale['payment_method'] === 'debt' ? 'warning' : 'info') 
                                                ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $sale['payment_method'])) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Status:</strong></td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $sale['payment_status'] === 'paid' ? 'success' : 
                                                    ($sale['payment_status'] === 'partial' ? 'warning' : 'danger') 
                                                ?>">
                                                    <?= ucfirst($sale['payment_status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Created By:</strong></td>
                                            <td><?= htmlspecialchars($sale['created_by_name'] ?? 'Unknown') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created At:</strong></td>
                                            <td><?= date('M j, Y g:i A', strtotime($sale['created_at'])) ?></td>
                                        </tr>
                                        <?php if ($sale['updated_at'] !== $sale['created_at']): ?>
                                            <tr>
                                                <td><strong>Last Updated:</strong></td>
                                                <td><?= date('M j, Y g:i A', strtotime($sale['updated_at'])) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($sale['notes'])): ?>
                                            <tr>
                                                <td><strong>Notes:</strong></td>
                                                <td><?= nl2br(htmlspecialchars($sale['notes'])) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Items -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Sale Items</h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($items)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No items found for this sale.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>SKU</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <code><?= htmlspecialchars($item['product_sku']) ?></code>
                                                    </td>
                                                    <td>
                                                        <?= number_format($item['quantity']) ?> 
                                                        <?= htmlspecialchars($item['product_unit'] ?? 'pcs') ?>
                                                    </td>
                                                    <td>
                                                        $<?= number_format($item['unit_price'], 2) ?>
                                                    </td>
                                                    <td>
                                                        <strong>$<?= number_format($item['total_price'], 2) ?></strong>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Customer Information & Totals -->
                <div class="col-md-4">
                    <!-- Customer Information -->
                    <?php if ($sale['customer_name']): ?>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Code:</strong></td>
                                        <td><code><?= htmlspecialchars($sale['customer_code']) ?></code></td>
                                    </tr>
                                    <?php if ($sale['customer_phone']): ?>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td><?= htmlspecialchars($sale['customer_phone']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($sale['customer_email']): ?>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><?= htmlspecialchars($sale['customer_email']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($sale['customer_address']): ?>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td><?= nl2br(htmlspecialchars($sale['customer_address'])) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                                <div class="text-center">
                                    <a href="<?= url('/customers/' . $sale['customer_id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-user"></i> View Customer
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-user-slash fa-3x text-gray-300 mb-3"></i>
                                <p class="text-gray-500">Walk-in Customer</p>
                                <small class="text-muted">No customer information recorded</small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Sale Totals -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Sale Totals</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td class="text-right">$<?= number_format($sale['subtotal'], 2) ?></td>
                                </tr>
                                <?php if ($sale['tax_amount'] > 0): ?>
                                    <tr>
                                        <td>Tax:</td>
                                        <td class="text-right">$<?= number_format($sale['tax_amount'], 2) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($sale['discount_amount'] > 0): ?>
                                    <tr>
                                        <td>Discount:</td>
                                        <td class="text-right text-success">-$<?= number_format($sale['discount_amount'], 2) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="border-top">
                                    <td><strong>Total Amount:</strong></td>
                                    <td class="text-right"><strong class="text-primary">$<?= number_format($sale['total_amount'], 2) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-info btn-block" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Receipt
                                </button>
                                
                                <?php if ($sale['payment_method'] === 'debt' && $sale['customer_id']): ?>
                                    <a href="<?= url('/debts/create?customer_id=' . $sale['customer_id'] . '&sale_id=' . $sale['id']) ?>" 
                                       class="btn btn-warning btn-block">
                                        <i class="fas fa-money-bill-wave"></i> View Debt
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?= url('/sales/create?customer_id=' . ($sale['customer_id'] ?? '')) ?>" 
                                   class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> New Sale
                                </a>
                                
                                <?php if (isAdmin()): ?>
                                    <hr>
                                    <button type="button" class="btn btn-danger btn-block" 
                                            onclick="deleteSale(<?= $sale['id'] ?>, '<?= htmlspecialchars($sale['sale_number']) ?>')">
                                        <i class="fas fa-trash"></i> Delete Sale
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<?php if (isAdmin()): ?>
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
<?php endif; ?>

<style>
@media print {
    .btn, .card-header, .modal, nav, .d-flex .btn {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-body {
        padding: 0 !important;
    }
    
    body {
        background: white !important;
    }
}
</style>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>