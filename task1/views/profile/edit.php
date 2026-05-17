<section class="profile-shell">
    <div class="profile-hero-card">
        <div class="avatar-circle"><?= h(strtoupper(substr($user['name'] ?? 'U', 0, 1))) ?></div>
        <div>
            <span class="category"><?= h(ucfirst($user['role'] ?? 'user')) ?> Profile</span>
            <h1><?= h($user['name']) ?></h1>
            <p><?= h($user['email']) ?></p>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <span class="badge badge-processing">Admin Panel Access: <?= h(ucfirst($user['account_status'] ?? 'active')) ?></span>
            <?php else: ?>
                <span class="badge badge-shipped">Customer Account</span>
            <?php endif; ?>
        </div>
    </div>

    <section class="card wide profile-card-modern">
        <div class="section-head">
            <div>
                <h1>My Profile</h1>
                <p class="muted">Update your account details, saved shipping addresses and password.</p>
            </div>
        </div>
        <form method="post" class="grid-form profile-form" novalidate>
            <div>
                <label>Name</label>
                <input type="text" name="name" value="<?= h($user['name']) ?>">
                <?php if (!empty($errors['name'])): ?><small class="field-error"><?= h($errors['name']) ?></small><?php endif; ?>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?= h($user['email']) ?>">
                <?php if (!empty($errors['email'])): ?><small class="field-error"><?= h($errors['email']) ?></small><?php endif; ?>
            </div>
            <div>
                <label>Phone</label>
                <input type="text" name="phone" value="<?= h($user['phone'] ?? '') ?>">
            </div>
            <div>
                <label>Account Role</label>
                <input type="text" value="<?= h(ucfirst($user['role'] ?? 'user')) ?>" readonly>
            </div>
            <div>
                <label>Saved Shipping Address 1</label>
                <textarea name="address_1" rows="3"><?= h($addresses[0] ?? '') ?></textarea>
            </div>
            <div>
                <label>Saved Shipping Address 2</label>
                <textarea name="address_2" rows="3"><?= h($addresses[1] ?? '') ?></textarea>
            </div>
            <div class="full password-panel"><h3>Change Password</h3><p class="muted">Leave both password fields empty if you do not want to change your password.</p></div>
            <div>
                <label>Current Password</label>
                <input type="password" name="current_password">
                <?php if (!empty($errors['current_password'])): ?><small class="field-error"><?= h($errors['current_password']) ?></small><?php endif; ?>
            </div>
            <div>
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Minimum 8 characters">
                <?php if (!empty($errors['new_password'])): ?><small class="field-error"><?= h($errors['new_password']) ?></small><?php endif; ?>
            </div>
            <div class="full profile-actions">
                <button class="btn" type="submit">Save Profile</button>
                <a class="btn outline" href="<?= current_role() === 'admin' ? base_url('/admin') : base_url('/products') ?>">Cancel</a>
            </div>
        </form>
    </section>
</section>
