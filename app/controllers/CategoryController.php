<?php

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        $this->categoryModel = new Category();
    }

    public function index()
    {
        try {
            $search = $_GET['search'] ?? '';
            $categories = $this->categoryModel->getCategoriesWithProductCount();

            if (!empty($search)) {
                $categories = $this->categoryModel->searchCategories($search);
            }

            // Get statistics
            $stats = $this->categoryModel->getStats();

            $data = [
                'title' => 'Qaybaha',
                'categories' => $categories,
                'search' => $search,
                'stats' => $stats
            ];

            return view('categories/index', $data);
        } catch (Exception $e) {
            error_log($e->getMessage());
            setFlash('error', 'Error loading categories');
            redirect('/dashboard');
        }
    }

    public function store()
    {
        try {
            if (empty($_POST['name'])) {
                throw new Exception('Category name is required');
            }

            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $category = $this->categoryModel->create($data);

            if ($category) {
                setFlash('success', 'Qaybta si guul leh ayaa loo abuuray');
            } else {
                throw new Exception('Failed to create category');
            }
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }

        redirect('/categories');
    }

    public function update($id)
    {
        try {
            $category = $this->categoryModel->find($id);
            if (!$category) {
                throw new Exception('Category not found');
            }

            if (empty($_POST['name'])) {
                throw new Exception('Category name is required');
            }

            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $updated = $this->categoryModel->update($id, $data);

            if ($updated) {
                setFlash('success', 'Qaybta si guul leh ayaa loo cusboonaysiiyay');
            } else {
                throw new Exception('Failed to update category');
            }
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }

        redirect('/categories');
    }

    public function delete($id)
    {
        try {
            $category = $this->categoryModel->find($id);
            if (!$category) {
                throw new Exception('Category not found');
            }

            // Check if category has products
            $productCount = $this->categoryModel->db->query(
                "SELECT COUNT(*) as count FROM products WHERE category_id = ?", 
                [$id]
            )->fetch()['count'];

            if ($productCount > 0) {
                throw new Exception('Cannot delete category that has products. Please reassign or remove products first.');
            }

            $deleted = $this->categoryModel->delete($id);

            if ($deleted) {
                setFlash('success', 'Qaybta si guul leh ayaa loo tirtiray');
            } else {
                throw new Exception('Failed to delete category');
            }
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }

        redirect('/categories');
    }
}