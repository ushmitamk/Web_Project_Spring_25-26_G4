# Task 4 — Order Management & Product Reviews
**Student 4 | Web Technologies Project**

## Your Responsibility
You build the **post-purchase layer**:
- Customer "My Orders" page with order detail/expand
- Admin order management with status update via AJAX
- Product review system (star rating + text, one per user per product)
- Average rating display on product pages (coordinate with Task 3)

## Files You Own (implement/edit these)
| File | Purpose |
|------|---------|
| `controllers/OrderController.php` | Customer order list/detail; admin order list; status update API |
| `controllers/ReviewController.php` | Submit review API; fetch reviews API |
| `models/Order.php` | DB queries for orders and order_items |
| `models/Review.php` | DB queries for reviews (insert, fetch, avg) |
| `views/orders/my_orders.php` | Customer order history with expandable detail |
| `views/admin/orders/index.php` | Admin order list with status dropdown & AJAX update |

## Shared Files (do NOT modify)
- `config/helpers.php` — use `require_login()`, `require_admin()`
- `config/database.php`, `config/config.php`
- `database/schema.sql`
- `views/layouts/` — shared header/footer

## Dependencies on Other Tasks
- **Task 1** — `$_SESSION['user_id']`, `$_SESSION['role']` must be set
- **Task 2** — admin views need `require_admin()` (already in helpers)
- **Task 3** — orders and order_items rows are written by Task 3 checkout; you read them here

## Key Outputs (completes the full loop)
- `reviews` rows inserted
- Average rating available via `GET /api/products/{id}/reviews` — Task 3's product detail page displays this
- Order status changes reflected in admin and customer views

## DB Tables You Use
```
orders      (read: list/detail; write: status update)
order_items (read: order detail line items)
products    (read: product name in order detail & review form)
reviews     (read + write)
users       (read: for admin order view — customer name)
```

## Requirements Summary
1. **Customer "My Orders"** — list all orders for `$_SESSION['user_id']`, newest first. Show order ID, date, total, colour-coded status badge. Clicking expands itemised detail.
2. **Admin Order Management** — admin-only page. Filters: status dropdown, date range. Each row has a status dropdown; changing it fires `PUT /api/orders/{id}` with `{status}`; PHP updates DB; JS swaps badge in-place. Allowed transitions: Pending → Processing → Shipped → Delivered; any → Cancelled.
3. **Product Review** — on customer order-detail page, products in a **Delivered** order show a "Leave a Review" form (1–5 star radio + text). AJAX `POST /api/reviews`. Enforce one review per user per product (unique constraint in DB). New review appears immediately below form.
4. **Average Rating Display** — `GET /api/products/{id}/reviews` returns reviews + average. Task 3's product detail page calls this. You implement the endpoint; Task 3 consumes it.

## API Endpoints to Implement
```
PUT  /api/orders/{id}           {status}             → {"ok": true}
POST /api/reviews               {product_id, order_id, rating, review_text} → {"ok": true, "review": {...}}
GET  /api/products/{id}/reviews                      → {"average": 4.2, "reviews": [...]}
```

## GitHub Collaboration Notes
- Branch name: `feature/task4-orders-reviews`
- Only commit files listed under "Files You Own"
- Do NOT modify `database/schema.sql`, `public/index.php`, or shared config
- **Coordinate with Task 3** on the `/api/products/{id}/reviews` endpoint — they consume it, you implement it
- Merge order: Task 1 → Task 2 → Task 3 → Task 4 (for full integration testing)
