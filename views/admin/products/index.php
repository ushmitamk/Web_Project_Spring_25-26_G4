<section class="card wide">
    <div class="section-head">
        <h1>Product Management</h1>
        <a class="btn" href="<?= base_url('/admin/products/create') ?>">Add Product</a>
    </div>
    <table class="data-table admin-product-table">
        <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Avg Rating</th><th>Availability</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr class="<?= (int)$p['stock_qty'] <= 5 ? 'low-stock' : '' ?>">
                <td><?= (int)$p['id'] ?></td>
                <td><img class="thumb" src="<?= asset($p['primary_image_path']) ?>" alt="<?= h($p['name']) ?>"></td>
                <td><?= h($p['name']) ?></td>
                <td><?= h(($p['parent_category_name'] ? $p['parent_category_name'] . ' / ' : '') . $p['category_name']) ?></td>
                <td><?= money($p['price']) ?></td>
                <td><?= (int)$p['stock_qty'] ?></td>
                <td>★ <?= h($p['avg_rating']) ?> (<?= (int)$p['review_count'] ?>)</td>
                <td><button class="availability-badge <?= (int)$p['is_available'] ? 'in' : 'out' ?>" data-product-id="<?= (int)$p['id'] ?>"><?= (int)$p['is_available'] ? 'In Stock' : 'Out of Stock' ?></button></td>
                <td class="actions">
                    <a class="btn small" href="<?= base_url('/admin/products/' . $p['id'] . '/edit') ?>">Edit</a>
                    <form method="post" action="<?= base_url('/admin/products/' . $p['id'] . '/delete') ?>" onsubmit="return confirm('Delete this product?');">
                        <button class="btn small danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p class="muted">Rows with stock quantity ≤ 5 are highlighted as low-stock alerts.</p>
</section>
