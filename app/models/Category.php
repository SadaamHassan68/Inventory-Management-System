<?php

class Category extends BaseModel
{
    protected $table = 'categories';
    protected $fillable = ['name', 'description', 'is_active'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active categories for dropdown
     */
    public function getActiveCategories()
    {
        return $this->where('is_active', '=', 1, 'name ASC');
    }

    /**
     * Get categories with product count
     */
    public function getCategoriesWithProductCount()
    {
        $sql = "SELECT 
                    c.*,
                    COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.id, c.name, c.description, c.is_active, c.created_at, c.updated_at
                ORDER BY c.name ASC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Get category statistics
     */
    public function getStats()
    {
        $stats = [];

        // Total categories
        $stats['total_categories'] = $this->count();

        // Active categories
        $stats['active_categories'] = $this->count('is_active = 1');

        // Categories with products
        $sql = "SELECT COUNT(DISTINCT category_id) as total FROM products WHERE category_id IS NOT NULL";
        $result = $this->db->query($sql)->fetch();
        $stats['categories_with_products'] = $result['total'] ?? 0;

        return $stats;
    }

    /**
     * Search categories
     */
    public function searchCategories($searchTerm)
    {
        return $this->search(['name', 'description'], $searchTerm, 'is_active = 1');
    }
}