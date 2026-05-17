<section class="card wide">
    <h1>Admin Order Management</h1>
    <form method="get" class="filter-bar">
        <select name="status">
            <option value="">All Statuses</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= h($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="date_from" value="<?= h($filters['date_from']) ?>">
        <input type="date" name="date_to" value="<?= h($filters['date_to']) ?>">
        <button class="btn small" type="submit">Filter</button>
        <a class="btn small outline" href="<?= base_url('/admin/orders') ?>">Reset</a>
    </form>
    <table class="data-table">
        <thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Change Status</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr data-order-id="<?= (int)$order['id'] ?>">
                <td>#<?= (int)$order['id'] ?></td>
                <td><?= h($order['customer_name']) ?><br><small><?= h($order['customer_email']) ?></small></td>
                <td><?= h($order['created_at']) ?></td>
                <td><?= money($order['total_amount']) ?></td>
                <td><span class="order-status <?= status_badge_class($order['status']) ?>"><?= h($order['status']) ?></span></td>
                <td>
                    <select class="admin-status-select">
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= h($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="status-message"></small>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
