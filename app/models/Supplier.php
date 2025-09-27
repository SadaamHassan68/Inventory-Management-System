<?php

class Supplier extends BaseModel
{
    protected $table = 'suppliers';
    protected $fillable = ['name', 'contact_person', 'email', 'phone', 'address', 'is_active'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active suppliers for dropdown
     */
    public function getActive()
    {
        return $this->where('is_active', '=', 1, 'name ASC');
    }

    /**
     * Get supplier statistics
     */
    public function getStats()
    {
        $stats = [];

        // Total suppliers
        $stats['total_suppliers'] = $this->count();

        // Active suppliers
        $stats['active_suppliers'] = $this->count('is_active = 1');

        // Suppliers with products
        $sql = "SELECT COUNT(DISTINCT supplier_id) as total FROM products WHERE supplier_id IS NOT NULL";
        $result = $this->db->query($sql)->fetch();
        $stats['suppliers_with_products'] = $result['total'] ?? 0;

        return $stats;
    }

    /**
     * Get suppliers with product count
     */
    public function getWithProductCount()
    {
        $sql = "SELECT s.*, 
                COUNT(p.id) as product_count 
                FROM {$this->table} s 
                LEFT JOIN products p ON s.id = p.supplier_id 
                GROUP BY s.id 
                ORDER BY s.name ASC";
        
        return $this->db->query($sql)->fetchAll();
    }
}