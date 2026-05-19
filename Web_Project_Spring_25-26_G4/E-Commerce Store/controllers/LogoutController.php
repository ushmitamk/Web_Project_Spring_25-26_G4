<?php

session_start();


if (!empty($_SESSION['user_id']) && !empty($_COOKIE['remember_token'])) {

    include "../config/db.php";

    $db   = new DatabaseConnection();
    $conn = $db->OpenCon();

    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    $db->CloseCon($conn);


    setcookie("remember_token", "", time() - 3600, "/");
}

session_destroy();
header("Location: ../views/auth/login.php");
exit();

?>