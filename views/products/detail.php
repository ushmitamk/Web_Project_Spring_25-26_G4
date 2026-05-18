<section class="detail-layout">
    <div class="detail-image"><img src="<?= asset($product['primary_image_path']) ?>" alt="<?= h($product['name']) ?>"></div>
    <div class="detail-info">
        <span class="category"><?= h(($product['parent_category_name'] ? $product['parent_category_name'] . ' / ' : '') . $product['category_name']) ?></span>
        <h1><?= h($product['name']) ?></h1>
        <p class="price big"><?= money($product['price']) ?></p>
        <p><?= nl2br(h($product['description'])) ?></p>
        <p><strong>Status:</strong> <?= (int)$product['is_available'] && (int)$product['stock_qty'] > 0 ? 'In Stock' : 'Out of Stock' ?> | <strong>Stock:</strong> <?= (int)$product['stock_qty'] ?></p>
        <p id="detail-rating" class="rating">★ <?= h($product['avg_rating']) ?> / 5</p>
        <button class="btn add-to-cart" data-product-id="<?= (int)$product['id'] ?>" <?= ((int)$product['is_available'] !== 1 || (int)$product['stock_qty'] <= 0) ? 'disabled' : '' ?>>Add to Cart</button>
    </div>
</section>

<section class="card wide review-panel">
    <div class="section-head">
        <div>
            <h2>Customer Reviews</h2>
            <p class="muted">Customers can review this product after adding it to the cart or purchasing it.</p>
        </div>
    </div>

    <?php if (is_logged_in() && current_role() === 'customer'): ?>
        <?php if ($hasReviewed): ?>
            <p class="ok review-note">You already reviewed this product.</p>
        <?php elseif ($canReview): ?>
            <form class="review-form review-form-card" data-product-id="<?= (int)$product['id'] ?>">
                <select name="rating" required>
                    <option value="">Rating</option>
                    <?php for ($r = 1; $r <= 5; $r++): ?><option value="<?= $r ?>"><?= $r ?> Star<?= $r > 1 ? 's' : '' ?></option><?php endfor; ?>
                </select>
                <input type="text" name="review_text" placeholder="Write your review" required>
                <button class="btn small" type="submit">Submit Review</button>
                <small class="review-message"></small>
            </form>
        <?php else: ?>
            <p class="muted review-note">Add this product to your cart or purchase it to submit a review.</p>
        <?php endif; ?>
    <?php elseif (!is_logged_in()): ?>
        <p class="muted review-note"><a href="<?= base_url('/login') ?>">Login</a> as a customer to review products.</p>
    <?php endif; ?>

    <div id="product-reviews" data-product-id="<?= (int)$product['id'] ?>" class="reviews-list">Loading reviews...</div>
</section>
