<section class="auth-card auth-card-modern">
    <h1>Create Account</h1>
    <p class="muted">Choose Customer for shopping access or Admin to send an approval request to the default Admin.</p>
    <form method="post" novalidate>
        <label>Name</label>
        <input type="text" name="name" value="<?= h($old['name']) ?>">
        <?php if (!empty($errors['name'])): ?><small class="field-error"><?= h($errors['name']) ?></small><?php endif; ?>

        <label>Email</label>
        <input type="email" name="email" value="<?= h($old['email']) ?>">
        <?php if (!empty($errors['email'])): ?><small class="field-error"><?= h($errors['email']) ?></small><?php endif; ?>

        <label>Phone <span class="muted">optional</span></label>
        <input type="text" name="phone" value="<?= h($old['phone']) ?>">

        <label>Register As</label>
        <div class="role-choice">
            <label class="role-option">
                <input type="radio" name="role" value="customer" <?= ($old['role'] ?? 'customer') === 'customer' ? 'checked' : '' ?>>
                <span><strong>Customer</strong><small>Shop, cart, checkout and review products.</small></span>
            </label>
            <label class="role-option">
                <input type="radio" name="role" value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'checked' : '' ?>>
                <span><strong>Admin</strong><small>Requires approval before Admin Panel access.</small></span>
            </label>
        </div>
        <?php if (!empty($errors['role'])): ?><small class="field-error"><?= h($errors['role']) ?></small><?php endif; ?>

        <label>Password</label>
        <input type="password" name="password" placeholder="Minimum 8 characters">
        <?php if (!empty($errors['password'])): ?><small class="field-error"><?= h($errors['password']) ?></small><?php endif; ?>

        <button class="btn full-width" type="submit">Register</button>
    </form>
    <p class="center">Already have an account? <a href="<?= base_url('/login') ?>">Login</a></p>
</section>
