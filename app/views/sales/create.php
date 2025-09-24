<?php include '../app/views/layouts/app.php'; ?>
<?php
$title = $title ?? 'Iib Cusub';
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($title) ?></h1>
                <a href="<?= url('/sales') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Ku noqo Iibka
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

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Macluumaadka Iibka Cusub</h6>
                </div>
                <div class="card-body">
                    <form action="<?= url('/sales') ?>" method="POST" id="saleForm">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        
                        <!-- Customer Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id">Macmiilka (Ikhtiyaari)</label>
                                    <select class="form-control" id="customer_id" name="customer_id">
                                        <option value="">Macmiil Soo Gala</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['id'] ?>" 
                                                <?= ($selectedCustomer && $selectedCustomer['id'] == $customer['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($customer['customer_code'] . ' - ' . $customer['full_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_date">Taariikhda Iibka</label>
                                    <input type="datetime-local" class="form-control" id="sale_date" name="sale_date" 
                                           value="<?= date('Y-m-d\TH:i') ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Items -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Alaabta la Iibinayo</h6>
                            </div>
                            <div class="card-body">
                                <div id="saleItems">
                                    <div class="sale-item-row row mb-3">
                                        <div class="col-md-4">
                                            <label>Alaabta</label>
                                            <select class="form-control product-select" name="items[0][product_id]" required>
                                                <option value="">Dooro Alaabta</option>
                                                <?php foreach ($products as $product): ?>
                                                    <option value="<?= $product['id'] ?>" 
                                                            data-price="<?= $product['price'] ?>" 
                                                            data-stock="<?= $product['quantity_in_stock'] ?>">
                                                        <?= htmlspecialchars($product['sku'] . ' - ' . $product['name']) ?> 
                                                        (Bakhaar: <?= $product['quantity_in_stock'] ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Tirada</label>
                                            <input type="number" class="form-control item-quantity" 
                                                   name="items[0][quantity]" min="1" value="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Qiimaha Hal Shay</label>
                                            <input type="number" class="form-control item-price" 
                                                   name="items[0][unit_price]" step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Wadarta</label>
                                            <input type="text" class="form-control item-total" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm d-block remove-item" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-success btn-sm" id="addItem">
                                    <i class="fas fa-plus"></i> Ku dar Alaab
                                </button>
                            </div>
                        </div>

                        <!-- Sale Totals -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <!-- Payment Information -->
                                <div class="form-group">
                                    <label for="payment_method">Habka Lacag Bixinta</label>
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        <option value="cash">Lacag Caddaan</option>
                                        <option value="credit">Kaarka Lacag Deynta</option>
                                        <option value="debit">Kaarka Lacag Bixinta</option>
                                        <option value="bank_transfer">Wareejinta Bangiga</option>
                                        <option value="debt">Deyn</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="payment_status">Xaalada Lacag Bixinta</label>
                                    <select class="form-control" id="payment_status" name="payment_status" required>
                                        <option value="paid">La Bixiyay</option>
                                        <option value="partial">Qayb</option>
                                        <option value="pending">La Sugayo</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Faallooyinka (Ikhtiyaari)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Totals -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Hoosaadka:</strong></div>
                                            <div class="col-6 text-right">
                                                <span id="displaySubtotal">$0.00</span>
                                                <input type="hidden" name="subtotal" id="subtotal" value="0">
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <label for="tax_amount">Cashuurta:</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control" id="tax_amount" 
                                                       name="tax_amount" step="0.01" min="0" value="0">
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <label for="discount_amount">Dhimista:</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control" id="discount_amount" 
                                                       name="discount_amount" step="0.01" min="0" value="0">
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        <div class="row mb-2">
                                            <div class="col-6"><strong>Wadarta Guud:</strong></div>
                                            <div class="col-6 text-right">
                                                <strong><span id="displayTotal">$0.00</span></strong>
                                                <input type="hidden" name="total_amount" id="total_amount" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Diiwaan geli Iibka
                                </button>
                                <a href="<?= url('/sales') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Jooji
                                </a>
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
    let itemIndex = 1;
    
    // Add new item row
    document.getElementById('addItem').addEventListener('click', function() {
        const itemsContainer = document.getElementById('saleItems');
        const newRow = createItemRow(itemIndex);
        itemsContainer.appendChild(newRow);
        itemIndex++;
        updateRemoveButtons();
    });
    
    // Create new item row
    function createItemRow(index) {
        const row = document.createElement('div');
        row.className = 'sale-item-row row mb-3';
        row.innerHTML = `
            <div class="col-md-4">
                <select class="form-control product-select" name="items[${index}][product_id]" required>
                    <option value="">Select Product</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>" 
                                data-price="<?= $product['price'] ?>" 
                                data-stock="<?= $product['quantity_in_stock'] ?>">
                            <?= htmlspecialchars($product['sku'] . ' - ' . $product['name']) ?> 
                            (Stock: <?= $product['quantity_in_stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control item-quantity" 
                       name="items[${index}][quantity]" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control item-price" 
                       name="items[${index}][unit_price]" step="0.01" min="0" required>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control item-total" readonly>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        // Add event listeners to the new row
        addRowEventListeners(row);
        return row;
    }
    
    // Add event listeners to a row
    function addRowEventListeners(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.item-quantity');
        const priceInput = row.querySelector('.item-price');
        const removeButton = row.querySelector('.remove-item');
        
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                priceInput.value = selectedOption.dataset.price;
                updateRowTotal(row);
            }
        });
        
        quantityInput.addEventListener('input', function() {
            updateRowTotal(row);
        });
        
        priceInput.addEventListener('input', function() {
            updateRowTotal(row);
        });
        
        removeButton.addEventListener('click', function() {
            row.remove();
            updateRemoveButtons();
            updateTotals();
        });
    }
    
    // Add event listeners to existing rows
    document.querySelectorAll('.sale-item-row').forEach(addRowEventListeners);
    
    // Update row total
    function updateRowTotal(row) {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const total = quantity * price;
        row.querySelector('.item-total').value = total.toFixed(2);
        updateTotals();
    }
    
    // Update overall totals
    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-total').forEach(function(input) {
            subtotal += parseFloat(input.value) || 0;
        });
        
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const total = subtotal + tax - discount;
        
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('displaySubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('total_amount').value = total.toFixed(2);
        document.getElementById('displayTotal').textContent = '$' + total.toFixed(2);
    }
    
    // Update remove button states
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.sale-item-row');
        rows.forEach(function(row, index) {
            const removeButton = row.querySelector('.remove-item');
            removeButton.disabled = rows.length <= 1;
        });
    }
    
    // Add event listeners for tax and discount
    document.getElementById('tax_amount').addEventListener('input', updateTotals);
    document.getElementById('discount_amount').addEventListener('input', updateTotals);
    
    // Form validation
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.sale-item-row');
        let hasValidItems = false;
        
        rows.forEach(function(row) {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.item-quantity');
            const priceInput = row.querySelector('.item-price');
            
            if (productSelect.value && quantityInput.value && priceInput.value) {
                hasValidItems = true;
            }
        });
        
        if (!hasValidItems) {
            e.preventDefault();
            alert('Please add at least one valid item to the sale.');
            return;
        }
        
        const total = parseFloat(document.getElementById('total_amount').value);
        if (total <= 0) {
            e.preventDefault();
            alert('Sale total must be greater than zero.');
            return;
        }
    });
    
    // Initialize calculations
    updateTotals();
});
</script>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>