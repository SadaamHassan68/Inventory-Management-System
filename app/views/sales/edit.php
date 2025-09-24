<?php include '../app/views/layouts/app.php'; ?>
<?php
$title = $title ?? 'Edit Sale';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($title) ?></h1>
                <div>
                    <a href="<?= url('/sales/' . $sale['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sale
                    </a>
                    <a href="<?= url('/sales') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> All Sales
                    </a>
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

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Sale Information</h6>
                </div>
                <div class="card-body">
                    <form action="<?= url('/sales/' . $sale['id']) ?>" method="POST">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="PUT">
                        
                        <!-- Basic Sale Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_number">Sale Number</label>
                                    <input type="text" class="form-control" id="sale_number" 
                                           value="<?= htmlspecialchars($sale['sale_number']) ?>" readonly>
                                    <small class="form-text text-muted">Sale number cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_date">Sale Date</label>
                                    <input type="datetime-local" class="form-control" id="sale_date" name="sale_date" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($sale['sale_date'])) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id">Customer</label>
                                    <select class="form-control" id="customer_id" name="customer_id">
                                        <option value="">Walk-in Customer</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['id'] ?>" 
                                                <?= ($sale['customer_id'] == $customer['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($customer['customer_code'] . ' - ' . $customer['full_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Created By</label>
                                    <input type="text" class="form-control" 
                                           value="<?= htmlspecialchars($sale['created_by_name'] ?? 'Unknown') ?>" readonly>
                                    <small class="form-text text-muted">Sale creator cannot be changed</small>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        <option value="cash" <?= $sale['payment_method'] === 'cash' ? 'selected' : '' ?>>Cash</option>
                                        <option value="credit" <?= $sale['payment_method'] === 'credit' ? 'selected' : '' ?>>Credit Card</option>
                                        <option value="debit" <?= $sale['payment_method'] === 'debit' ? 'selected' : '' ?>>Debit Card</option>
                                        <option value="bank_transfer" <?= $sale['payment_method'] === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                        <option value="debt" <?= $sale['payment_method'] === 'debt' ? 'selected' : '' ?>>Credit (Debt)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select class="form-control" id="payment_status" name="payment_status" required>
                                        <option value="paid" <?= $sale['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="partial" <?= $sale['payment_status'] === 'partial' ? 'selected' : '' ?>>Partial</option>
                                        <option value="pending" <?= $sale['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Amounts -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="subtotal">Subtotal</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="subtotal" name="subtotal" 
                                               step="0.01" min="0" value="<?= $sale['subtotal'] ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tax_amount">Tax Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="tax_amount" name="tax_amount" 
                                               step="0.01" min="0" value="<?= $sale['tax_amount'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discount_amount">Discount Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                               step="0.01" min="0" value="<?= $sale['discount_amount'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total_amount">Total Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="total_amount" name="total_amount" 
                                               step="0.01" min="0" value="<?= $sale['total_amount'] ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($sale['notes'] ?? '') ?></textarea>
                        </div>

                        <!-- Sale Items Display (Read-only) -->
                        <?php if (!empty($items)): ?>
                            <div class="form-group">
                                <label>Sale Items</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="thead-light">
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
                                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                    <td><code><?= htmlspecialchars($item['product_sku']) ?></code></td>
                                                    <td><?= number_format($item['quantity']) ?> <?= htmlspecialchars($item['product_unit'] ?? 'pcs') ?></td>
                                                    <td>$<?= number_format($item['unit_price'], 2) ?></td>
                                                    <td>$<?= number_format($item['total_price'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Sale items cannot be modified after creation. To change items, create a new sale.
                                </small>
                            </div>
                        <?php endif; ?>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Sale
                                </button>
                                <a href="<?= url('/sales/' . $sale['id']) ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                
                                <div class="float-right">
                                    <small class="text-muted">
                                        Last updated: <?= date('M j, Y g:i A', strtotime($sale['updated_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate total when subtotal, tax, or discount changes
    function updateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const total = subtotal + tax - discount;
        
        document.getElementById('total_amount').value = total.toFixed(2);
    }
    
    document.getElementById('subtotal').addEventListener('input', updateTotal);
    document.getElementById('tax_amount').addEventListener('input', updateTotal);
    document.getElementById('discount_amount').addEventListener('input', updateTotal);
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const total = parseFloat(document.getElementById('total_amount').value);
        if (total <= 0) {
            e.preventDefault();
            alert('Sale total must be greater than zero.');
            return;
        }
        
        const subtotal = parseFloat(document.getElementById('subtotal').value);
        if (subtotal <= 0) {
            e.preventDefault();
            alert('Subtotal must be greater than zero.');
            return;
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>