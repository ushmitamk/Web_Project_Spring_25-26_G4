<?php
class AdminController
{
    public function dashboard()
    {
        require_admin();
        render('admin/dashboard', [
            'productCount' => Product::countAll(),
            'orderCount' => Order::countAll(),
            'customerCount' => User::countByRole('customer'),
            'pendingAdminCount' => User::countPendingAdmins(),
        ]);
    }

    public function adminRequests()
    {
        require_admin();
        render('admin/admins/pending', ['admins' => User::pendingAdmins()]);
    }

    public function approveAdmin($id)
    {
        require_admin();
        if (User::approveAdmin($id)) {
            flash('success', 'Admin account approved successfully.');
        } else {
            flash('error', 'Pending admin request was not found.');
        }
        redirect('/admin/admin-requests');
    }

    public function rejectAdmin($id)
    {
        require_admin();
        if (User::rejectAdmin($id)) {
            flash('success', 'Admin request rejected.');
        } else {
            flash('error', 'Pending admin request was not found.');
        }
        redirect('/admin/admin-requests');
    }

    public function categories()
    {
        require_admin();
        render('admin/categories/index', ['categories' => Category::all()]);
    }

    public function categoryCreate()
    {
        require_admin();
        $this->categoryForm(null);
    }

    public function categoryEdit($id)
    {
        require_admin();
        $category = Category::find($id);
        if (!$category) { http_response_code(404); render('errors/404'); return; }
        $this->categoryForm($category);
    }

