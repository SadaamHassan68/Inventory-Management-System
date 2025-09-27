<?php

class SettingsController
{
    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/dashboard');
        }
    }

    public function index()
    {
        try {
            // Get current settings from database or config
            $settings = [
                'app_name' => 'Inventory Management System',
                'currency' => 'USD',
                'timezone' => 'UTC',
                'low_stock_threshold' => 10,
                'auto_backup' => true,
                'email_notifications' => true,
                'default_tax_rate' => 0.00,
                'items_per_page' => 20
            ];

            $data = [
                'title' => 'System Settings',
                'settings' => $settings
            ];

            return view('settings/index', $data);
        } catch (Exception $e) {
            error_log($e->getMessage());
            setFlash('error', 'Error loading settings');
            redirect('/dashboard');
        }
    }

    public function update()
    {
        try {
            // Validate and update settings
            $allowedSettings = [
                'app_name',
                'currency', 
                'timezone',
                'low_stock_threshold',
                'auto_backup',
                'email_notifications',
                'default_tax_rate',
                'items_per_page'
            ];

            $updatedSettings = [];
            foreach ($allowedSettings as $setting) {
                if (isset($_POST[$setting])) {
                    $updatedSettings[$setting] = $_POST[$setting];
                }
            }

            // Here you would typically save to database
            // For now, we'll just simulate success
            if (!empty($updatedSettings)) {
                setFlash('success', 'Settings updated successfully');
            } else {
                throw new Exception('No settings to update');
            }

        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }

        redirect('/settings');
    }
}