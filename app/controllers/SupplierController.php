<?php

class SupplierController
{
    private $supplierModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        $this->supplierModel = new Supplier();
    }

    public function index()
    {
        try {
            $search = $_GET['search'] ?? '';
            $suppliers = $this->supplierModel->all('name ASC');

            if (!empty($search)) {
                $suppliers = $this->supplierModel->search(['name', 'email', 'phone'], $search);
            }

            $data = [
                'title' => 'Suppliers',
                'suppliers' => $suppliers,
                'search' => $search
            ];

            return view('suppliers/index', $data);
        } catch (Exception $e) {
            error_log($e->getMessage());
            setFlash('error', 'Error loading suppliers');
            redirect('/dashboard');
        }
    }

    public function store()
    {
        try {
            if (empty($_POST['name'])) {
                throw new Exception('Supplier name is required');
            }

            $data = [
                'name' => trim($_POST['name']),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $supplier = $this->supplierModel->create($data);

            if ($supplier) {
                setFlash('success', 'Supplier created successfully');
            } else {
                throw new Exception('Failed to create supplier');
            }
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }

        redirect('/suppliers');
    }

    public function update($id)
    {
        try {
            $supplier = $this->supplierModel->find($id);
            if (!$supplier) {
                throw new Exception('Supplier not found');
            }

            if (empty($_POST['name'])) {
                throw new Exception('Supplier name is required');
            }

            $data = [
                'name' => trim($_POST['name']),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            $updated = $this->supplierModel->update($id, $data);

            if ($updated) {
                setFlash('success', 'Supplier updated successfully');
            } else {
                throw new Exception('Failed to update supplier');
            }
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }

        redirect('/suppliers');
    }

    public function delete($id)
    {
        try {
            $supplier = $this->supplierModel->find($id);
            if (!$supplier) {
                throw new Exception('Supplier not found');
            }

            $deleted = $this->supplierModel->delete($id);

            if ($deleted) {
                setFlash('success', 'Supplier deleted successfully');
            } else {
                throw new Exception('Failed to delete supplier');
            }
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
        }

        redirect('/suppliers');
    }
}