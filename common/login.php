<?php
session_start();
include("../includes/db.php");
include("../includes/functions.php");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE Username = ? OR Email = ?");
    $stmt->execute([$userInput, $userInput]);
    $user = $stmt->fetch();

    if ($user && verifyPassword($password, $user['Password'])) {
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['User_Role'] = $user['User_Role'];
        $_SESSION['Ref_ID'] = $user['Ref_ID'];

        $redirects = [
            'admin' => '../admin/dashboard.php',
            'warehouse_manager' => '../warehouse_manager/dashboard.php',
            'employee' => '../warehouse_employee/dashboard.php',
            'farmer' => '../farmer/dashboard.php',
            'customer' => '../customer/dashboard.php',
            'retailer' => '../customer/dashboard.php',
            'transport_manager' => '../transport_manager/dashboard.php',
            'driver' => '../driver/dashboard.php',
        ];

        header("Location: " . ($redirects[$user['User_Role']] ?? '../index.php'));
        exit;
    } else {
        $msg = "Invalid email/username or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <div style="max-width: 500px; margin: 80px auto; padding: 30px; background-color: rgba(41, 183, 218, 0.1); border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1);">

        <h2 style="text-align: center; margin-bottom: 20px;">Login</h2>

        <?php if (!empty($msg)): ?>
            <div style="color: red; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form method="post" onsubmit="return validateLogin()">

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 6px;">Email or Username <span style="color:red">*</span></label>
                <input type="text" name="email" id="email" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 3px;">
                <span id="erremail" style="color: red; font-size: 13px;"></span>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 6px;">Password <span style="color:red">*</span></label>
                <input type="password" name="password" id="password" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 3px;">
                <span id="errpassword" style="color: red; font-size: 13px;"></span>
            </div>

            <div style="text-align: center; margin-top: 25px;">
                <button type="submit" name="btncustlogin" style="padding: 10px 25px; margin-right: 10px; background-color: #29b7da; color: white; border: none; border-radius: 5px; font-size: 15px;">Login</button>

                <a href="register.php" style="padding: 10px 25px; background-color: #5cb85c; color: white; border-radius: 5px; text-decoration: none; font-size: 15px;">Register</a>
            </div>

        </form>
    </div>

    <script>
        function validateLogin() {
            let email = document.getElementById('email').value.trim();
            let password = document.getElementById('password').value.trim();
            let erremail = document.getElementById('erremail');
            let errpassword = document.getElementById('errpassword');

            erremail.textContent = '';
            errpassword.textContent = '';

            let valid = true;

            if (!email) {
                erremail.textContent = 'Required';
                valid = false;
            }

            if (!password) {
                errpassword.textContent = 'Required';
                valid = false;
            }

            return valid;
        }
    </script>

</body>

</html>