    private function categoryForm($category)
    {
        $errors = [];
        $old = $category ?: ['name' => '', 'parent_id' => null];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['name'] = trim($_POST['name'] ?? '');
            $old['parent_id'] = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            if ($old['name'] === '') $errors['name'] = 'Category name is required.';
            if ($category && $old['parent_id'] && (int)$old['parent_id'] === (int)$category['id']) {
                $errors['parent_id'] = 'A category cannot be its own parent.';
            }
            if ($old['parent_id']) {
                $parent = Category::find($old['parent_id']);
                if (!$parent || !empty($parent['parent_id'])) {
                    $errors['parent_id'] = 'Parent category must be a top-level category.';
                }
            }
            if (!$errors) {
                if ($category) {
                    Category::update($category['id'], $old['name'], $old['parent_id']);
                    flash('success', 'Category updated successfully.');
                } else {
                    Category::create($old['name'], $old['parent_id']);
                    flash('success', 'Category created successfully.');
                }
                redirect('/admin/categories');
            }
        }
        render('admin/categories/form', [
            'category' => $category,
            'old' => $old,
            'errors' => $errors,
            'parents' => Category::rootOptions($category ? $category['id'] : null),
        ]);
    }

    public function categoryDelete($id)
    {
        require_admin();
        if (Category::hasChildren($id) || Category::hasProducts($id)) {
            flash('error', 'Category cannot be deleted because child categories or products reference it.');
        } else {
            Category::delete($id);
            flash('success', 'Category deleted successfully.');
        }
        redirect('/admin/categories');
    }

    public function products()
    {
        require_admin();
        render('admin/products/index', ['products' => Product::adminAll()]);
    }

    public function productCreate()
    {
        require_admin();
        $this->productForm(null);
    }

    public function productEdit($id)
    {
        require_admin();
        $product = Product::find($id);
        if (!$product) { http_response_code(404); render('errors/404'); return; }
        $this->productForm($product);
    }

    private function productForm($product)
    {
        $errors = [];
        $old = $product ?: [
            'category_id' => '', 'name' => '', 'description' => '', 'price' => '',
            'stock_qty' => '', 'primary_image_path' => '', 'is_available' => 1
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['category_id'] = (int)($_POST['category_id'] ?? 0);
            $old['name'] = trim($_POST['name'] ?? '');
            $old['description'] = trim($_POST['description'] ?? '');
            $old['price'] = trim($_POST['price'] ?? '');
            $old['stock_qty'] = trim($_POST['stock_qty'] ?? '');
            $old['is_available'] = !empty($_POST['is_available']) ? 1 : 0;

            if ($old['category_id'] <= 0 || !Category::find($old['category_id'])) $errors['category_id'] = 'Valid category is required.';
            if ($old['name'] === '') $errors['name'] = 'Product name is required.';
            if ($old['description'] === '') $errors['description'] = 'Description is required.';
            if (!is_numeric($old['price']) || (float)$old['price'] <= 0) $errors['price'] = 'Price must be positive.';
            if (!ctype_digit((string)$old['stock_qty']) || (int)$old['stock_qty'] < 0) $errors['stock_qty'] = 'Stock quantity must be zero or more.';

            $imageResult = $this->handleProductImage($product ? false : true);
            if (!$imageResult['ok']) {
                $errors['primary_image_path'] = $imageResult['message'];
            } elseif (!empty($imageResult['path'])) {
                $old['primary_image_path'] = $imageResult['path'];
            } elseif ($product) {
                $old['primary_image_path'] = $product['primary_image_path'];
            }

            if (!$errors) {
                if ($product) {
                    Product::update($product['id'], $old);
                    flash('success', 'Product updated successfully.');
                } else {
                    Product::create($old);
                    flash('success', 'Product created successfully.');
                }
                redirect('/admin/products');
            }
        }
        render('admin/products/form', ['product' => $product, 'old' => $old, 'errors' => $errors, 'categories' => Category::hierarchyOptions()]);
    }

    private function handleProductImage($required)
    {
        if (empty($_FILES['primary_image']) || $_FILES['primary_image']['error'] === UPLOAD_ERR_NO_FILE) {
            return $required ? ['ok' => false, 'message' => 'Primary image is required.'] : ['ok' => true, 'path' => null];
        }
        $file = $_FILES['primary_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => 'Upload failed.'];
        }
        if ($file['size'] > 3 * 1024 * 1024) {
            return ['ok' => false, 'message' => 'Image must be 3 MB or smaller.'];
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (!isset($allowed[$mime])) {
            return ['ok' => false, 'message' => 'Only JPEG or PNG images are allowed.'];
        }
        $uploadDir = ROOT_PATH . '/public/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $filename = 'product_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        $target = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['ok' => false, 'message' => 'Could not save uploaded image.'];
        }
        return ['ok' => true, 'path' => 'uploads/products/' . $filename];
    }

    public function productDelete($id)
    {
        require_admin();
        if (Product::hasOrderItems($id)) {
            flash('error', 'Product cannot be deleted because order items reference it.');
        } else {
            Product::delete($id);
            flash('success', 'Product deleted successfully.');
        }
        redirect('/admin/products');
    }

    public function toggleAvailability($id)
    {
        require_admin();
        $newValue = Product::toggleAvailability($id);
        if ($newValue === null) json_response(['ok' => false, 'message' => 'Product not found.'], 404);
        json_response(['ok' => true, 'is_available' => (int)$newValue, 'text' => $newValue ? 'In Stock' : 'Out of Stock']);
    }

    public function orders()
    {
        require_admin();
        $filters = [
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
        ];
        if ($filters['status'] && !in_array($filters['status'], Order::$statuses, true)) $filters['status'] = '';
        render('admin/orders/index', ['orders' => Order::adminOrders($filters), 'filters' => $filters, 'statuses' => Order::$statuses]);
    }

    public function updateOrderStatus($id)
    {
        require_admin();
        $data = input_json();
        $newStatus = $data['status'] ?? '';
        if (!in_array($newStatus, Order::$statuses, true)) json_response(['ok' => false, 'message' => 'Invalid status.'], 422);
        $order = Order::find($id);
        if (!$order) json_response(['ok' => false, 'message' => 'Order not found.'], 404);
        if (!Order::canMoveTo($order['status'], $newStatus)) {
            json_response(['ok' => false, 'message' => 'Invalid status sequence. Allowed: Pending → Processing → Shipped → Delivered, or Cancelled from any state.'], 422);
        }
        Order::updateStatus($id, $newStatus);
        json_response(['ok' => true, 'status' => $newStatus]);
    }
}
