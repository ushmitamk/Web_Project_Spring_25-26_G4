# Task 3 — Shopping Cart & Checkout (Customer)
**Student 3 | Web Technologies Project**

## Your Responsibility
You build the **complete customer purchase flow**:
- Product catalogue with AJAX search and category filter
- Session-based AJAX cart (add, update qty, remove)
- Checkout with saved address pre-fill, order writing

## Files You Own (implement/edit these)
| File | Purpose |
|------|---------|
| `controllers/ProductController.php` | Product listing, detail page, AJAX search/filter APIs |
| `controllers/CartController.php` | Cart add/update/remove APIs, checkout, confirmation |
| `models/Product.php` | Product queries (available products, detail, search) |
| `models/Category.php` | Category list for filter dropdown |
| `models/Cart.php` | Cart helper (session-based, no DB table) |
| `models/Order.php` | Write orders + order_items, decrement stock |
| `views/products/index.php` | Product catalogue grid page |
| `views/products/detail.php` | Single product detail page |
| `views/products/_grid.php` | AJAX-re-renderable product grid partial |
| `views/cart/index.php` | Cart page with +/− qty and remove |
| `views/cart/checkout.php` | Checkout form (saved addresses + payment method) |
| `views/cart/confirmation.php` | Order confirmation page |

## Shared Files (do NOT modify)
- `config/helpers.php` — use `require_login()`, `require_customer()`
- `config/database.php`, `config/config.php`
- `database/schema.sql`
- `views/layouts/` — shared header/footer

## Dependencies on Other Tasks
- **Task 1** — `$_SESSION['user_id']` must be set; `users.shipping_addresses` used for checkout pre-fill
- **Task 2** — `products` and `categories` rows must exist with `is_available = 1`
- **Task 4** — provides average rating display on detail/grid pages (coordinate the `GET /api/products/{id}/reviews` endpoint)

## Key Outputs (Task 4 depends on these)
- `orders` rows written on checkout
- `order_items` rows written on checkout
- `products.stock_qty` decremented on checkout

## DB Tables You Use
```
products    (read: catalogue; write: decrement stock_qty)
categories  (read: filter dropdown)
orders      (write on checkout)
order_items (write on checkout)
users       (read: shipping_addresses for pre-fill)
reviews     (read-only: average rating on detail page)
```
Cart is stored ONLY in `$_SESSION['cart']` as `[product_id => quantity]` — no DB table.

## Requirements Summary
1. **Product Catalogue** — grid of `is_available = 1` products. AJAX keyword search `GET /api/products/search?q=`. AJAX category filter `GET /api/products?category_id=`. Both re-render `_grid.php` in-place.
2. **Add to Cart (AJAX)** — `POST /api/cart/add` with `{product_id}`. Cap at available stock. Return new total count; JS updates navbar cart icon.
3. **Cart Page** — list items, +/− qty, remove. Each action fires AJAX to `/api/cart/update` or `/api/cart/remove`; JS updates only totals — no full reload.
4. **Checkout** — saved addresses as radio buttons (pre-filled from profile). Payment: Cash / Card. On submit: validate cart non-empty & stock sufficient → write orders + order_items → decrement stock → clear `$_SESSION['cart']` → redirect to confirmation.

## API Endpoints to Implement
```
GET  /api/products/search?q=…        → JSON product list
GET  /api/products?category_id=…     → JSON product list
POST /api/cart/add    {product_id}   → {"count": N}
POST /api/cart/update {product_id, qty} → {"line_total": "…", "grand_total": "…"}
POST /api/cart/remove {product_id}   → {"grand_total": "…"}
```

## GitHub Collaboration Notes
- Branch name: `feature/task3-cart-checkout`
- Only commit files listed under "Files You Own"
- Do NOT modify `database/schema.sql`, `public/index.php`, or shared config
- **Requires Task 1 merged first** (auth/session), and Task 2 data to test fully
