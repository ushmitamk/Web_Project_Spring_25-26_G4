<?php include "../../controllers/LoginController.php"; ?>

<?php
if (!empty($_SESSION["user_id"])) {
    if ($_SESSION["role"] == "admin") {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../shop/products.php");
    }
    exit();
}
$registered = $_GET["registered"] ?? "";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: Arial; }
        .login-box {
            width: 430px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="login-box">

    <div class="d-flex justify-content-between mb-3">
        <a href="register.php">Register</a>
        <a href="../../index.php">Home</a>
    </div>

    <h3 class="mb-4 text-center">Login</h3>

    <?php if ($registered): ?>
        <div class="alert alert-success">Registration Successful! Please Login.</div>
    <?php endif; ?>

    <form action="" method="post">
        <table>
            <tr>
                <td><p style="background-color:powderblue;">Role :</p></td>
                <td>
                    <select name="role" class="form-select">
                        <option value="">-- Select Role --</option>
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                    <p style="color:red;"><?php echo $validateRole; ?></p>
                </td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Email :</p></td>
                <td>
                    <input type="text" name="email" placeholder="Enter Email" class="form-control">
                    <p style="color:red;"><?php echo $validateemail; ?></p>
                </td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Password :</p></td>
                <td>
                    <input type="password" name="password" placeholder="Enter Password" class="form-control">
                    <p style="color:red;"><?php echo $validatepassword; ?></p>
                </td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Remember Me :</p></td>
                <td>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me">
                        <label class="form-check-label" for="remember_me">
                            Keep me logged in for 30 days
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <br>
                    <input type="submit" name="Login" value="Login" class="btn btn-primary">
                    <input type="reset" value="Reset" class="btn btn-secondary">
                    <br><br>
                    <?php echo $loginInfo; ?>
                </td>
            </tr>
        </table>
    </form>

    <p class="mt-3 text-center">
        Don't have an account? <a href="register.php">Register here</a>
    </p>

</div>
</body>
</html>