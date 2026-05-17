<?php
class Order
{
    public static $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

    public static function createFromCart($userId, $shippingAddress, $paymentMethod)
    {
        $items = Cart::items();
        if (empty($items)) {
            throw new Exception('Cart is empty.');
        }
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $total = 0;
            foreach ($items as $item) {
                $fresh = Product::find($item['id']);
                if (!$fresh || (int)$fresh['stock_qty'] < (int)$item['quantity']) {
                    throw new Exception('Not enough stock for ' . $item['name']);
                }
                $total += (float)$item['price'] * (int)$item['quantity'];
            }
            Database::run(
                "INSERT INTO orders (user_id, shipping_address, payment_method, total_amount, status, created_at)
                 VALUES (?, ?, ?, ?, 'Pending', NOW())",
                [(int)$userId, $shippingAddress, $paymentMethod, $total]
            );
            $orderId = (int)$pdo->lastInsertId();
            foreach ($items as $item) {
                Database::run(
                    "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)",
                    [$orderId, (int)$item['id'], (int)$item['quantity'], (float)$item['price']]
                );
                Product::decrementStock($item['id'], $item['quantity']);
            }
            $pdo->commit();
            Cart::clear();
            return $orderId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function find($id)
    {
        return Database::run("SELECT * FROM orders WHERE id = ?", [(int)$id])->fetch();
    }

    public static function findForUser($id, $userId)
    {
        return Database::run("SELECT * FROM orders WHERE id = ? AND user_id = ?", [(int)$id, (int)$userId])->fetch();
    }

    public static function userOrders($userId)
    {
        return Database::run("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [(int)$userId])->fetchAll();
    }

    public static function itemsForOrder($orderId)
    {
        return Database::run(
            "SELECT oi.*, p.name, p.primary_image_path
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             ORDER BY oi.id",
            [(int)$orderId]
        )->fetchAll();
    }

    public static function adminOrders($filters = [])
    {
        $params = [];
        $where = [];
        if (!empty($filters['status'])) {
            $where[] = 'o.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'DATE(o.created_at) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'DATE(o.created_at) <= ?';
            $params[] = $filters['date_to'];
        }
        $sql = "SELECT o.*, u.name AS customer_name, u.email AS customer_email
                FROM orders o
                JOIN users u ON u.id = o.user_id";
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY o.created_at DESC';
        return Database::run($sql, $params)->fetchAll();
    }

    public static function canMoveTo($current, $newStatus)
    {
        if ($current === $newStatus) {
            return true;
        }
        if ($newStatus === 'Cancelled') {
            return true;
        }
        $sequence = [
            'Pending' => 'Processing',
            'Processing' => 'Shipped',
            'Shipped' => 'Delivered',
        ];
        return isset($sequence[$current]) && $sequence[$current] === $newStatus;
    }

    public static function updateStatus($id, $status)
    {
        Database::run("UPDATE orders SET status = ? WHERE id = ?", [$status, (int)$id]);
    }

    public static function userHasDeliveredProduct($userId, $productId)
    {
        return (int)Database::run(
            "SELECT COUNT(*)
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'Delivered'",
            [(int)$userId, (int)$productId]
        )->fetchColumn() > 0;
    }

    public static function userHasPurchasedProduct($userId, $productId)
    {
        return (int)Database::run(
            "SELECT COUNT(*)
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = ? AND oi.product_id = ?",
            [(int)$userId, (int)$productId]
        )->fetchColumn() > 0;
    }

    public static function countAll()
    {
        return (int)Database::run("SELECT COUNT(*) FROM orders")->fetchColumn();
    }
}
