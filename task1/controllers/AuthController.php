<?php
class AuthController
{
    public function register()
    {
        $errors = [];
        $old = ['name' => '', 'email' => '', 'phone' => '', 'role' => 'customer'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['name'] = trim($_POST['name'] ?? '');
            $old['email'] = trim($_POST['email'] ?? '');
            $old['phone'] = trim($_POST['phone'] ?? '');
            $old['role'] = $_POST['role'] ?? 'customer';
            $password = $_POST['password'] ?? '';

            if ($old['name'] === '') $errors['name'] = 'Name is required.';
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required.';
            elseif (User::emailExists($old['email'])) $errors['email'] = 'Email already exists.';
            if (!in_array($old['role'], ['customer', 'admin'], true)) $errors['role'] = 'Please choose Customer or Admin.';
            if ($password === '' || strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters.';

            if (!$errors) {
                $accountStatus = $old['role'] === 'admin' ? 'pending' : 'active';
                User::create($old['name'], $old['email'], $old['phone'], $password, $old['role'], $accountStatus);
                if ($old['role'] === 'admin') {
                    flash('success', 'Admin registration request submitted. Please wait for the default Admin to approve your account.');
                } else {
                    flash('success', 'Registration successful. Please login.');
                }
                redirect('/login');
            }
        }
        render('auth/register', ['errors' => $errors, 'old' => $old]);
    }

    public function login()
    {
        $errors = [];
        $email = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = User::findByEmail($email);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors['login'] = 'Invalid email or password.';
            } elseif (($user['role'] ?? '') === 'admin' && (($user['account_status'] ?? 'active') !== 'active')) {
                $status = $user['account_status'] ?? 'pending';
                $errors['login'] = $status === 'rejected'
                    ? 'This admin request was rejected. Please contact the default Admin.'
                    : 'Your admin account is pending approval from the default Admin.';
            } else {
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                if (!empty($_POST['remember_me'])) {
                    $token = bin2hex(random_bytes(32));
                    User::updateRememberToken($user['id'], password_hash($token, PASSWORD_DEFAULT));
                    setcookie('remember_me', $user['id'] . ':' . $token, time() + 60 * 60 * 24 * 30, '/', '', false, true);
                }
                redirect($user['role'] === 'admin' ? '/admin' : '/products');
            }
        }
        render('auth/login', ['errors' => $errors, 'email' => $email]);
    }

    public function logout()
    {
        if (is_logged_in()) {
            User::clearRememberToken(current_user_id());
        }
        setcookie('remember_me', '', time() - 3600, '/', '', false, true);
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        redirect('/login');
    }
}
