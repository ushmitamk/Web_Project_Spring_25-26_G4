<?php

include "../config/db.php";
session_start();

$validateemail    = "";
$validatepassword = "";
$validateRole     = "";
$loginInfo        = "";

if (!empty($_COOKIE['remember_token']) && empty($_SESSION['user_id'])) {

    $db   = new DatabaseConnection();
    $conn = $db->OpenCon();

    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $_COOKIE['remember_token']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $_SESSION['user_id']  = $row['id'];
        $_SESSION['username'] = $row['name'];
        $_SESSION['Email']    = $row['email'];
        $_SESSION['role']     = $row['role'];
        $_SESSION['Name']     = $row['name'];

        $db->CloseCon($conn);

        if ($row['role'] == 'admin') {
            header("Location: ../views/admin/dashboard.php");
        } else {
            header("Location: ../views/shop/products.php");
        }
        exit();
    }

    $db->CloseCon($conn);
}

if (isset($_POST["Login"])) {

    $email    = $_POST["email"];
    $password = $_POST["password"];
    $role     = $_POST["role"];
    $remember = isset($_POST["remember_me"]);

    $hasError = false;

    if (empty($email) || !preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
        $hasError      = true;
        $validateemail = "You Must Enter Valid Email";
    }

    if (empty($password) || strlen($password) < 6) {
        $hasError         = true;
        $validatepassword = "Password Must Contain at least 6 characters!";
    }

    if (empty($role)) {
        $hasError     = true;
        $validateRole = "Please select a role!";
    }

    if ($hasError == false) {

        $db     = new DatabaseConnection();
        $conn   = $db->OpenCon();

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row["password_hash"]) && $row["role"] == $role) {

                $_SESSION["user_id"]  = $row["id"];
                $_SESSION["username"] = $row["name"];
                $_SESSION["Email"]    = $row["email"];
                $_SESSION["role"]     = $row["role"];
                $_SESSION["Name"]     = $row["name"];

   
                if ($remember) {
                    $token = bin2hex(random_bytes(32));

                    $stmt2 = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt2->bind_param("si", $token, $row["id"]);
                    $stmt2->execute();

                    setcookie("remember_token", $token, time() + (30 * 24 * 60 * 60), "/");
                }

                $db->CloseCon($conn);

                if ($row["role"] == "admin") {
                    header("Location: ../views/admin/dashboard.php");
                } else {
                    header("Location: ../views/shop/products.php");
                }
                exit();

            } else {
                $loginInfo = "<span style='color:red;'>Invalid email, password or role!</span>";
            }

        } else {
            $loginInfo = "<span style='color:red;'>No account found with this email!</span>";
        }

        $db->CloseCon($conn);
    }
}

?>