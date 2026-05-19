<?php

include "../config/db.php";

$email = trim($_POST["email"] ?? "");

if (empty($email)) {
    echo "Email is required!";
} else {
    $db   = new DatabaseConnection();
    $conn = $db->OpenCon();

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email Already Registered!";
    } else {
        echo "Email Available";
    }

    $db->CloseCon($conn);
}

?>