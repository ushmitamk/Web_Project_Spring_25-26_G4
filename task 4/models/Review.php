<?php
class Review
{
    public static function forProduct($productId)
    {
        return Database::run(
            "SELECT r.*, u.name AS reviewer_name
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.product_id = ?
             ORDER BY r.created_at DESC",
            [(int)$productId]
        )->fetchAll();
    }

    public static function averageForProduct($productId)
    {
        return Database::run("SELECT COALESCE(ROUND(AVG(rating), 1), 0) FROM reviews WHERE product_id = ?", [(int)$productId])->fetchColumn();
    }

    public static function exists($productId, $userId)
    {
        return (int)Database::run(
            "SELECT COUNT(*) FROM reviews WHERE product_id = ? AND user_id = ?",
            [(int)$productId, (int)$userId]
        )->fetchColumn() > 0;
    }

    public static function create($productId, $userId, $rating, $text)
    {
        Database::run(
            "INSERT INTO reviews (product_id, user_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())",
            [(int)$productId, (int)$userId, (int)$rating, $text]
        );
    }
}
