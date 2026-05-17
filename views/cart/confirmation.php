<section class="card wide center">
    <h1>Order Confirmed</h1>
    <p>Your order <strong>#<?= (int)$order['id'] ?></strong> has been placed successfully.</p>
    <p>Status: <span class="<?= status_badge_class($order['status']) ?>"><?= h($order['status']) ?></span></p>
    <table class="data-table">
        <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr><td><?= h($item['name']) ?></td><td><?= (int)$item['quantity'] ?></td><td><?= money($item['unit_price']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p>Total: <strong><?= money($order['total_amount']) ?></strong></p>
    <a class="btn" href="<?= base_url('/my-orders') ?>">View My Orders</a>
</section>
