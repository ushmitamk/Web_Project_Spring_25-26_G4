# Project 04 - E-Commerce Store (PHP MVC + XAMPP)

This is a complete PHP MVC e-commerce store for the P4 lab exam requirements. It includes authentication, profile management, admin category/product CRUD, AJAX product availability toggle, AJAX catalogue filters, session cart, checkout, customer order history, admin order fulfilment and product reviews.

## Folder structure

- `config/` - database connection and helper functions
- `controllers/` - PHP MVC controllers
- `models/` - PDO model classes
- `views/` - PHP views
- `public/` - front controller, CSS, JavaScript and uploads
- `database/schema.sql` - database schema and demo seed data

## How to run with XAMPP

1. Copy the folder `p4_ecommerce_store` into your XAMPP `htdocs` folder.
2. Start Apache and MySQL from XAMPP Control Panel.
3. Open phpMyAdmin and import `database/schema.sql`.
4. Edit `config/config.php` only if your MySQL username/password is not the XAMPP default.
5. Open this URL in your browser:

   `http://localhost/p4_ecommerce_store/public/`

## Demo accounts

- Admin: `admin@p4.test` / `admin12345`
- Customer: `customer@p4.test` / `customer12345`

## Important features included

- Password hashing with `password_hash()` and checking with `password_verify()`.
- Remember Me login using a 30-day cookie and a hashed token stored in `users.remember_token`.
- `require_admin()` helper in `config/helpers.php`.
- PDO prepared statements through `Database::run()`.
- Server-side validation with inline error messages.
- Product image upload validation: JPEG/PNG only, maximum 3 MB, stored in `public/uploads/products/`.
- Category delete blocked if child categories or products reference it.
- Product delete blocked if order items reference it.
- Low stock rows highlighted when `stock_qty <= 5`.
- Product availability AJAX toggle using `PATCH /api/products/{id}/availability`.
- Product search and category filter using AJAX.
- Cart stored in `$_SESSION['cart']` as `[product_id => quantity]`.
- Cart add/update/remove are AJAX-based and capped at available stock.
- Checkout writes `orders` and `order_items`, decrements product stock and clears the cart.
- Admin order status update via `PUT /api/orders/{id}` with the required status sequence.
- Delivered orders allow one AJAX review per user per product, enforced by a database unique key.

## Note

If Apache rewrite is disabled, enable `mod_rewrite` in XAMPP. The included `public/.htaccess` sends clean URLs to `public/index.php`.

## Update notes

This version adds the requested improvements while keeping the existing project structure and features intact:

- Added role selection on registration: Customer or Admin.
- New Admin registrations are saved as `pending` and cannot access the Admin Panel until approved.
- Existing active Admins can approve or reject pending Admin requests from **Admin Requests**.
- Added an `account_status` column for users. The application also runs a small migration on load to add the column to older databases automatically.
- Ensured the `reviews` table exists and updated review permissions.
- Customers can submit product reviews for products in their cart or purchase history.
- Added review forms on product detail, cart and order pages where relevant.
- Added Back navigation buttons on inner pages.
- Improved Admin and Customer profile page design while keeping existing editable fields and password-change functionality.
