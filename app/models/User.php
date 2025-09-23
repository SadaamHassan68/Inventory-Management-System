<?php
/**
 * User Model for Inventory Management System
 */
class User extends BaseModel {
    protected $table = 'users';
    protected $fillable = [
        'username', 'email', 'password_hash', 'full_name', 
        'role', 'phone', 'is_active'
    ];
    protected $hidden = ['password_hash'];

    // Get user with password hash for authentication (bypasses hidden fields)
    public function findByWithPassword($field, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value";
        return $this->db->fetch($sql, ['value' => $value]);
    }

    public function create($data) {
        // Hash password before storing
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        return parent::create($data);
    }

    public function update($id, $data) {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        return parent::update($id, $data);
    }

    public function authenticate($username, $password) {
        // Find user by username or email
        $sql = "SELECT * FROM {$this->table} WHERE (username = :username OR email = :email) AND is_active = 1";
        $user = $this->db->fetch($sql, ['username' => $username, 'email' => $username]);
        
        if (!$user) {
            return false;
        }

        // Check if account is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return ['error' => 'Account is temporarily locked. Please try again later.'];
        }

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Reset login attempts and update last login
            $this->db->update($this->table, [
                'login_attempts' => 0,
                'locked_until' => null,
                'last_login' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $user['id']]);
            
            // Remove sensitive data
            unset($user['password_hash']);
            return $user;
        } else {
            // Increment login attempts
            $attempts = $user['login_attempts'] + 1;
            $updateData = ['login_attempts' => $attempts];
            
            // Lock account if too many attempts
            global $config;
            $maxAttempts = $config['security']['max_login_attempts'] ?? 5;
            $lockoutDuration = $config['security']['lockout_duration'] ?? 900; // 15 minutes
            
            if ($attempts >= $maxAttempts) {
                $updateData['locked_until'] = date('Y-m-d H:i:s', time() + $lockoutDuration);
            }
            
            $this->db->update($this->table, $updateData, 'id = :id', ['id' => $user['id']]);
            
            return false;
        }
    }

    public function validateRegistration($data) {
        $rules = [
            'username' => 'required|min:3|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'full_name' => 'required|min:2|max:100',
            'role' => 'required'
        ];
        
        return $this->validate($data, $rules);
    }

    public function validateUpdate($data, $userId) {
        $rules = [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'full_name' => 'required|min:2|max:100',
            'role' => 'required'
        ];
        
        if (!empty($data['password'])) {
            $rules['password'] = 'min:6';
        }
        
        $errors = $this->validate($data, $rules);
        
        // Check unique username (excluding current user)
        if (!empty($data['username'])) {
            $existing = $this->db->fetch(
                "SELECT id FROM users WHERE username = :username AND id != :id", 
                ['username' => $data['username'], 'id' => $userId]
            );
            if ($existing) {
                $errors['username'][] = 'Username already exists';
            }
        }
        
        // Check unique email (excluding current user)
        if (!empty($data['email'])) {
            $existing = $this->db->fetch(
                "SELECT id FROM users WHERE email = :email AND id != :id", 
                ['email' => $data['email'], 'id' => $userId]
            );
            if ($existing) {
                $errors['email'][] = 'Email already exists';
            }
        }
        
        return $errors;
    }

    public function hasPermission($permission) {
        global $config;
        $user = auth();
        
        if (!$user) {
            return false;
        }
        
        $rolePermissions = $config['roles'][$user['role']] ?? [];
        return in_array($permission, $rolePermissions);
    }

    public function getActiveUsers() {
        return $this->where('is_active', '=', true);
    }

    public function getUsersByRole($role) {
        return $this->where('role', '=', $role);
    }

    public function searchUsers($searchTerm) {
        return $this->search(['username', 'email', 'full_name'], $searchTerm, 'is_active = 1');
    }

    public function getUserStats() {
        $stats = [];
        
        // Total active users
        $stats['total_active'] = $this->count('is_active = 1');
        
        // Users by role
        $stats['admins'] = $this->count("role = 'admin' AND is_active = 1");
        $stats['staff'] = $this->count("role = 'staff' AND is_active = 1");
        
        // Recent logins (last 7 days)
        $stats['recent_logins'] = $this->count(
            "last_login >= :date AND is_active = 1", 
            ['date' => date('Y-m-d H:i:s', strtotime('-7 days'))]
        );
        
        return $stats;
    }

    public function getRecentActivity($limit = 10) {
        $sql = "SELECT u.username, u.full_name, al.action, al.created_at
                FROM activity_logs al
                JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql);
    }

    public function updateLastLogin($userId) {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    public function deactivateUser($userId) {
        return $this->update($userId, ['is_active' => false]);
    }

    public function activateUser($userId) {
        return $this->update($userId, ['is_active' => true]);
    }

    public function resetLoginAttempts($userId) {
        return $this->db->update($this->table, [
            'login_attempts' => 0,
            'locked_until' => null
        ], 'id = :id', ['id' => $userId]);
    }
}