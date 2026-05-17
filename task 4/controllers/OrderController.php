<?php
class OrderController
{
    public function myOrders()
    {
        require_customer();
        $orders = Order::userOrders(current_user_id());
        $itemsByOrder = [];
        $reviewed = [];
        foreach ($orders as $order) {
            $itemsByOrder[(int)$order['id']] = Order::itemsForOrder($order['id']);
            foreach ($itemsByOrder[(int)$order['id']] as $item) {
                $reviewed[(int)$item['product_id']] = Review::exists($item['product_id'], current_user_id());
            }
        }
        render('orders/my_orders', ['orders' => $orders, 'itemsByOrder' => $itemsByOrder, 'reviewed' => $reviewed]);
    }
}
