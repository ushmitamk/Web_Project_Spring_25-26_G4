<?php
class CartController
{
    public function index()
    {
        $items = Cart::items();
        $reviewed = [];
        if (is_logged_in() && current_role() === 'customer') {
            foreach ($items as $item) {
                $reviewed[(int)$item['id']] = Review::exists($item['id'], current_user_id());
            }
        }
        render('cart/index', ['items' => $items, 'total' => Cart::total(), 'reviewed' => $reviewed]);
    }

    public function apiAdd()
    {
        $data = $_POST ?: input_json();
        $productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
        if ($productId <= 0) json_response(['ok' => false, 'message' => 'Invalid product.'], 422);
        $result = Cart::add($productId);
        json_response($result, $result['ok'] ? 200 : 422);
    }

    public function apiUpdate()
    {
        $data = $_POST ?: input_json();
        $productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 0;
        if ($productId <= 0) json_response(['ok' => false, 'message' => 'Invalid product.'], 422);
        $result = Cart::update($productId, $quantity);
        json_response($result, $result['ok'] ? 200 : 422);
    }

    public function apiRemove()
    {
        $data = $_POST ?: input_json();
        $productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
        if ($productId <= 0) json_response(['ok' => false, 'message' => 'Invalid product.'], 422);
        json_response(Cart::remove($productId));
    }

    public function checkout()
    {
        require_customer();
        $items = Cart::items();
        $user = User::findById(current_user_id());
        $addresses = User::decodeAddresses($user);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($items)) $errors['cart'] = 'Your cart is empty.';
            $payment = $_POST['payment_method'] ?? '';
            if (!in_array($payment, ['Cash', 'Card'], true)) $errors['payment_method'] = 'Choose a payment method.';
            $choice = $_POST['shipping_choice'] ?? '';
            $address = '';
            if ($choice === 'new') {
                $address = trim($_POST['new_address'] ?? '');
            } elseif (is_numeric($choice) && isset($addresses[(int)$choice])) {
                $address = $addresses[(int)$choice];
            }
            if ($address === '') $errors['shipping_address'] = 'Shipping address is required.';
            if (!$errors) {
                try {
                    $orderId = Order::createFromCart(current_user_id(), $address, $payment);
                    redirect('/confirmation/' . $orderId);
                } catch (Exception $e) {
                    $errors['cart'] = $e->getMessage();
                }
            }
        }
        render('cart/checkout', ['items' => $items, 'total' => Cart::total(), 'addresses' => $addresses, 'errors' => $errors]);
    }

    public function confirmation($id)
    {
        require_customer();
        $order = Order::findForUser($id, current_user_id());
        if (!$order) {
            http_response_code(404);
            render('errors/404');
            return;
        }
        render('cart/confirmation', ['order' => $order, 'items' => Order::itemsForOrder($id)]);
    }
}
