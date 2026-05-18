<?php
$success = get_flash('success');
$error = get_flash('error');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePathForBack = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$relativePath = $currentPath;
if ($basePathForBack !== '' && $basePathForBack !== '/' && substr($relativePath, 0, strlen($basePathForBack)) === $basePathForBack) {
    $relativePath = substr($relativePath, strlen($basePathForBack));
}
$relativePath = '/' . trim($relativePath, '/');
$showBackButton = !in_array($relativePath, ['/', '/products'], true);
$backFallback = current_role() === 'admin' ? base_url('/admin') : base_url('/products');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShopNest</title>
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= base_url('/products') ?>">ShopNest</a>
    <nav class="nav">
        <a href="<?= base_url('/products') ?>">Products</a>
        <a href="<?= base_url('/cart') ?>">Cart <span id="cart-count" class="pill"><?= Cart::countItems() ?></span></a>
        <?php if (is_logged_in() && current_role() === 'customer'): ?>
            <a href="<?= base_url('/my-orders') ?>">My Orders</a>
            <a href="<?= base_url('/profile') ?>">Profile</a>
        <?php endif; ?>
        <?php if (is_logged_in() && current_role() === 'admin'): ?>
            <a href="<?= base_url('/admin') ?>">Admin</a>
            <a href="<?= base_url('/admin/products') ?>">Product Management</a>
            <a href="<?= base_url('/admin/orders') ?>">Orders</a>
            <a href="<?= base_url('/admin/admin-requests') ?>">Admin Requests</a>
            <a href="<?= base_url('/profile') ?>">Profile</a>
        <?php endif; ?>
        <?php if (is_logged_in()): ?>
            <span class="hello">Hi, <?= h($_SESSION['name']) ?></span>
            <a class="btn small" href="<?= base_url('/logout') ?>">Logout</a>
        <?php else: ?>
            <a class="btn small" href="<?= base_url('/login') ?>">Login</a>
            <a class="btn small outline" href="<?= base_url('/register') ?>">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
    <?php if ($showBackButton): ?>
        <div class="page-tools"><button type="button" class="btn small outline page-back" data-fallback="<?= h($backFallback) ?>">← Back</button></div>
    <?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?= h($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
