<?php
class User
{
    public static function create($name, $email, $phone, $password, $role = 'customer', $accountStatus = 'active')
    {
        $role = in_array($role, ['customer', 'admin'], true) ? $role : 'customer';
        $accountStatus = in_array($accountStatus, ['active', 'pending', 'rejected'], true) ? $accountStatus : 'active';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        Database::run(
            "INSERT INTO users (name, email, password_hash, phone, role, account_status, shipping_addresses, created_at)
             VALUES (?, ?, ?, ?, ?, ?, JSON_ARRAY(), NOW())",
            [$name, $email, $hash, $phone, $role, $accountStatus]
        );
        return Database::connection()->lastInsertId();
    }

    public static function findByEmail($email)
    {
        return Database::run("SELECT * FROM users WHERE email = ? LIMIT 1", [$email])->fetch();
    }

    public static function findById($id)
    {
        return Database::run("SELECT * FROM users WHERE id = ? LIMIT 1", [(int)$id])->fetch();
    }

    public static function emailExists($email, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = Database::run("SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1", [$email, (int)$excludeId]);
        } else {
            $stmt = Database::run("SELECT id FROM users WHERE email = ? LIMIT 1", [$email]);
        }
        return (bool)$stmt->fetch();
    }

    public static function updateProfile($id, $name, $email, $phone, $addresses)
    {
        $json = json_encode(array_values($addresses));
        Database::run(
            "UPDATE users SET name = ?, email = ?, phone = ?, shipping_addresses = ? WHERE id = ?",
            [$name, $email, $phone, $json, (int)$id]
        );
        $_SESSION['name'] = $name;
    }

    public static function updatePassword($id, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        Database::run("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, (int)$id]);
    }

    public static function updateRememberToken($id, $hashedToken)
    {
        Database::run("UPDATE users SET remember_token = ? WHERE id = ?", [$hashedToken, (int)$id]);
    }

    public static function clearRememberToken($id)
    {
        Database::run("UPDATE users SET remember_token = NULL WHERE id = ?", [(int)$id]);
    }

    public static function countByRole($role)
    {
        return (int)Database::run("SELECT COUNT(*) FROM users WHERE role = ?", [$role])->fetchColumn();
    }

    public static function countPendingAdmins()
    {
        return (int)Database::run("SELECT COUNT(*) FROM users WHERE role = 'admin' AND account_status = 'pending'")->fetchColumn();
    }

    public static function pendingAdmins()
    {
        return Database::run(
            "SELECT id, name, email, phone, role, account_status, created_at
             FROM users
             WHERE role = 'admin' AND account_status = 'pending'
             ORDER BY created_at DESC"
        )->fetchAll();
    }

    public static function approveAdmin($id)
    {
        $stmt = Database::run(
            "UPDATE users SET account_status = 'active' WHERE id = ? AND role = 'admin' AND account_status = 'pending'",
            [(int)$id]
        );
        return $stmt->rowCount() > 0;
    }

    public static function rejectAdmin($id)
    {
        $stmt = Database::run(
            "UPDATE users SET account_status = 'rejected' WHERE id = ? AND role = 'admin' AND account_status = 'pending'",
            [(int)$id]
        );
        return $stmt->rowCount() > 0;
    }

    public static function isActiveAdmin($user)
    {
        return $user && ($user['role'] ?? '') === 'admin' && (($user['account_status'] ?? 'active') === 'active');
    }

    public static function decodeAddresses($user)
    {
        if (empty($user['shipping_addresses'])) {
            return [];
        }
        $decoded = json_decode($user['shipping_addresses'], true);
        return is_array($decoded) ? $decoded : [];
    }
}
