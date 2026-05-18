<?php
class Cart
{
    public static function get()
    {
        if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        return $_SESSION['cart'];
    }

    public static function countItems()
    {
        $total = 0;
        foreach (self::get() as $qty) {
            $total += (int)$qty;
        }
        return $total;
    }

    public static function add($productId)
    {
        $product = Product::find($productId);
        if (!$product || (int)$product['is_available'] !== 1) {
            return ['ok' => false, 'message' => 'Product is not available.'];
        }
        if ((int)$product['stock_qty'] < 1) {
            return ['ok' => false, 'message' => 'Product is out of stock.'];
        }
        self::get();
        $current = isset($_SESSION['cart'][$productId]) ? (int)$_SESSION['cart'][$productId] : 0;
        $_SESSION['cart'][$productId] = min($current + 1, (int)$product['stock_qty']);
        return ['ok' => true, 'cart_count' => self::countItems(), 'quantity' => $_SESSION['cart'][$productId]];
    }

    public static function update($productId, $quantity)
    {
        $product = Product::find($productId);
        if (!$product) {
            return ['ok' => false, 'message' => 'Product not found.'];
        }
        self::get();
        $quantity = max(0, (int)$quantity);
        $quantity = min($quantity, (int)$product['stock_qty']);
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        $items = self::items();
        return [
            'ok' => true,
            'quantity' => $quantity,
            'line_total' => isset($items[$productId]) ? $items[$productId]['line_total'] : 0,
            'grand_total' => self::total(),
            'cart_count' => self::countItems(),
        ];
    }

    public static function remove($productId)
    {
        self::get();
        unset($_SESSION['cart'][$productId]);
        return ['ok' => true, 'grand_total' => self::total(), 'cart_count' => self::countItems()];
    }

    public static function clear()
    {
        $_SESSION['cart'] = [];
    }

    public static function hasProduct($productId)
    {
        $cart = self::get();
        return isset($cart[(int)$productId]) && (int)$cart[(int)$productId] > 0;
    }

    public static function items()
    {
        $cart = self::get();
        if (empty($cart)) {
            return [];
        }
        $ids = array_map('intval', array_keys($cart));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = Database::run("SELECT * FROM products WHERE id IN ($placeholders)", $ids)->fetchAll();
        $items = [];
        foreach ($rows as $product) {
            $id = (int)$product['id'];
            $qty = min((int)$cart[$id], (int)$product['stock_qty']);
            if ($qty < 1) {
                unset($_SESSION['cart'][$id]);
                continue;
            }
            $product['quantity'] = $qty;
            $product['line_total'] = $qty * (float)$product['price'];
            $items[$id] = $product;
        }
        return $items;
    }

    public static function total()
    {
        $total = 0;
        foreach (self::items() as $item) {
            $total += (float)$item['line_total'];
        }
        return $total;
    }
}
