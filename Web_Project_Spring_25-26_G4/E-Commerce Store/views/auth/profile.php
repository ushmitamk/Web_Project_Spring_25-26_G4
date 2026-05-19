<?php
include "../../controllers/ProfileController.php";

if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$addresses = json_decode($user["shipping_addresses"] ?? "[]", true);
$addr1 = $addresses[0] ?? "";
$addr2 = $addresses[1] ?? "";
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: Arial; }
        .profile-box {
            width: 540px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="profile-box">

    <div class="d-flex justify-content-between mb-3">
        <a href="../../index.php">Home</a>
        <a href="../../controllers/LogoutController.php">Logout</a>
    </div>

    <h3 class="text-center">My Profile</h3>
    <p class="text-center text-muted">Role: <b><?php echo ucfirst($user["role"]); ?></b></p>
    <hr>

    <fieldset class="mb-4">
        <legend>Profile Info</legend>
        <table>
            <tr>
                <td><p style="background-color:powderblue;">Name :</p></td>
                <td><?php echo htmlspecialchars($user["name"]); ?></td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Email :</p></td>
                <td><?php echo htmlspecialchars($user["email"]); ?></td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Phone :</p></td>
                <td><?php echo htmlspecialchars($user["phone"] ?? "-"); ?></td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Address 1 :</p></td>
                <td><?php echo htmlspecialchars($addr1 ?: "-"); ?></td>
            </tr>
            <tr>
                <td><p style="background-color:powderblue;">Address 2 :</p></td>
                <td><?php echo htmlspecialchars($addr2 ?: "-"); ?></td>
            </tr>
        </table>
    </fieldset>

    <fieldset class="mb-4">
        <legend>Update Profile</legend>
        <?php echo $profileInfo; ?>
        <form action="" method="post">
            <table>
                <tr>
                    <td><p style="background-color:powderblue;">Name :</p></td>
                    <td>
                        <input type="text" name="name" class="form-control"
                               value="<?php echo htmlspecialchars($user["name"]); ?>">
                        <p style="color:red;"><?php echo $ValidName; ?></p>
                    </td>
                </tr>
                <tr>
                    <td><p style="background-color:powderblue;">Phone :</p></td>
                    <td>
                        <input type="text" name="phone" class="form-control"
                               value="<?php echo htmlspecialchars($user["phone"] ?? ""); ?>">
                        <p style="color:red;"><?php echo $ValidPhone; ?></p>
                    </td>
                </tr>
                <tr>
                    <td><p style="background-color:powderblue;">Address 1 :</p></td>
                    <td>
                        <input type="text" name="address1" class="form-control"
                               value="<?php echo htmlspecialchars($addr1); ?>">
                    </td>
                </tr>
                <tr>
                    <td><p style="background-color:powderblue;">Address 2 :</p></td>
                    <td>
                        <input type="text" name="address2" class="form-control"
                               value="<?php echo htmlspecialchars($addr2); ?>">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="UpdateProfile" value="Update Profile" class="btn btn-primary">
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>

    <fieldset>
        <legend>Change Password</legend>
        <?php echo $passInfo; ?>
        <form action="" method="post">
            <table>
                <tr>
                    <td><p style="background-color:powderblue;">Current Password :</p></td>
                    <td><input type="password" name="current_password" class="form-control"></td>
                </tr>
                <tr>
                    <td><p style="background-color:powderblue;">New Password :</p></td>
                    <td><input type="password" name="new_password" class="form-control"></td>
                </tr>
                <tr>
                    <td><p style="background-color:powderblue;">Confirm New Password :</p></td>
                    <td><input type="password" name="confirm_password" class="form-control"></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="ChangePassword" value="Change Password" class="btn btn-warning">
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>

</div>
</body>
</html>
<?php $db->CloseCon($conn); ?>