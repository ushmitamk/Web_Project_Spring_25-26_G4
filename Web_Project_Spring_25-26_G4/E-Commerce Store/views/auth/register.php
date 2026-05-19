<?php include "../../controllers/RegisterController.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../controllers/JS/CheckEmail.js"></script>
    <style>
        body { background: #f4f6f9; font-family: Arial; }
        .register-box {
            width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="register-box">

    <div class="d-flex justify-content-between mb-3">
        <a href="login.php">Login</a>
        <a href="../../index.php">Home</a>
    </div>

    <h3 class="mb-4 text-center">Register</h3>

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
                <td><p style="background-color:powderblue;">Full Name :</p></td>
                <td>
                    <input type="text" name="name" placeholder="Enter your Name" class="form-control">
                    <p style="color:red;"><?php echo $ValidName; ?></p>
                </td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Email :</p></td>
                <td>
                    <input type="text" name="email" id="email"
                           placeholder="Enter Email"
                           onkeyup="CheckEmail()"
                           class="form-control">
                    <p id="erroremail" style="color:red;"><?php echo $validateemail; ?></p>
                </td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Phone :</p></td>
                <td>
                    <input type="text" name="phone" placeholder="Enter Phone Number" class="form-control">
                    <p style="color:red;"><?php echo $ValidPhone; ?></p>
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
                <td><p style="background-color:powderblue;">Confirm Password :</p></td>
                <td>
                    <input type="password" name="confirmpassword" placeholder="Confirm Password" class="form-control">
                    <p style="color:red;"><?php echo $validateconfirm; ?></p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="Register" value="Register" class="btn btn-success">
                    <input type="reset" value="Reset" class="btn btn-secondary">
                    <br><br>
                    <?php echo $registerInfo; ?>
                </td>
            </tr>
        </table>
    </form>

    <p class="mt-3 text-center">
        Already have an account? <a href="login.php">Login here</a>
    </p>

</div>
</body>
</html>