# Task 1 — User Authentication & Profile
**Student 1 | Web Technologies Project**

## Your Responsibility
You build the **identity layer** of the e-commerce application:
- User Registration
- Login with Remember Me (persistent cookie)
- Customer Profile management (name, email, phone, shipping addresses)
- Admin Gate helper (`require_admin()`) — already implemented in `config/helpers.php`

## Files You Own (implement/edit these)
| File | Purpose |
|------|---------|
| `controllers/AuthController.php` | Registration, login, logout, cookie-based auto-login |
| `controllers/ProfileController.php` | Customer profile & password change |
| `models/User.php` | DB queries for users table |
| `views/auth/register.php` | Registration form |
| `views/auth/login.php` | Login form with Remember Me checkbox |
| `views/profile/edit.php` | Profile edit form |

## Shared Files (do NOT modify)
- `config/database.php` — PDO connection
- `config/config.php` — constants (BASE_URL, ROOT_PATH, etc.)
- `config/helpers.php` — `require_admin()`, `require_login()`, flash, redirect, etc.
- `database/schema.sql` — shared DB schema (do not alter)
- `public/index.php` — front controller / router
- `views/layouts/` — shared header/footer

## Key Outputs (other students depend on these)
- `$_SESSION['user_id']`, `$_SESSION['role']`, `$_SESSION['name']` set on login
- `users.remember_token` written for persistent login
- `users.shipping_addresses` (JSON) — Task 3 uses this for checkout pre-fill
- `require_admin()` in `config/helpers.php` — Tasks 2 & 4 use this

## DB Tables You Use
```
users (id, name, email, password_hash, phone, role, shipping_addresses, remember_token, created_at)
```

## Requirements Summary
1. **Registration** — name, email, optional phone, password (≥8 chars). Validate email uniqueness. Hash with `password_hash()`. Role defaults to `customer`. Redirect to login on success.
2. **Login** — sets `$_SESSION['user_id']`, `$_SESSION['name']`, `$_SESSION['role']`. Admins → admin dashboard; customers → /products.
3. **Remember Me** — stores hashed token in `users.remember_token`, 30-day cookie. On next visit PHP reinstates the session via `auto_login_from_cookie()`.
4. **Customer Profile** — update name, email, phone, up to 2 shipping addresses (JSON). Password change requires current password verification.
5. **Admin Gate** — `require_admin()` helper in `config/helpers.php` (already present, review it).

## GitHub Collaboration Notes
- Branch name: `feature/task1-auth`
- Only commit files listed under "Files You Own" (plus any new files you create)
- Do NOT modify `database/schema.sql`, `public/index.php`, or any shared config
