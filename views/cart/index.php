<section class="card wide">
    <h1>Shopping Cart</h1>
    <div id="cart-area">
    <?php if (empty($items)): ?>
        <p class="empty">Your cart is empty.</p>
        <a class="btn" href="<?= base_url('/products') ?>">Browse Products</a>
    <?php else: ?>
        <table class="data-table cart-table">
            <thead><tr><th>Product</th><th>Unit Price</th><th>Quantity</th><th>Line Total</th><th>Review</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr data-product-id="<?= (int)$item['id'] ?>">
                    <td><?= h($item['name']) ?></td>
                    <td><?= money($item['price']) ?></td>
                    <td>
                        <button class="qty-btn" data-action="minus">−</button>
                        <span class="qty"><?= (int)$item['quantity'] ?></span>
                        <button class="qty-btn" data-action="plus">+</button>
                        <small class="muted">Max <?= (int)$item['stock_qty'] ?></small>
                    </td>
                    <td class="line-total"><?= money($item['line_total']) ?></td>
                    <td>
                        <?php if (is_logged_in() && current_role() === 'customer'): ?>
                            <?php if (!empty($reviewed[(int)$item['id']])): ?>
                                <span class="ok">Reviewed</span>
                            <?php else: ?>
                                <form class="review-form compact-review-form" data-product-id="<?= (int)$item['id'] ?>">
                                    <select name="rating" required>
                                        <option value="">Rating</option>
                                        <?php for ($r = 1; $r <= 5; $r++): ?><option value="<?= $r ?>"><?= $r ?> Star<?= $r > 1 ? 's' : '' ?></option><?php endfor; ?>
                                    </select>
                                    <input type="text" name="review_text" placeholder="Review" required>
                                    <button class="btn small" type="submit">Submit</button>
                                    <small class="review-message"></small>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="muted">Customer login required</span>
                        <?php endif; ?>
                    </td>
                    <td><button class="btn small danger remove-cart">Remove</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="totals">Grand Total: <strong id="grand-total"><?= money($total) ?></strong></div>
        <a class="btn" href="<?= base_url('/checkout') ?>">Proceed to Checkout</a>
    <?php endif; ?>
    </div>
</section>
