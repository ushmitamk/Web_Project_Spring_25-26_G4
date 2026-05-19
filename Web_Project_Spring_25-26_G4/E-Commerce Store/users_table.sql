
CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

CREATE TABLE IF NOT EXISTS users (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    name               VARCHAR(100)             NOT NULL,
    email              VARCHAR(150)             NOT NULL UNIQUE,
    password_hash      VARCHAR(255)             NOT NULL,
    phone              VARCHAR(20)              DEFAULT NULL,
    role               ENUM('customer','admin') DEFAULT 'customer',
    shipping_addresses JSON                     DEFAULT NULL,
    remember_token     VARCHAR(100)             DEFAULT NULL,
    created_at         TIMESTAMP                DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: admin@shop.com / admin1234
INSERT INTO users (name, email, password_hash, role) VALUES (
    'Admin',
    'admin@shop.com',
    '$2y$10$TKh8H1.PfuAi24a.WKUUC.R1qO0bYFHMl9RZEzp.n6NxPWV8XePEm',
    'admin'
);