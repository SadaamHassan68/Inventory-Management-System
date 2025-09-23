<?php
/**
 * Authentication Controller for Inventory Management System
 */
class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function loginForm() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        view('auth.login');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        // CSRF protection
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        $errors = [];
        if (empty($username)) {
            $errors['username'] = 'Username or email is required';
        }
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            setErrors($errors);
            setOldInput($_POST);
            redirect('/login');
        }

        // Attempt authentication
        $result = $this->userModel->authenticate($username, $password);
        
        if ($result === false) {
            setFlash('error', 'Invalid username or password');
            setOldInput($_POST);
            redirect('/login');
        }

        if (is_array($result) && isset($result['error'])) {
            setFlash('error', $result['error']);
            redirect('/login');
        }

        // Login successful
        $_SESSION['user'] = $result;
        
        // Log activity
        logActivity('user_login');
        
        // Clear any old input and errors
        clearOldInput();
        clearErrors();
        
        setFlash('success', 'Welcome back, ' . $result['full_name'] . '!');
        redirect('/dashboard');
    }

    public function registerForm() {
        // Check if registration is allowed (only admins can register new users in this system)
        if (!isLoggedIn() || !isAdmin()) {
            setFlash('error', 'Only administrators can register new users.');
            redirect('/login');
        }
        
        view('auth.register');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }

        // Check permissions
        if (!isLoggedIn() || !isAdmin()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
        }

        // CSRF protection
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/register');
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'full_name' => trim($_POST['full_name'] ?? ''),
            'role' => $_POST['role'] ?? 'staff',
            'phone' => trim($_POST['phone'] ?? ''),
        ];

        // Validate input
        $errors = $this->userModel->validateRegistration($data);

        // Password confirmation
        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'][] = 'Password confirmation does not match';
        }

        // Role validation
        if (!in_array($data['role'], ['admin', 'staff'])) {
            $errors['role'][] = 'Invalid role selected';
        }

        if (!empty($errors)) {
            setErrors($errors);
            setOldInput($_POST);
            redirect('/register');
        }

        try {
            // Create user
            $user = $this->userModel->create($data);
            
            if ($user) {
                // Log activity
                logActivity('user_created', 'users', $user['id'], null, $user);
                
                setFlash('success', 'User created successfully');
                clearOldInput();
                clearErrors();
                redirect('/users');
            } else {
                setFlash('error', 'Failed to create user');
                redirect('/register');
            }
        } catch (\Exception $e) {
            setFlash('error', 'An error occurred: ' . $e->getMessage());
            setOldInput($_POST);
            redirect('/register');
        }
    }

    public function logout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/dashboard');
        }

        // Log activity before destroying session
        logActivity('user_logout');
        
        // Destroy session
        session_destroy();
        session_start();
        
        setFlash('success', 'You have been logged out successfully');
        redirect('/login');
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/dashboard');
        }

        // CSRF protection
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/dashboard');
        }

        $user = auth();
        if (!$user) {
            redirect('/login');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        $errors = [];
        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required';
        }
        if (empty($newPassword)) {
            $errors['new_password'] = 'New password is required';
        } elseif (strlen($newPassword) < 6) {
            $errors['new_password'] = 'New password must be at least 6 characters';
        }
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Password confirmation does not match';
        }

        if (!empty($errors)) {
            setErrors($errors);
            redirect('/dashboard');
        }

        // Verify current password
        $userRecord = $this->userModel->getDb()->fetch(
            "SELECT password_hash FROM users WHERE id = :id", 
            ['id' => $user['id']]
        );

        if (!password_verify($currentPassword, $userRecord['password_hash'])) {
            setFlash('error', 'Current password is incorrect');
            redirect('/dashboard');
        }

        // Update password
        try {
            $this->userModel->update($user['id'], ['password' => $newPassword]);
            
            // Log activity
            logActivity('password_changed');
            
            setFlash('success', 'Password changed successfully');
            redirect('/dashboard');
        } catch (\Exception $e) {
            setFlash('error', 'Failed to change password: ' . $e->getMessage());
            redirect('/dashboard');
        }
    }

    public function profile() {
        $user = auth();
        if (!$user) {
            redirect('/login');
        }

        view('auth.profile', ['user' => $user]);
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profile');
        }

        // CSRF protection
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/profile');
        }

        $user = auth();
        if (!$user) {
            redirect('/login');
        }

        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
        ];

        // Basic validation
        $errors = [];
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        }
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        // Check email uniqueness (excluding current user)
        $existingUser = $this->userModel->getDb()->fetch(
            "SELECT id FROM users WHERE email = :email AND id != :id", 
            ['email' => $data['email'], 'id' => $user['id']]
        );
        if ($existingUser) {
            $errors['email'] = 'Email already exists';
        }

        if (!empty($errors)) {
            setErrors($errors);
            setOldInput($_POST);
            redirect('/profile');
        }

        try {
            $oldData = $this->userModel->find($user['id']);
            $updatedUser = $this->userModel->update($user['id'], $data);
            
            if ($updatedUser) {
                // Update session data
                $_SESSION['user'] = array_merge($_SESSION['user'], $data);
                
                // Log activity
                logActivity('profile_updated', 'users', $user['id'], $oldData, $updatedUser);
                
                setFlash('success', 'Profile updated successfully');
                clearOldInput();
                clearErrors();
            } else {
                setFlash('error', 'Failed to update profile');
            }
        } catch (\Exception $e) {
            setFlash('error', 'An error occurred: ' . $e->getMessage());
            setOldInput($_POST);
        }

        redirect('/profile');
    }
}