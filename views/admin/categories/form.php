<section class="card">
    <h1><?= $category ? 'Edit Category' : 'Add Category' ?></h1>
    <form method="post" novalidate>
        <label>Category Name</label>
        <input type="text" name="name" value="<?= h($old['name']) ?>">
        <?php if (!empty($errors['name'])): ?><small class="field-error"><?= h($errors['name']) ?></small><?php endif; ?>

        <label>Parent Category</label>
        <select name="parent_id">
            <option value="">None (Top-level)</option>
            <?php foreach ($parents as $parent): ?>
                <option value="<?= (int)$parent['id'] ?>" <?= (int)($old['parent_id'] ?? 0) === (int)$parent['id'] ? 'selected' : '' ?>><?= h($parent['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['parent_id'])): ?><small class="field-error"><?= h($errors['parent_id']) ?></small><?php endif; ?>
        <button class="btn" type="submit">Save Category</button>
        <a class="btn outline" href="<?= base_url('/admin/categories') ?>">Cancel</a>
    </form>
</section>
