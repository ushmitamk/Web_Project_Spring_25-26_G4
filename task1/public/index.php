<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/helpers.php';

foreach (glob(ROOT_PATH . '/models/*.php') as $file) require_once $file;
foreach (glob(ROOT_PATH . '/controllers/*.php') as $file) require_once $file;

Database::migrate();
start_secure_session();
auto_login_from_cookie();

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($basePath !== '' && $basePath !== '/' && substr($uri, 0, strlen($basePath)) === $basePath) {
    $uri = substr($uri, strlen($basePath));
}
$path = '/' . trim($uri, '/');
if ($path === '//') $path = '/';

$routes = [
    ['GET', '#^/$#', [ProductController::class, 'index']],
    ['GET', '#^/products$#', [ProductController::class, 'index']],
    ['GET', '#^/products/(\d+)$#', [ProductController::class, 'detail']],
    ['GET', '#^/api/products$#', [ProductController::class, 'apiProducts']],
    ['GET', '#^/api/products/search$#', [ProductController::class, 'apiProducts']],
    ['GET', '#^/api/products/(\d+)/reviews$#', [ProductController::class, 'apiReviews']],

    ['GET', '#^/register$#', [AuthController::class, 'register']],
    ['POST', '#^/register$#', [AuthController::class, 'register']],
    ['GET', '#^/login$#', [AuthController::class, 'login']],
    ['POST', '#^/login$#', [AuthController::class, 'login']],
    ['GET', '#^/logout$#', [AuthController::class, 'logout']],
    ['GET', '#^/profile$#', [ProfileController::class, 'edit']],
    ['POST', '#^/profile$#', [ProfileController::class, 'edit']],

    ['GET', '#^/cart$#', [CartController::class, 'index']],
    ['POST', '#^/api/cart/add$#', [CartController::class, 'apiAdd']],
    ['POST', '#^/api/cart/update$#', [CartController::class, 'apiUpdate']],
    ['POST', '#^/api/cart/remove$#', [CartController::class, 'apiRemove']],
    ['GET', '#^/checkout$#', [CartController::class, 'checkout']],
    ['POST', '#^/checkout$#', [CartController::class, 'checkout']],
    ['GET', '#^/confirmation/(\d+)$#', [CartController::class, 'confirmation']],
    ['GET', '#^/my-orders$#', [OrderController::class, 'myOrders']],
    ['POST', '#^/api/reviews$#', [ReviewController::class, 'apiCreate']],

    ['GET', '#^/admin$#', [AdminController::class, 'dashboard']],
    ['GET', '#^/admin/admin-requests$#', [AdminController::class, 'adminRequests']],
    ['POST', '#^/admin/admin-requests/(\d+)/approve$#', [AdminController::class, 'approveAdmin']],
    ['POST', '#^/admin/admin-requests/(\d+)/reject$#', [AdminController::class, 'rejectAdmin']],
    ['GET', '#^/admin/categories$#', [AdminController::class, 'categories']],
    ['GET', '#^/admin/categories/create$#', [AdminController::class, 'categoryCreate']],
    ['POST', '#^/admin/categories/create$#', [AdminController::class, 'categoryCreate']],
    ['GET', '#^/admin/categories/(\d+)/edit$#', [AdminController::class, 'categoryEdit']],
    ['POST', '#^/admin/categories/(\d+)/edit$#', [AdminController::class, 'categoryEdit']],
    ['POST', '#^/admin/categories/(\d+)/delete$#', [AdminController::class, 'categoryDelete']],
    ['GET', '#^/admin/products$#', [AdminController::class, 'products']],
    ['GET', '#^/admin/products/create$#', [AdminController::class, 'productCreate']],
    ['POST', '#^/admin/products/create$#', [AdminController::class, 'productCreate']],
    ['GET', '#^/admin/products/(\d+)/edit$#', [AdminController::class, 'productEdit']],
    ['POST', '#^/admin/products/(\d+)/edit$#', [AdminController::class, 'productEdit']],
    ['POST', '#^/admin/products/(\d+)/delete$#', [AdminController::class, 'productDelete']],
    ['PATCH', '#^/api/products/(\d+)/availability$#', [AdminController::class, 'toggleAvailability']],
    ['GET', '#^/admin/orders$#', [AdminController::class, 'orders']],
    ['PUT', '#^/api/orders/(\d+)$#', [AdminController::class, 'updateOrderStatus']],
];

foreach ($routes as $route) {
    list($routeMethod, $pattern, $handler) = $route;
    if ($method === $routeMethod && preg_match($pattern, $path, $matches)) {
        array_shift($matches);
        $class = $handler[0];
        $action = $handler[1];
        $controller = new $class();
        call_user_func_array([$controller, $action], $matches);
        exit;
    }
}

http_response_code(404);
render('errors/404');
