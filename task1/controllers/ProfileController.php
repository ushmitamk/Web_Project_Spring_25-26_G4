<?php
class ProfileController
{
    public function edit()
    {
        require_login();
        $user = User::findById(current_user_id());
        $addresses = User::decodeAddresses($user);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $addresses = [];
            foreach (['address_1', 'address_2'] as $field) {
                $addr = trim($_POST[$field] ?? '');
                if ($addr !== '') $addresses[] = $addr;
            }
            if ($name === '') $errors['name'] = 'Name is required.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required.';
            elseif (User::emailExists($email, current_user_id())) $errors['email'] = 'Email is already used by another account.';

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            if ($currentPassword !== '' || $newPassword !== '') {
                if (!password_verify($currentPassword, $user['password_hash'])) {
                    $errors['current_password'] = 'Current password is incorrect.';
                }
                if (strlen($newPassword) < 8) {
                    $errors['new_password'] = 'New password must be at least 8 characters.';
                }
            }
            if (!$errors) {
                User::updateProfile(current_user_id(), $name, $email, $phone, array_slice($addresses, 0, 2));
                if ($newPassword !== '') {
                    User::updatePassword(current_user_id(), $newPassword);
                }
                flash('success', 'Profile updated successfully.');
                redirect('/profile');
            }
            $user['name'] = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
        }
        render('profile/edit', ['user' => $user, 'addresses' => $addresses, 'errors' => $errors]);
    }
}
