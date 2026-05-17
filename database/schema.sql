CREATE DATABASE IF NOT EXISTS ecommerce_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE  ecommerce_store;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(40) NULL,
    role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    account_status ENUM('active','pending','rejected') NOT NULL DEFAULT 'active',
    shipping_addresses JSON NULL,
    remember_token VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    name VARCHAR(120) NOT NULL,
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock_qty INT NOT NULL DEFAULT 0,
    primary_image_path VARCHAR(255) NOT NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method ENUM('Cash','Card') NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL,
    review_text TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review_user_product (product_id, user_id),
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Seed users. Passwords: admin12345 and customer12345.
INSERT IGNORE INTO users (id, name, email, password_hash, phone, role, account_status, shipping_addresses, created_at) VALUES
(1, 'Admin User', 'admin@p4.test', '$2y$12$1UTZxrDoQ9G1zVDgJT0YaOvqEuXVBdKrCyPBPZ4FEip6Xiq5Y.iXm', '01700000001', 'admin', 'active', JSON_ARRAY(), NOW()),
(2, 'Customer User', 'customer@p4.test', '$2y$12$pRGQC3LLYdCjeF3UiHcSIuGCgm8640yCJvJ0icdP8n.qXZ7VUiJuK', '01700000002', 'customer', 'active', JSON_ARRAY('House 10, Road 2, Dhaka, Bangladesh', 'Flat 3B, Mirpur, Dhaka, Bangladesh'), NOW());

INSERT IGNORE INTO categories (id, parent_id, name) VALUES
(1, NULL, 'Electronics'),
(2, NULL, 'Home & Living'),
(3, NULL, 'Fashion');

INSERT IGNORE INTO categories (id, parent_id, name) VALUES
(4, 1, 'Smartphones'),
(5, 1, 'Accessories'),
(6, 2, 'Kitchen'),
(7, 3, 'Bags');

INSERT IGNORE INTO products (id, category_id, name, description, price, stock_qty, primary_image_path, is_available, created_at) VALUES
(1, 4, 'Nova X1 Smartphone', 'A fast smartphone with bright display, long battery life and excellent everyday performance.', 349.99, 12, 'assets/images/sample-phone.png', 1, NOW()),
(2, 5, 'Wireless Earbuds Pro', 'Comfortable wireless earbuds with clear sound, compact case and long playback time.', 79.99, 5, 'assets/images/sample-earbuds.png', 1, NOW()),
(3, 6, 'Smart Blender 500W', 'Kitchen blender with stainless blades, safety lock and multiple speed settings.', 59.50, 3, 'assets/images/sample-blender.png', 1, NOW()),
(4, 7, 'Travel Backpack', 'Durable everyday backpack with laptop compartment and water-resistant fabric.', 45.00, 20, 'assets/images/sample-bag.png', 1, NOW());
