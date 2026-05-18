<?php
class Database
{
    private static $pdo = null;
    private static $migrated = false;

    public static function connection()
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }

    public static function run($sql, $params = [])
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function migrate()
    {
        if (self::$migrated) {
            return;
        }
        self::$migrated = true;

        // Keeps old installations working without manually rebuilding the database.
        if (!self::columnExists('users', 'account_status')) {
            self::connection()->exec("ALTER TABLE users ADD account_status ENUM('active','pending','rejected') NOT NULL DEFAULT 'active' AFTER role");
        }

        if (!self::columnExists('users', 'remember_token')) {
            self::connection()->exec("ALTER TABLE users ADD remember_token VARCHAR(255) NULL AFTER account_status");
        }

        self::connection()->exec("CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            user_id INT NOT NULL,
            rating TINYINT NOT NULL,
            review_text TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_review_user_product (product_id, user_id),
            CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB");
    }

    private static function columnExists($table, $column)
    {
        $stmt = self::run(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?",
            [$table, $column]
        );
        return (int)$stmt->fetchColumn() > 0;
    }
}
