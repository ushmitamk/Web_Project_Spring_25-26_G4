<?php
session_start();

if (empty($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: Arial; }
        .dash-box {
            width: 550px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .dash-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            background-color: powderblue;
            border: 1px solid #aaa;
            border-radius: 6px;
            font-size: 15px;
            text-decoration: none;
            color: black;
        }
        .dash-btn:hover { background-color: #87CEEB; color: black; }
    </style>
</head>
<body>

<div class="dash-box">

    <div class="d-flex justify-content-between mb-3">
        <a href="../../index.php">Home</a>
        <a href="../../controllers/LogoutController.php">Logout</a>
    </div>

    <h3>Admin Dashboard</h3>
    <p>Welcome, <b><?php echo htmlspecialchars($_SESSION["Name"]); ?></b>
       &nbsp;|&nbsp; Role: <b>Admin</b></p>
    <hr>

    <a class="dash-btn" href="../auth/profile.php">My Profile (Task 1)</a>
    <a class="dash-btn" href="#">Product and Category Management (Task 2)</a>
    <a class="dash-btn" href="#">Shopping Cart and Checkout (Task 3)</a>
    <a class="dash-btn" href="#">Order Management and Reviews (Task 4)</a>

</div>

</body>
</html>