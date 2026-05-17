<?php
class ReviewController
{
    public function apiCreate()
    {
        require_customer();
        $data = $_POST ?: input_json();
        $productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
        $rating = isset($data['rating']) ? (int)$data['rating'] : 0;
        $text = trim($data['review_text'] ?? '');
        if ($productId <= 0) json_response(['ok' => false, 'message' => 'Invalid product.'], 422);
        if ($rating < 1 || $rating > 5) json_response(['ok' => false, 'message' => 'Rating must be between 1 and 5.'], 422);
        if ($text === '') json_response(['ok' => false, 'message' => 'Review text is required.'], 422);
        if (!Cart::hasProduct($productId) && !Order::userHasPurchasedProduct(current_user_id(), $productId)) {
            json_response(['ok' => false, 'message' => 'You can review only products that are in your cart or purchase history.'], 403);
        }
        if (Review::exists($productId, current_user_id())) {
            json_response(['ok' => false, 'message' => 'You already reviewed this product.'], 409);
        }
        try {
            Review::create($productId, current_user_id(), $rating, $text);
            json_response(['ok' => true, 'review' => ['reviewer_name' => $_SESSION['name'], 'rating' => $rating, 'review_text' => $text, 'created_at' => date('Y-m-d H:i:s')]]);
        } catch (Exception $e) {
            json_response(['ok' => false, 'message' => 'Review could not be saved. It may already exist.'], 409);
        }
    }
}
