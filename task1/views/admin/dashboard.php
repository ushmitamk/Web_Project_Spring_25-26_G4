<section class="hero admin-hero">
    <div>
        <h1>Admin Dashboard</h1>
        <p>Manage categories, products, availability, orders, fulfilment status, admin requests and catalogue data.</p>
    </div>
</section>
<section class="stats-grid">
    <div class="stat-card"><strong><?= (int)$productCount ?></strong><span>Products</span></div>
    <div class="stat-card"><strong><?= (int)$orderCount ?></strong><span>Orders</span></div>
    <div class="stat-card"><strong><?= (int)$customerCount ?></strong><span>Customers</span></div>
    <div class="stat-card pending-card"><strong><?= (int)$pendingAdminCount ?></strong><span>Pending Admins</span></div>
</section>
<section class="quick-actions">
    <a class="btn" href="<?= base_url('/admin/categories') ?>">Category Management</a>
    <a class="btn" href="<?= base_url('/admin/products') ?>">Product Management</a>
    <a class="btn" href="<?= base_url('/admin/orders') ?>">Order Management</a>
    <a class="btn outline" href="<?= base_url('/admin/admin-requests') ?>">Admin Requests</a>
</section>
