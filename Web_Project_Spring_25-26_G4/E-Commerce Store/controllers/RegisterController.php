<?php

include "../config/db.php";
session_start();

$ValidName        = "";
$validateemail    = "";
$validatepassword = "";
$validateconfirm  = "";
$ValidPhone       = "";
$validateRole     = "";
$registerInfo     = "";

if (isset($_POST["Register"])) {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirmpassword"]);
    $phone    = trim($_POST["phone"]);
    $role     = $_POST["role"];

    $hasError = false;

    if (empty($name) || strlen($name) < 3) {
        $hasError  = true;
        $ValidName = "Name must contain at least 3 characters!";
    }

    if (empty($email) || !preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
        $hasError      = true;
        $validateemail = "You Must Enter a Valid Email!";
    }

    if (empty($password) || strlen($password) < 8) {
        $hasError         = true;
        $validatepassword = "Password must contain at least 8 characters!";
    }

    if (empty($confirm)) {
        $hasError        = true;
        $validateconfirm = "Confirm Password cannot be empty!";
    } elseif ($confirm != $password) {
        $hasError        = true;
        $validateconfirm = "Password and Confirm Password must match!";
    }

    if (empty($phone)) {
        $hasError   = true;
        $ValidPhone = "Phone is required!";
    } elseif (!is_numeric($phone)) {
        $hasError   = true;
        $ValidPhone = "Phone must be a numeric number!";
    }

    if (empty($role)) {
        $hasError     = true;
        $validateRole = "Please select a role!";
    }

    if ($hasError == false) {

        $db   = new DatabaseConnection();
        $conn = $db->OpenCon();

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $validateemail = "Email Already Registered!";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt2 = $conn->prepare(
                "INSERT INTO users (name, email, phone, password_hash, role) VALUES (?,?,?,?,?)"
            );
            $stmt2->bind_param("sssss", $name, $email, $phone, $password_hash, $role);
            $result2 = $stmt2->execute();

            if ($result2) {
                $db->CloseCon($conn);
                header("Location: ../views/auth/login.php?registered=1");
                exit();
            } else {
                $registerInfo = "<span style='color:red;'>Registration failed. Try again!</span>";
            }
        }

        $db->CloseCon($conn);
    }
}

?>