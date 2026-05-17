<section class="card wide">
    <div class="section-head">
        <div>
            <h1>Pending Admin Requests</h1>
            <p class="muted">Approve only trusted users. Pending admins cannot access the Admin Panel until approval.</p>
        </div>
        <a class="btn outline" href="<?= base_url('/admin') ?>">Back to Dashboard</a>
    </div>

    <?php if (empty($admins)): ?>
        <p class="empty">No pending admin registration requests.</p>
    <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Requested At</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?= h($admin['name']) ?></td>
                    <td><?= h($admin['email']) ?></td>
                    <td><?= h($admin['phone'] ?: '—') ?></td>
                    <td><?= h($admin['created_at']) ?></td>
                    <td><span class="badge badge-pending">Pending</span></td>
                    <td class="actions">
                        <form method="post" action="<?= base_url('/admin/admin-requests/' . $admin['id'] . '/approve') ?>" onsubmit="return confirm('Approve this admin account?');">
                            <button class="btn small" type="submit">Approve</button>
                        </form>
                        <form method="post" action="<?= base_url('/admin/admin-requests/' . $admin['id'] . '/reject') ?>" onsubmit="return confirm('Reject this admin request?');">
                            <button class="btn small danger" type="submit">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
