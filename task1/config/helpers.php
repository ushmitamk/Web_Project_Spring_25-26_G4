<?php
function start_secure_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function base_url($path = '')
{
    $path = '/' . ltrim((string)$path, '/');
    return rtrim(BASE_URL, '/') . ($path === '/' ? '/' : $path);
}

function asset($path)
{
    $path = ltrim((string)$path, '/');
    return base_url($path);
}

function redirect($path)
{
    header('Location: ' . base_url($path));
    exit;
}

function is_logged_in()
{
    return !empty($_SESSION['user_id']);
}

function current_user_id()
{
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_role()
{
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function require_login()
{
    if (!is_logged_in()) {
        flash('error', 'Please login first.');
        redirect('/login');
    }
}

function require_admin()
{
    require_login();
    if (current_role() !== 'admin') {
        flash('error', 'Admin access required.');
        redirect('/login');
    }
    $user = User::findById(current_user_id());
    if (!User::isActiveAdmin($user)) {
        flash('error', 'Your admin account is not active yet.');
        $_SESSION = [];
        redirect('/login');
    }
}

function require_customer()
{
    require_login();
    if (current_role() !== 'customer') {
        flash('error', 'Customer access required.');
        redirect('/products');
    }
}

function flash($key, $message)
{
    $_SESSION['flash'][$key] = $message;
}

function get_flash($key)
{
    if (!empty($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

function render($view, $data = [], $layout = true)
{
    extract($data);
    $viewFile = ROOT_PATH . '/views/' . $view . '.php';
    if (!file_exists($viewFile)) {
        throw new Exception('View not found: ' . $view);
    }
    if ($layout) {
        include ROOT_PATH . '/views/layouts/header.php';
    }
    include $viewFile;
    if ($layout) {
        include ROOT_PATH . '/views/layouts/footer.php';
    }
}

function render_to_string($view, $data = [])
{
    ob_start();
    render($view, $data, false);
    return ob_get_clean();
}

function json_response($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function input_json()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function money($amount)
{
    return '$' . number_format((float)$amount, 2);
}

function status_badge_class($status)
{
    $map = [
        'Pending' => 'badge badge-pending',
        'Processing' => 'badge badge-processing',
        'Shipped' => 'badge badge-shipped',
        'Delivered' => 'badge badge-delivered',
        'Cancelled' => 'badge badge-cancelled',
    ];
    return isset($map[$status]) ? $map[$status] : 'badge';
}

function category_label($category)
{
    return !empty($category['parent_name']) ? $category['parent_name'] . ' / ' . $category['name'] : $category['name'];
}

function auto_login_from_cookie()
{
    if (is_logged_in() || empty($_COOKIE['remember_me'])) {
        return;
    }
    $parts = explode(':', $_COOKIE['remember_me'], 2);
    if (count($parts) !== 2) {
        return;
    }
    $userId = (int)$parts[0];
    $token = $parts[1];
    if ($userId <= 0 || $token === '') {
        return;
    }
    $user = User::findById($userId);
    if ($user && ($user['role'] !== 'admin' || (($user['account_status'] ?? 'active') === 'active')) && !empty($user['remember_token']) && password_verify($token, $user['remember_token'])) {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
    }
}
