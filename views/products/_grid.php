<?php if (empty($products)): ?>
    <div class="empty">No products found.</div>
<?php endif; ?>
<?php foreach ($products as $product): ?>
    <article class="product-card">
        <a href="<?= base_url('/products/' . $product['id']) ?>">
            <img src="<?= asset($product['primary_image_path']) ?>" alt="<?= h($product['name']) ?>">
        </a>
        <div class="product-info">
            <span class="category"><?= h(($product['parent_category_name'] ? $product['parent_category_name'] . ' / ' : '') . $product['category_name']) ?></span>
            <h3><a href="<?= base_url('/products/' . $product['id']) ?>"><?= h($product['name']) ?></a></h3>
            <p class="price"><?= money($product['price']) ?></p>
            <p class="rating">★ <?= h($product['avg_rating']) ?> / 5 (<?= (int)$product['review_count'] ?>)</p>
            <p class="muted">Stock: <?= (int)$product['stock_qty'] ?></p>
            <button class="btn add-to-cart" data-product-id="<?= (int)$product['id'] ?>" <?= (int)$product['stock_qty'] <= 0 ? 'disabled' : '' ?>>Add to Cart</button>
        </div>
    </article>
<?php endforeach; ?>
