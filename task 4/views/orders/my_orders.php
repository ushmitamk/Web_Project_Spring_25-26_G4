<section class="card wide">
    <h1>My Orders</h1>
    <?php if (empty($orders)): ?>
        <p class="empty">No orders yet.</p>
    <?php else: ?>
        <table class="data-table orders-table">
            <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th><th>Details</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= (int)$order['id'] ?></td>
                    <td><?= h($order['created_at']) ?></td>
                    <td><?= money($order['total_amount']) ?></td>
                    <td><span class="<?= status_badge_class($order['status']) ?>"><?= h($order['status']) ?></span></td>
                    <td><button class="btn small toggle-order" data-target="order-<?= (int)$order['id'] ?>">View</button></td>
                </tr>
                <tr id="order-<?= (int)$order['id'] ?>" class="order-details hidden">
                    <td colspan="5">
                        <strong>Shipping:</strong> <?= nl2br(h($order['shipping_address'])) ?><br>
                        <strong>Payment:</strong> <?= h($order['payment_method']) ?>
                        <table class="data-table nested">
                            <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Review</th></tr></thead>
                            <tbody>
                            <?php foreach ($itemsByOrder[(int)$order['id']] as $item): ?>
                                <tr>
                                    <td><?= h($item['name']) ?></td>
                                    <td><?= (int)$item['quantity'] ?></td>
                                    <td><?= money($item['unit_price']) ?></td>
                                    <td>
                                        <?php if (!empty($reviewed[(int)$item['product_id']])): ?>
                                            <span class="ok">Reviewed</span>
                                        <?php else: ?>
                                            <form class="review-form" data-product-id="<?= (int)$item['product_id'] ?>">
                                                <select name="rating" required>
                                                    <option value="">Rating</option>
                                                    <?php for ($r = 1; $r <= 5; $r++): ?><option value="<?= $r ?>"><?= $r ?> Star<?= $r > 1 ? 's' : '' ?></option><?php endfor; ?>
                                                </select>
                                                <input type="text" name="review_text" placeholder="Write review" required>
                                                <button class="btn small" type="submit">Submit</button>
                                                <small class="review-message"></small>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
