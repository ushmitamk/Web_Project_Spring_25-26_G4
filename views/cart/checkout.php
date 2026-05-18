<section class="card wide">
    <h1>Checkout</h1>
    <?php if (!empty($errors['cart'])): ?><div class="alert error"><?= h($errors['cart']) ?></div><?php endif; ?>
    <?php if (empty($items)): ?>
        <p class="empty">Your cart is empty.</p>
        <a class="btn" href="<?= base_url('/products') ?>">Browse Products</a>
    <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr><td><?= h($item['name']) ?></td><td><?= (int)$item['quantity'] ?></td><td><?= money($item['price']) ?></td><td><?= money($item['line_total']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="totals">Total: <strong><?= money($total) ?></strong></div>
        <form method="post" class="checkout-form" novalidate>
            <h3>Shipping Address</h3>
            <?php foreach ($addresses as $i => $address): ?>
                <label class="radio-card"><input type="radio" name="shipping_choice" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?>> <?= nl2br(h($address)) ?></label>
            <?php endforeach; ?>
            <label class="radio-card"><input type="radio" name="shipping_choice" value="new" <?= empty($addresses) ? 'checked' : '' ?>> Use a new address</label>
            <textarea name="new_address" rows="3" placeholder="New shipping address"></textarea>
            <?php if (!empty($errors['shipping_address'])): ?><small class="field-error"><?= h($errors['shipping_address']) ?></small><?php endif; ?>
            <h3>Payment Method</h3>
            <label class="inline"><input type="radio" name="payment_method" value="Cash"> Cash</label>
            <label class="inline"><input type="radio" name="payment_method" value="Card"> Card</label>
            <?php if (!empty($errors['payment_method'])): ?><small class="field-error"><?= h($errors['payment_method']) ?></small><?php endif; ?>
            <br><button class="btn" type="submit">Place Order</button>
        </form>
    <?php endif; ?>
</section>
