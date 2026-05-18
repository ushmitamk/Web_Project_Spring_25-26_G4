<section class="auth-card">
    <h1>Login</h1>
    <?php if (!empty($errors['login'])): ?>
        <div class="alert error">
            <?= h($errors['login']) ?>
        </div><?php endif; ?>
    <form method="post" novalidate>
        <label>Email</label>
        <input type="email" name="email" value="<?= h($email) ?>" required>
        <label>Password</label>
        <input type="password" name="password" required>
        
        <br><br>
       
        <button class="btn" type="submit">Login</button>
    
<label><input type="checkbox" name="remember_me" value="1"> Remember Me for 30 days</label>
</form>
    <p class="center">No account? <a href="<?= base_url('/register') ?>">Register</a></p>
    
</section>
