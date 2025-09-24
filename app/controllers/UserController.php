<?php

class UserController
{
    private $db;
    private $userModel;

    public function __construct()
    {
        // Check authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $this->db = Database::getInstance();
        $this->userModel = new User();
    }

    /**
     * Display users list
     */
    public function index()
    {
        // Check permission - only admin can manage users
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/dashboard');
            return;
        }

        $title = 'User Management';
        
        // Get search parameter
        $search = $_GET['search'] ?? '';
        
        // Get pagination parameters
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $users = [];
        $totalUsers = 0;
        
        if (!empty($search)) {
            // Search users
            $users = $this->userModel->searchUsers($search);
            $totalUsers = count($users);
        } else {
            // Get all users with pagination
            $users = $this->db->fetchAll(
                "SELECT id, username, email, full_name, role, phone, is_active, 
                        created_at, last_login 
                 FROM users 
                 ORDER BY created_at DESC 
                 LIMIT :limit OFFSET :offset",
                ['limit' => $limit, 'offset' => $offset]
            );
            
            // Get total count
            $totalResult = $this->db->fetch("SELECT COUNT(*) as total FROM users");
            $totalUsers = $totalResult['total'];
        }
        
        $totalPages = ceil($totalUsers / $limit);
        
        // Get user statistics
        $stats = $this->userModel->getUserStats();
        
        $data = [
            'title' => $title,
            'users' => $users,
            'stats' => $stats,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalUsers,
                'per_page' => $limit
            ],
            'filters' => [
                'search' => $search
            ]
        ];
        
        view('users.index', $data);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/users');
            return;
        }

        $data = [
            'title' => 'Add New User'
        ];

        view('users.create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/users');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users/create');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/users/create');
            return;
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
            redirect('/users/create');
            return;
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
                redirect('/users/create');
            }
        } catch (\Exception $e) {
            setFlash('error', 'An error occurred: ' . $e->getMessage());
            setOldInput($_POST);
            redirect('/users/create');
        }
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/users');
            return;
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            setFlash('error', 'User not found');
            redirect('/users');
            return;
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user
        ];

        view('users.edit', $data);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/users');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            redirect('/users');
            return;
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            setFlash('error', 'User not found');
            redirect('/users');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/users/edit/' . $id);
            return;
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'role' => $_POST['role'] ?? 'staff',
            'phone' => trim($_POST['phone'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Handle password update (optional)
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
            $data['password_confirm'] = $_POST['password_confirm'] ?? '';
            
            if ($data['password'] !== $data['password_confirm']) {
                setFlash('error', 'Password confirmation does not match');
                redirect('/users/edit/' . $id);
                return;
            }
        }

        // Validate input
        $errors = $this->userModel->validateUpdate($data, $id);

        if (!empty($errors)) {
            setErrors($errors);
            setOldInput($_POST);
            redirect('/users/edit/' . $id);
            return;
        }

        try {
            $this->userModel->update($id, $data);
            
            // Log activity
            logActivity('user_updated', 'users', $id);
            
            setFlash('success', 'User updated successfully');
            redirect('/users');
        } catch (Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            setFlash('error', 'Failed to update user');
            redirect('/users/edit/' . $id);
        }
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/users');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users');
            return;
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            setFlash('error', 'User not found');
            redirect('/users');
            return;
        }

        // Check if trying to delete self
        $currentUser = auth();
        if ($currentUser['id'] == $id) {
            setFlash('error', 'You cannot delete your own account');
            redirect('/users');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/users');
            return;
        }

        try {
            // Instead of deleting, deactivate the user to preserve data integrity
            $this->userModel->deactivateUser($id);
            
            // Log activity
            logActivity('user_deactivated', 'users', $id);
            
            setFlash('success', 'User deactivated successfully');
            redirect('/users');
        } catch (Exception $e) {
            error_log("Error deactivating user: " . $e->getMessage());
            setFlash('error', 'Failed to deactivate user');
            redirect('/users');
        }
    }
}