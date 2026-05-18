# Task 2 — Product & Category Management (Admin)
**Student 2 | Web Technologies Project**

## Your Responsibility
You build the **admin product catalogue**:
- Nested category CRUD (parent/child, one level deep)
- Product CRUD with image upload
- Stock alert highlighting
- Live availability toggle via AJAX

## Files You Own (implement/edit these)
| File | Purpose |
|------|---------|
| `controllers/AdminController.php` | All admin actions: category CRUD, product CRUD, availability toggle API |
| `models/Product.php` | DB queries for products (includes average rating subquery) |
| `models/Category.php` | DB queries for categories (hierarchy) |
| `views/admin/dashboard.php` | Admin home page |
| `views/admin/products/index.php` | Product listing with stock alerts & AJAX toggle |
| `views/admin/products/form.php` | Product create/edit form |
| `views/admin/categories/index.php` | Category listing |
| `views/admin/categories/form.php` | Category create/edit form |
| `views/admin/admins/pending.php` | Pending admin accounts view |

## Shared Files (do NOT modify)
- `config/helpers.php` — use `require_admin()` at the top of every admin page
- `config/database.php`, `config/config.php` — connection & constants
- `database/schema.sql` — shared schema (do not alter)
- `views/layouts/` — shared header/footer

## Key Outputs (other students depend on these)
- `categories` rows — Task 3 uses these for the filter dropdown
- `products` rows (with `is_available`, `primary_image_path`) — Task 3 displays these
- `products.stock_qty` — Task 3 decrements this on checkout

## DB Tables You Use
```
categories (id, parent_id, name)
products   (id, category_id, name, description, price, stock_qty, primary_image_path, is_available, created_at)
reviews    (read-only — for average rating display)
order_items (read-only — to block product deletion)
```

## Requirements Summary
1. **Category CRUD** — one level of nesting via `parent_id`. Delete blocked if child categories or products exist.
2. **Product CRUD** — name, description, price (positive), stock qty, category dropdown (shows hierarchy), image upload (JPEG/PNG ≤ 3 MB → `public/uploads/products/`). Delete blocked if product has order_items.
3. **Stock Alert** — rows with `stock_qty ≤ 5` get amber background. Each row shows average star rating (JOIN on reviews).
4. **Availability AJAX Toggle** — `PATCH /api/products/{id}/availability` flips `is_available`; JS swaps badge in-place.

## API Endpoint to Implement
```
PATCH /api/products/{id}/availability
  → flips products.is_available
  ← JSON: {"ok": true, "is_available": 0|1}
```

## GitHub Collaboration Notes
- Branch name: `feature/task2-admin-products`
- Only commit files listed under "Files You Own"
- Do NOT modify `database/schema.sql`, `public/index.php`, or shared config
- **Require Task 1 to be merged first** (you need `require_admin()` working)
