<!DOCTYPE html>
<html>
<head>
    <title>E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: Arial; }
        .main-box {
            width: 500px;
            margin: 80px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .task-btn {
            display: block;
            width: 100%;
            padding: 14px;
            margin-bottom: 15px;
            background-color: powderblue;
            border: 1px solid #aaa;
            border-radius: 6px;
            font-size: 16px;
            text-decoration: none;
            color: black;
        }
        .task-btn:hover { background-color: #87CEEB; color: black; }
    </style>
</head>
<body>

<div class="main-box">

    <h2>E-Commerce Store</h2>
    <p class="text-muted">Web Technologies - Group Project</p>
    <hr>

    <a class="task-btn" href="views/auth/login.php">
        Task 1 - User Login and Register
    </a>

    <a class="task-btn" href="#">
        Task 2 - Product and Category Management
    </a>

    <a class="task-btn" href="#">
        Task 3 - Shopping Cart and Checkout
    </a>

    <a class="task-btn" href="#">
        Task 4 - Orders and Reviews
    </a>

    <hr>
    <small class="text-muted">Admin: admin@shop.com / admin1234</small>

</div>

</body>
</html>