<?php
class Product
{
    private static function ratingJoinSql()
    {
        return "LEFT JOIN (
                    SELECT product_id, ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS review_count
                    FROM reviews
                    GROUP BY product_id
                ) rv ON rv.product_id = p.id";
    }

    private static function ratingSelectSql()
    {
        return "COALESCE(rv.avg_rating, 0) AS avg_rating, COALESCE(rv.review_count, 0) AS review_count";
    }

    public static function available($filters = [])
    {
        $params = [];
        $where = ["p.is_available = 1"];
        if (!empty($filters['q'])) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = '%' . $filters['q'] . '%';
            $params[] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['category_id'])) {
            $where[] = "(p.category_id = ? OR c.parent_id = ?)";
            $params[] = (int)$filters['category_id'];
            $params[] = (int)$filters['category_id'];
        }
        $sql = "SELECT p.*, c.name AS category_name, pc.name AS parent_category_name, " . self::ratingSelectSql() . "
                FROM products p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN categories pc ON c.parent_id = pc.id
                " . self::ratingJoinSql() . "
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.created_at DESC, p.id DESC";
        return Database::run($sql, $params)->fetchAll();
    }

    public static function adminAll()
    {
        $sql = "SELECT p.*, c.name AS category_name, pc.name AS parent_category_name, " . self::ratingSelectSql() . "
                FROM products p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN categories pc ON c.parent_id = pc.id
                " . self::ratingJoinSql() . "
                ORDER BY p.created_at DESC, p.id DESC";
        return Database::run($sql)->fetchAll();
    }

    public static function find($id)
    {
        $sql = "SELECT p.*, c.name AS category_name, pc.name AS parent_category_name, " . self::ratingSelectSql() . "
                FROM products p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN categories pc ON c.parent_id = pc.id
                " . self::ratingJoinSql() . "
                WHERE p.id = ?";
        return Database::run($sql, [(int)$id])->fetch();
    }

    public static function create($data)
    {
        Database::run(
            "INSERT INTO products (category_id, name, description, price, stock_qty, primary_image_path, is_available, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
            [(int)$data['category_id'], $data['name'], $data['description'], (float)$data['price'], (int)$data['stock_qty'], $data['primary_image_path'], (int)$data['is_available']]
        );
    }

    public static function update($id, $data)
    {
        Database::run(
            "UPDATE products
             SET category_id = ?, name = ?, description = ?, price = ?, stock_qty = ?, primary_image_path = ?, is_available = ?
             WHERE id = ?",
            [(int)$data['category_id'], $data['name'], $data['description'], (float)$data['price'], (int)$data['stock_qty'], $data['primary_image_path'], (int)$data['is_available'], (int)$id]
        );
    }

    public static function hasOrderItems($id)
    {
        return (int)Database::run("SELECT COUNT(*) FROM order_items WHERE product_id = ?", [(int)$id])->fetchColumn() > 0;
    }

    public static function delete($id)
    {
        Database::run("DELETE FROM products WHERE id = ?", [(int)$id]);
    }

    public static function toggleAvailability($id)
    {
        $product = self::find($id);
        if (!$product) {
            return null;
        }
        $newValue = (int)!((int)$product['is_available']);
        Database::run("UPDATE products SET is_available = ? WHERE id = ?", [$newValue, (int)$id]);
        return $newValue;
    }

    public static function decrementStock($id, $quantity)
    {
        Database::run("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ? AND stock_qty >= ?", [(int)$quantity, (int)$id, (int)$quantity]);
    }

    public static function countAll()
    {
        return (int)Database::run("SELECT COUNT(*) FROM products")->fetchColumn();
    }
}
