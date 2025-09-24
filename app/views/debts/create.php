<?php include '../app/views/layouts/app.php'; ?>

<div class="container">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice"></i> <?= htmlspecialchars($title) ?>
            </h1>
            <p class="text-muted mb-0">Record a new customer debt</p>
        </div>
        <a href="<?= url('/debts') ?>" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Debts
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Debt Form -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 text-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-file-invoice"></i> Debt Information
                            </h6>
                        </div>
                <div class="card-body">
                    <form method="POST" action="<?= url('/debts') ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        
                        <!-- Flash Messages -->
                        <?php if (hasFlash('error')): ?>
                            <div class="alert alert-danger">
                                <?= getFlash('error') ?>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <!-- Customer Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id" class="font-weight-bold">Customer <span class="text-danger">*</span></label>
                                    <select id="customer_id" name="customer_id" class="form-control <?= hasErrors('customer_id') ? 'is-invalid' : '' ?>" required>
                                        <option value="">Select a customer...</option>
                                        <?php if (!empty($customers)): ?>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= $customer['id'] ?>" 
                                                        <?= ($selectedCustomer && $selectedCustomer['id'] == $customer['id']) ? 'selected' : '' ?>
                                                        data-phone="<?= htmlspecialchars($customer['phone']) ?>"
                                                        data-email="<?= htmlspecialchars($customer['email'] ?? '') ?>"
                                                        data-credit-limit="<?= $customer['credit_limit'] ?? 0 ?>"
                                                        data-current-balance="<?= $customer['current_balance'] ?? 0 ?>">
                                                    <?= htmlspecialchars($customer['customer_code'] . ' - ' . $customer['full_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <?php if (hasErrors('customer_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('customer_id')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Debt Number -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="debt_number" class="font-weight-bold">Debt Number</label>
                                    <input type="text" 
                                           id="debt_number" 
                                           name="debt_number" 
                                           class="form-control <?= hasErrors('debt_number') ? 'is-invalid' : '' ?>"
                                           value="<?= old('debt_number') ?>"
                                           placeholder="Auto-generated if empty">
                                    <small class="form-text text-muted">Leave empty to auto-generate</small>
                                    <?php if (hasErrors('debt_number')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('debt_number')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Amount -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="original_amount" class="font-weight-bold">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" 
                                               id="original_amount" 
                                               name="original_amount" 
                                               class="form-control <?= hasErrors('original_amount') ? 'is-invalid' : '' ?>"
                                               value="<?= old('original_amount') ?>"
                                               step="0.01" 
                                               min="0.01"
                                               required>
                                        <?php if (hasErrors('original_amount')): ?>
                                            <div class="invalid-feedback">
                                                <?= getErrors('original_amount')[0] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="due_date" class="font-weight-bold">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           id="due_date" 
                                           name="due_date" 
                                           class="form-control <?= hasErrors('due_date') ? 'is-invalid' : '' ?>"
                                           value="<?= old('due_date', date('Y-m-d', strtotime('+30 days'))) ?>"
                                           min="<?= date('Y-m-d') ?>"
                                           required>
                                    <?php if (hasErrors('due_date')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('due_date')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Sale Reference -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sale_id" class="font-weight-bold">Related Sale ID</label>
                                    <input type="number" 
                                           id="sale_id" 
                                           name="sale_id" 
                                           class="form-control <?= hasErrors('sale_id') ? 'is-invalid' : '' ?>"
                                           value="<?= old('sale_id') ?>"
                                           placeholder="Optional">
                                    <small class="form-text text-muted">Reference to related sale</small>
                                    <?php if (hasErrors('sale_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('sale_id')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-control <?= hasErrors('description') ? 'is-invalid' : '' ?>"
                                      rows="2"
                                      placeholder="Brief description of the debt..."><?= old('description') ?></textarea>
                            <?php if (hasErrors('description')): ?>
                                <div class="invalid-feedback">
                                    <?= getErrors('description')[0] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label for="notes" class="font-weight-bold">Notes</label>
                            <textarea id="notes" 
                                      name="notes" 
                                      class="form-control <?= hasErrors('notes') ? 'is-invalid' : '' ?>"
                                      rows="2"
                                      placeholder="Additional notes or payment terms..."><?= old('notes') ?></textarea>
                            <?php if (hasErrors('notes')): ?>
                                <div class="invalid-feedback">
                                    <?= getErrors('notes')[0] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= url('/debts') ?>'">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Record Debt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

                <div class="col-lg-4">
                    <!-- Customer Info Panel -->
                    <div id="customerInfoPanel" class="card shadow mb-4" style="display: none;">
                        <div class="card-header py-3 text-center">
                            <h6 class="m-0 font-weight-bold text-info">
                                <i class="fas fa-user"></i> Customer Information
                            </h6>
                        </div>
                <div class="card-body">
                    <div id="customerDetails">
                        <div class="row">
                            <div class="col-6">
                                <small class="font-weight-bold text-muted">Phone:</small>
                                <div id="customerPhone" class="mb-2">-</div>
                            </div>
                            <div class="col-6">
                                <small class="font-weight-bold text-muted">Email:</small>
                                <div id="customerEmail" class="mb-2">-</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <small class="font-weight-bold text-muted">Credit Limit:</small>
                                <div id="customerCreditLimit" class="text-success">$0.00</div>
                            </div>
                            <div class="col-6">
                                <small class="font-weight-bold text-muted">Current Balance:</small>
                                <div id="customerBalance" class="text-danger">$0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                    <!-- Credit Warning -->
                    <div id="creditWarning" class="card border-warning mb-4" style="display: none;">
                        <div class="card-header bg-warning text-white py-2 text-center">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-exclamation-triangle"></i> Credit Limit Warning
                            </h6>
                        </div>
                <div class="card-body">
                    <p class="text-warning mb-2">This debt will exceed the customer's credit limit.</p>
                    <div id="creditDetails" class="small text-muted"></div>
                </div>
            </div>

                    <!-- Quick Tips -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 text-center">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-lightbulb"></i> Quick Tips
                            </h6>
                        </div>
                <div class="card-body">
                    <ul class="small text-muted mb-0">
                        <li>Debt numbers are auto-generated if left empty</li>
                        <li>Due date defaults to 30 days from today</li>
                        <li>Link to sales for better tracking</li>
                        <li>Monitor customer credit limits</li>
                        <li>Add detailed notes for payment terms</li>
                    </ul>
                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');
    const customerPanel = document.getElementById('customerInfoPanel');
    const customerPhone = document.getElementById('customerPhone');
    const customerEmail = document.getElementById('customerEmail');
    const customerCreditLimit = document.getElementById('customerCreditLimit');
    const customerBalance = document.getElementById('customerBalance');
    const originalAmount = document.getElementById('original_amount');
    const creditWarning = document.getElementById('creditWarning');
    const creditDetails = document.getElementById('creditDetails');

    function updateCustomerInfo() {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        
        if (selectedOption.value) {
            customerPhone.textContent = selectedOption.dataset.phone || '-';
            customerEmail.textContent = selectedOption.dataset.email || '-';
            customerCreditLimit.textContent = '$' + parseFloat(selectedOption.dataset.creditLimit || 0).toFixed(2);
            customerBalance.textContent = '$' + parseFloat(selectedOption.dataset.currentBalance || 0).toFixed(2);
            customerPanel.style.display = 'block';
            
            checkCreditLimit();
        } else {
            customerPanel.style.display = 'none';
            creditWarning.style.display = 'none';
        }
    }

    function checkCreditLimit() {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        const amount = parseFloat(originalAmount.value) || 0;
        
        if (selectedOption.value && amount > 0) {
            const creditLimit = parseFloat(selectedOption.dataset.creditLimit || 0);
            const currentBalance = parseFloat(selectedOption.dataset.currentBalance || 0);
            const newBalance = currentBalance + amount;
            
            if (newBalance > creditLimit && creditLimit > 0) {
                creditDetails.innerHTML = `
                    <strong>Current Balance:</strong> $${currentBalance.toFixed(2)}<br>
                    <strong>New Debt:</strong> $${amount.toFixed(2)}<br>
                    <strong>New Total:</strong> $${newBalance.toFixed(2)}<br>
                    <strong>Credit Limit:</strong> $${creditLimit.toFixed(2)}<br>
                    <strong>Over Limit:</strong> $${(newBalance - creditLimit).toFixed(2)}
                `;
                creditWarning.style.display = 'block';
            } else {
                creditWarning.style.display = 'none';
            }
        } else {
            creditWarning.style.display = 'none';
        }
    }

    customerSelect.addEventListener('change', updateCustomerInfo);
    originalAmount.addEventListener('input', checkCreditLimit);

    // Initialize if customer is pre-selected
    if (customerSelect.value) {
        updateCustomerInfo();
    }

    // Auto-focus amount after customer selection
    customerSelect.addEventListener('change', function() {
        if (this.value) {
            setTimeout(() => originalAmount.focus(), 100);
        }
    });

    // Auto-format currency inputs
    originalAmount.addEventListener('input', function() {
        let value = this.value;
        if (value.includes('.')) {
            let parts = value.split('.');
            if (parts[1].length > 2) {
                this.value = parts[0] + '.' + parts[1].substring(0, 2);
            }
        }
    });
});
</script>