<section class="hero">
    <div>
        <h1>Product Marketplace</h1>
        <p>Explore products by category, pick your favorites, add to cart, and enjoy a smooth checkout experience</p>
    </div>
    <?php if (!is_logged_in()): ?>
        <a class="btn" href="<?= base_url('/login') ?>">Login to Checkout</a>
    <?php endif; ?>
</section>

<section class="toolbar">
    <input id="product-search" type="search" placeholder="Search products..." value="<?= h($filters['q']) ?>">
    <select id="category-filter">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= (int)($filters['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>><?= h($cat['label']) ?></option>
        <?php endforeach; ?>
    </select>
</section>

<section id="product-grid" class="product-grid">
    <?php render('products/_grid', ['products' => $products], false); ?>
</section>
