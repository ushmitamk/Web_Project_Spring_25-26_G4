<?php

include "../config/db.php";
include "../config/helpers.php";
session_start();

require_auth();

$ValidName   = "";
$ValidPhone  = "";
$profileInfo = "";
$passInfo    = "";

$db   = new DatabaseConnection();
$conn = $db->OpenCon();
if (isset($_POST["UpdateProfile"])) {

    $name     = trim($_POST["name"]);
    $phone    = trim($_POST["phone"]);
    $address1 = trim($_POST["address1"]);
    $address2 = trim($_POST["address2"]);

    $hasError = false;

    if (empty($name) || strlen($name) < 3) {
        $hasError  = true;
        $ValidName = "Name must contain at least 3 characters!";
    }

    if (empty($phone) || !is_numeric($phone)) {
        $hasError   = true;
        $ValidPhone = "Phone must be a numeric number!";
    }

    if ($hasError == false) {
        $addresses = json_encode([$address1, $address2]);

        $stmt = $conn->prepare(
            "UPDATE users SET name=?, phone=?, shipping_addresses=? WHERE id=?"
        );
        $stmt->bind_param("sssi", $name, $phone, $addresses, $_SESSION["user_id"]);
        $result = $stmt->execute();

        if ($result) {
            $_SESSION["username"] = $name;
            $_SESSION["Name"]     = $name;
            $profileInfo = "<span style='color:green;'>Profile Updated Successfully!</span>";
        } else {
            $profileInfo = "<span style='color:red;'>Update Failed! Try again.</span>";
        }
    }
}

if (isset($_POST["ChangePassword"])) {

    $current = trim($_POST["current_password"]);
    $new     = trim($_POST["new_password"]);
    $confirm = trim($_POST["confirm_password"]);

    $hasError = false;

  
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!password_verify($current, $user["password_hash"])) {
        $hasError = true;
        $passInfo = "<span style='color:red;'>Current Password is incorrect!</span>";
    } elseif (strlen($new) < 8) {
        $hasError = true;
        $passInfo = "<span style='color:red;'>New Password must be at least 8 characters!</span>";
    } elseif ($new != $confirm) {
        $hasError = true;
        $passInfo = "<span style='color:red;'>New Passwords do not match!</span>";
    }

    if ($hasError == false) {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);

        $stmt2 = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $stmt2->bind_param("si", $new_hash, $_SESSION["user_id"]);
        $result = $stmt2->execute();

        if ($result) {
            $passInfo = "<span style='color:green;'>Password Changed Successfully!</span>";
        } else {
            $passInfo = "<span style='color:red;'>Password change failed!</span>";
        }
    }
}

?>