<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventory Management System' ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap CSS for sales views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        .sidebar-transition {
            transition: margin-left 0.3s ease-in-out;
        }
        
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php if (isLoggedIn()): ?>
        <!-- Navigation -->
        <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <!-- Left side -->
                    <div class="flex items-center">
                        <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 lg:hidden">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex-shrink-0 flex items-center ml-4 lg:ml-0">
                            <h1 class="text-xl font-bold text-gray-800">
                                <i class="fas fa-warehouse text-blue-600"></i>
                                Inventory MS
                            </h1>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative dropdown">
                            <button class="text-gray-500 hover:text-gray-700 relative">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg hidden">
                                <div class="py-2">
                                    <div class="px-4 py-2 border-b">
                                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                                    </div>
                                    <a href="#" class="block px-4 py-2 hover:bg-gray-50">
                                        <div class="text-sm">
                                            <p class="text-red-600">Low Stock Alert</p>
                                            <p class="text-gray-600">5 products are running low on stock</p>
                                        </div>
                                    </a>
                                    <a href="#" class="block px-4 py-2 hover:bg-gray-50">
                                        <div class="text-sm">
                                            <p class="text-orange-600">Overdue Debt</p>
                                            <p class="text-gray-600">2 customers have overdue payments</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative dropdown">
                            <button class="flex items-center text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                                    <?= strtoupper(substr(auth()['full_name'], 0, 1)) ?>
                                </div>
                                <span class="ml-2 hidden md:block"><?= auth()['full_name'] ?></span>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                                <div class="py-2">
                                    <a href="<?= url('profile') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    <?php if (isAdmin()): ?>
                                        <a href="<?= url('settings') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-cog mr-2"></i> Settings
                                        </a>
                                    <?php endif; ?>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="<?= url('logout') ?>" class="block">
                                        <?= csrfField() ?>
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out mt-16">
            <div class="flex flex-col h-full">
                <nav class="flex-1 px-2 py-4 space-y-2">
                    <!-- Dashboard -->
                    <a href="<?= url('dashboard') ?>" class="flex items-center px-2 py-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>

                    <!-- Products -->
                    <div class="space-y-1">
                        <button class="flex items-center w-full px-2 py-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white" onclick="toggleSubmenu('products')">
                            <i class="fas fa-box mr-3"></i>
                            Products
                            <i class="fas fa-chevron-right ml-auto transform transition-transform" id="products-icon"></i>
                        </button>
                        <div id="products-menu" class="hidden ml-6 space-y-1">
                            <a href="<?= url('products') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">All Products</a>
                            <a href="<?= url('products/create') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Add Product</a>
                            <a href="<?= url('products/low-stock') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Low Stock</a>
                            <a href="<?= url('categories') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Categories</a>
                            <a href="<?= url('suppliers') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Suppliers</a>
                        </div>
                    </div>

                    <!-- Customers -->
                    <div class="space-y-1">
                        <button class="flex items-center w-full px-2 py-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white" onclick="toggleSubmenu('customers')">
                            <i class="fas fa-users mr-3"></i>
                            Customers
                            <i class="fas fa-chevron-right ml-auto transform transition-transform" id="customers-icon"></i>
                        </button>
                        <div id="customers-menu" class="hidden ml-6 space-y-1">
                            <a href="<?= url('customers') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">All Customers</a>
                            <a href="<?= url('customers/create') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Add Customer</a>
                        </div>
                    </div>

                    <!-- Sales -->
                    <div class="space-y-1">
                        <button class="flex items-center w-full px-2 py-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white" onclick="toggleSubmenu('sales')">
                            <i class="fas fa-shopping-cart mr-3"></i>
                            Sales
                            <i class="fas fa-chevron-right ml-auto transform transition-transform" id="sales-icon"></i>
                        </button>
                        <div id="sales-menu" class="hidden ml-6 space-y-1">
                            <a href="<?= url('sales') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">All Sales</a>
                            <a href="<?= url('sales/create') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">New Sale</a>
                        </div>
                    </div>

                    <!-- Debts -->
                    <div class="space-y-1">
                        <button class="flex items-center w-full px-2 py-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white" onclick="toggleSubmenu('debts')">
                            <i class="fas fa-credit-card mr-3"></i>
                            Debts
                            <i class="fas fa-chevron-right ml-auto transform transition-transform" id="debts-icon"></i>
                        </button>
                        <div id="debts-menu" class="hidden ml-6 space-y-1">
                            <a href="<?= url('debts') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">All Debts</a>
                            <a href="<?= url('debts/create') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Record Debt</a>
                            <a href="<?= url('debts/overdue') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Overdue</a>
                        </div>
                    </div>

                    <!-- Reports -->
                    <div class="space-y-1">
                        <button class="flex items-center w-full px-2 py-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white" onclick="toggleSubmenu('reports')">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Reports
                            <i class="fas fa-chevron-right ml-auto transform transition-transform" id="reports-icon"></i>
                        </button>
                        <div id="reports-menu" class="hidden ml-6 space-y-1">
                            <a href="<?= url('reports') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Overview</a>
                            <a href="<?= url('reports/sales') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Sales Report</a>
                            <a href="<?= url('reports/inventory') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Inventory Report</a>
                            <a href="<?= url('reports/debts') ?>" class="block px-2 py-1 text-sm text-gray-400 hover:text-white">Debt Report</a>
                        </div>
                    </div>

                    <?php if (isAdmin()): ?>
                        <!-- Admin Section -->
                        <div class="border-t border-gray-700 pt-4 mt-4">
                            <a href="<?= url('users') ?>" class="flex items-center px-2 py-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white">
                                <i class="fas fa-user-cog mr-3"></i>
                                User Management
                            </a>
                            <a href="<?= url('settings') ?>" class="flex items-center px-2 py-2 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white">
                                <i class="fas fa-cog mr-3"></i>
                                Settings
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div id="main-content" class="lg:ml-64 transition-all duration-300 ease-in-out mt-16">
    <?php else: ?>
        <!-- Public Layout -->
        <div class="min-h-screen">
    <?php endif; ?>

            <!-- Flash Messages -->
            <?php if (hasFlash('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 no-print">
                    <div class="flex">
                        <div class="py-1">
                            <i class="fas fa-check-circle mr-2"></i>
                        </div>
                        <div>
                            <p class="font-bold">Success!</p>
                            <p class="text-sm"><?= getFlash('success') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 no-print">
                    <div class="flex">
                        <div class="py-1">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                        </div>
                        <div>
                            <p class="font-bold">Error!</p>
                            <p class="text-sm"><?= getFlash('error') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('warning')): ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 no-print">
                    <div class="flex">
                        <div class="py-1">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                        </div>
                        <div>
                            <p class="font-bold">Warning!</p>
                            <p class="text-sm"><?= getFlash('warning') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="p-4">
                <?= $content ?? '' ?>
            </div>

        </div>

    <!-- Scripts -->
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('-translate-x-full');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                mainContent.classList.remove('lg:ml-64');
            } else {
                mainContent.classList.add('lg:ml-64');
            }
        });

        // Submenu toggle
        function toggleSubmenu(menuName) {
            const menu = document.getElementById(menuName + '-menu');
            const icon = document.getElementById(menuName + '-icon');
            
            menu.classList.toggle('hidden');
            icon.classList.toggle('rotate-90');
        }

        // Auto-hide flash messages
        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-yellow-100');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(function(dropdown) {
                if (!dropdown.contains(event.target)) {
                    const menu = dropdown.querySelector('.dropdown-menu');
                    if (menu) {
                        menu.classList.add('hidden');
                    }
                }
            });
        });

        // Show dropdowns on hover (desktop) and click (mobile)
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(function(dropdown) {
            const button = dropdown.querySelector('button');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                menu.classList.toggle('hidden');
            });
        });

        // Form validation helper
        function validateForm(formId, rules) {
            const form = document.getElementById(formId);
            const errors = {};
            let hasErrors = false;

            Object.keys(rules).forEach(function(field) {
                const input = form.querySelector(`[name="${field}"]`);
                const value = input ? input.value.trim() : '';
                const fieldRules = rules[field];

                fieldRules.forEach(function(rule) {
                    if (rule === 'required' && !value) {
                        errors[field] = 'This field is required';
                        hasErrors = true;
                    } else if (rule.startsWith('min:') && value.length < parseInt(rule.split(':')[1])) {
                        errors[field] = `Minimum ${rule.split(':')[1]} characters required`;
                        hasErrors = true;
                    } else if (rule === 'email' && value && !isValidEmail(value)) {
                        errors[field] = 'Invalid email format';
                        hasErrors = true;
                    }
                });
            });

            // Display errors
            Object.keys(errors).forEach(function(field) {
                const errorElement = document.getElementById(field + '-error');
                if (errorElement) {
                    errorElement.textContent = errors[field];
                    errorElement.classList.remove('hidden');
                }
            });

            return !hasErrors;
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
    </script>
    
    <!-- Bootstrap JS for sales views -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>