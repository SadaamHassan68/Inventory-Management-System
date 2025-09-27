<?php
$title = $title ?? 'Ku dar Isticmaale';
ob_start();
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ku dar Isticmaale Cusub</h1>
            <p class="text-gray-600">Samee isticmaale cusub oo ku dar xog kaydigaaga</p>
        </div>
        <div>
            <a href="<?= url('/users') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Ku noqo Isticmaalayaasha
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

    <!-- User Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="<?= url('/users') ?>" class="space-y-6">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Macluumaadka Isticmaalaha</h3>
            </div>

            <div class="px-4 space-y-4">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Magaca Isticmaalaha *</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="<?= old('username') ?>"
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Waa inuu noqdaa mid gaar ah oo ay ku jiraan kaliya xarfo, tiro, iyo xariiqado hoose</p>
                        <?php if (hasErrors('username')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('username')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Ciwaanka Emailka *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= old('email') ?>"
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('email')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('email')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Magaca Buuxa *</label>
                        <input type="text" 
                               id="full_name" 
                               name="full_name" 
                               value="<?= old('full_name') ?>"
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('full_name')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('full_name')) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Doorka *</label>
                        <select id="role" 
                                name="role" 
                                required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Dooro Doorka</option>
                            <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>Shaqaale</option>
                            <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Maamule</option>
                        </select>
                        <?php if (hasErrors('role')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('role')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Lambarka Taleefanka</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= old('phone') ?>"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Dooran - Ku dar koodka dalka haddii aad ka tirsan tahay dal kale</p>
                    <?php if (hasErrors('phone')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('phone')) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Password Section -->
            <div class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dejinta Erayga Sirta ah</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Erayga Sirta ah *</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Ugu yaraan 6 xaraf</p>
                        <?php if (hasErrors('password')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('password')) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700">Xaqiiji Erayga Sirta ah *</label>
                        <input type="password" 
                               id="password_confirm" 
                               name="password_confirm" 
                               required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php if (hasErrors('password_confirm')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= implode(', ', getErrors('password_confirm')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <a href="<?= url('/users') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Jooji
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Samee Isticmaale
                </button>
            </div>
        </form>
    </div>

    <!-- Help Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- User Roles Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-base font-medium text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>Doorarka Isticmaalayaasha
                </h3>
            </div>
            <div class="px-4 py-3 space-y-3">
                <div>
                    <h4 class="text-sm font-medium text-red-600">
                        <i class="fas fa-user-shield mr-1"></i>Maamule
                    </h4>
                    <ul class="mt-1 text-xs text-gray-600 space-y-1">
                        <li>• Gelitaan buuxa oo nidaamka ah</li>
                        <li>• Maamulka dhammaan isticmaalayaasha</li>
                        <li>• Daawashada dhammaan warbixinnada</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-blue-600">
                        <i class="fas fa-user-tie mr-1"></i>Shaqaale
                    </h4>
                    <ul class="mt-1 text-xs text-gray-600 space-y-1">
                        <li>• Diiwaangelinta iibka</li>
                        <li>• Maamulka macaamiisha</li>
                        <li>• Cusboonaysiinta kaydka</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Security Guidelines Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-base font-medium text-gray-900">
                    <i class="fas fa-shield-alt mr-2 text-yellow-500"></i>Tilmaamaha Amniga
                </h3>
            </div>
            <div class="px-4 py-3">
                <ul class="text-xs text-gray-600 space-y-1">
                    <li>• Dooro eray sir ah oo xoog leh oo gaar ah</li>
                    <li>• Magaca isticmaalaha waa inuu noqdaa mid xirfad leh</li>
                    <li>• Emailka ayaa loo isticmaali doonaa wargelinta</li>
                    <li>• Doorarka maamulka waa in la yareeyo</li>
                </ul>
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
        this.setCustomValidity('Erayada sirta ah ma isku mid ahan');
        this.classList.add('border-red-500');
        this.classList.remove('border-gray-300');
    } else {
        this.setCustomValidity('');
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
    }
});

// Username validation
document.getElementById('username').addEventListener('input', function() {
    const username = this.value;
    const pattern = /^[a-zA-Z0-9_]+$/;
    
    if (!pattern.test(username) && username.length > 0) {
        this.setCustomValidity('Magaca isticmaalaha waxaa ka kooban kara kaliya xarfo, tiro, iyo xariiqado hoose');
        this.classList.add('border-red-500');
        this.classList.remove('border-gray-300');
    } else {
        this.setCustomValidity('');
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
    }
});

// Email validation
document.getElementById('email').addEventListener('blur', function() {
    if (this.value && !this.value.includes('@')) {
        this.setCustomValidity('Fadlan geli ciwaanka email-ka sax ah');
        this.classList.add('border-red-500');
        this.classList.remove('border-gray-300');
    } else {
        this.setCustomValidity('');
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
    }
});

// Phone number formatting (simple)
document.getElementById('phone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length >= 6) {
        value = value.replace(/(\d{3})(\d{3})(\d+)/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{3})(\d+)/, '($1) $2');
    }
    this.value = value;
});
</script>

<?php
$content = ob_get_clean();
include APP_ROOT . '/app/views/layouts/app.php';
?>