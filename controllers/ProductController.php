<?php
class ProductController
{
    public function index()
    {
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'category_id' => isset($_GET['category_id']) ? (int)$_GET['category_id'] : null,
        ];
        $products = Product::available($filters);
        $categories = Category::hierarchyOptions();
        render('products/index', ['products' => $products, 'categories' => $categories, 'filters' => $filters]);
    }

    public function detail($id)
    {
        $product = Product::find($id);
        if (!$product) {
            http_response_code(404);
            render('errors/404');
            return;
        }
        $canReview = false;
        $hasReviewed = false;
        if (is_logged_in() && current_role() === 'customer') {
            $canReview = Cart::hasProduct($id) || Order::userHasPurchasedProduct(current_user_id(), $id);
            $hasReviewed = Review::exists($id, current_user_id());
        }
        render('products/detail', ['product' => $product, 'canReview' => $canReview, 'hasReviewed' => $hasReviewed]);
    }

    public function apiProducts()
    {
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'category_id' => isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null,
        ];
        $products = Product::available($filters);
        $html = render_to_string('products/_grid', ['products' => $products]);
        json_response(['ok' => true, 'html' => $html]);
    }

    public function apiReviews($id)
    {
        $reviews = Review::forProduct($id);
        $avg = Review::averageForProduct($id);
        json_response(['ok' => true, 'average' => $avg, 'reviews' => $reviews]);
    }
}
