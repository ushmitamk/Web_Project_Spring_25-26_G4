<section class="card wide">
    <div class="section-head">
        <h1>Category Management</h1>
        <a class="btn" href="<?= base_url('/admin/categories/create') ?>">Add Category</a>
    </div>
    <table class="data-table">
        <thead><tr><th>ID</th><th>Name</th><th>Parent</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= (int)$cat['id'] ?></td>
                <td><?= h($cat['name']) ?></td>
                <td><?= h($cat['parent_name'] ?: '—') ?></td>
                <td class="actions">
                    <a class="btn small" href="<?= base_url('/admin/categories/' . $cat['id'] . '/edit') ?>">Edit</a>
                    <form method="post" action="<?= base_url('/admin/categories/' . $cat['id'] . '/delete') ?>" onsubmit="return confirm('Delete this category?');">
                        <button class="btn small danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
