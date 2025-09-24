<?php include '../app/views/layouts/app.php'; ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus"></i> <?= htmlspecialchars($title) ?>
        </h1>
        <a href="<?= url('/users') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Users
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- User Creation Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= url('/users') ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= hasErrors('username') ? 'is-invalid' : '' ?>" 
                                           id="username" name="username" value="<?= old('username') ?>" required>
                                    <?php if (hasErrors('username')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('username')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Must be unique and contain only letters, numbers, and underscores</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= hasErrors('email') ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" value="<?= old('email') ?>" required>
                                    <?php if (hasErrors('email')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('email')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= hasErrors('full_name') ? 'is-invalid' : '' ?>" 
                                           id="full_name" name="full_name" value="<?= old('full_name') ?>" required>
                                    <?php if (hasErrors('full_name')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('full_name')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="role">Role <span class="text-danger">*</span></label>
                                    <select class="form-control <?= hasErrors('role') ? 'is-invalid' : '' ?>" 
                                            id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>Staff</option>
                                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                                    </select>
                                    <?php if (hasErrors('role')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('role')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control <?= hasErrors('phone') ? 'is-invalid' : '' ?>" 
                                   id="phone" name="phone" value="<?= old('phone') ?>">
                            <?php if (hasErrors('phone')): ?>
                                <div class="invalid-feedback">
                                    <?= getErrors('phone')[0] ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Optional - Include country code if international</small>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-primary mb-3">
                            <i class="fas fa-lock"></i> Password Setup
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= hasErrors('password') ? 'is-invalid' : '' ?>" 
                                           id="password" name="password" required>
                                    <?php if (hasErrors('password')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('password')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirm">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= hasErrors('password_confirm') ? 'is-invalid' : '' ?>" 
                                           id="password_confirm" name="password_confirm" required>
                                    <?php if (hasErrors('password_confirm')): ?>
                                        <div class="invalid-feedback">
                                            <?= getErrors('password_confirm')[0] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= url('/users') ?>'">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Help Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle"></i> User Roles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-danger">
                            <i class="fas fa-user-shield"></i> Administrator
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li>Full system access</li>
                            <li>Manage all users</li>
                            <li>View all reports</li>
                            <li>System configuration</li>
                            <li>Data export/import</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h6 class="text-info">
                            <i class="fas fa-user-tie"></i> Staff
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li>Record sales</li>
                            <li>Manage customers</li>
                            <li>Update inventory</li>
                            <li>View assigned reports</li>
                            <li>Process payments</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Security Guidelines -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-shield-alt"></i> Security Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="small text-muted mb-0">
                        <li>Choose a strong, unique password</li>
                        <li>Username should be professional</li>
                        <li>Email will be used for notifications</li>
                        <li>Phone number helps with account recovery</li>
                        <li>Admin roles should be limited</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('password_confirm').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

// Username validation
document.getElementById('username').addEventListener('input', function() {
    const username = this.value;
    const pattern = /^[a-zA-Z0-9_]+$/;
    
    if (!pattern.test(username) && username.length > 0) {
        this.setCustomValidity('Username can only contain letters, numbers, and underscores');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});
</script>