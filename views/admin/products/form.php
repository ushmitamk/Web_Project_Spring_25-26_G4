<section class="card wide">
    <h1><?= $product ? 'Edit Product' : 'Add Product' ?></h1>
    <form method="post" enctype="multipart/form-data" class="grid-form" novalidate>
        <div>
            <label>Name</label>
            <input type="text" name="name" value="<?= h($old['name']) ?>">
            <?php if (!empty($errors['name'])): ?><small class="field-error"><?= h($errors['name']) ?></small><?php endif; ?>
        </div>
        <div>
            <label>Category</label>
            <select name="category_id">
                <option value="">Choose category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>" <?= (int)($old['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>><?= h($cat['label']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['category_id'])): ?><small class="field-error"><?= h($errors['category_id']) ?></small><?php endif; ?>
        </div>
        <div>
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="<?= h($old['price']) ?>">
            <?php if (!empty($errors['price'])): ?><small class="field-error"><?= h($errors['price']) ?></small><?php endif; ?>
        </div>
        <div>
            <label>Stock Quantity</label>
            <input type="number" name="stock_qty" min="0" value="<?= h($old['stock_qty']) ?>">
            <?php if (!empty($errors['stock_qty'])): ?><small class="field-error"><?= h($errors['stock_qty']) ?></small><?php endif; ?>
        </div>
        <div class="full">
            <label>Description</label>
            <textarea name="description" rows="5"><?= h($old['description']) ?></textarea>
            <?php if (!empty($errors['description'])): ?><small class="field-error"><?= h($errors['description']) ?></small><?php endif; ?>
        </div>
        <div>
            <label>Primary Image <?= $product ? '<span class="muted">leave empty to keep current</span>' : '' ?></label>
            <input type="file" name="primary_image" accept="image/png,image/jpeg">
            <?php if (!empty($errors['primary_image_path'])): ?><small class="field-error"><?= h($errors['primary_image_path']) ?></small><?php endif; ?>
        </div>
        <?php if (!empty($old['primary_image_path'])): ?>
            <div><label>Current Image</label><img class="preview" src="<?= asset($old['primary_image_path']) ?>" alt="Current image"></div>
        <?php endif; ?>
        <div class="full"><label class="inline"><input type="checkbox" name="is_available" value="1" <?= (int)($old['is_available'] ?? 1) === 1 ? 'checked' : '' ?>> Available / In Stock badge</label></div>
        <div class="full">
            <button class="btn" type="submit">Save Product</button>
            <a class="btn outline" href="<?= base_url('/admin/products') ?>">Cancel</a>
        </div>
    </form>
</section>
