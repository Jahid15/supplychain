<?php
include("../includes/db.php");
include("../includes/functions.php");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = hashPassword($_POST['password']);
    $email = $_POST['email'];
    $role = $_POST['role'];
    $ref_id = NULL;

    $sql = "INSERT INTO users (Username, Password, Email, User_Role, Ref_ID) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$username, $password, $email, $role, $ref_id])) {
        $msg = "Registration successful. <a href='login.php'>Login here</a>.";
    } else {
        $msg = "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <div style="max-width: 550px; margin: 60px auto; padding: 30px; background-color: rgba(41, 183, 218, 0.1); border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.08);">

        <h2 style="text-align: center; margin-bottom: 25px;">Register</h2>

        <?php if ($msg): ?>
            <div style="margin-bottom: 15px; background: #d9edf7; padding: 10px 15px; border-left: 5px solid #31708f; color: #31708f;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="post" onsubmit="return validateRegister()">

            <div style="margin-bottom: 15px;">
                <label for="username" style="display: block; margin-bottom: 6px;">Username <span style="color:red">*</span></label>
                <input type="text" name="username" id="username" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email" style="display: block; margin-bottom: 6px;">Email <span style="color:red">*</span></label>
                <input type="email" name="email" id="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; margin-bottom: 6px;">Password <span style="color:red">*</span></label>
                <input type="password" name="password" id="password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="role" style="display: block; margin-bottom: 6px;">Select Role <span style="color:red">*</span></label>
                <select name="role" id="role" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="customer">Customer</option>
                    <option value="farmer">Farmer</option>
                    <option value="retailer">Retailer</option>
                </select>
            </div>

            <div style="text-align: center; display: flex; justify-content: center; gap: 10px;">
                <button type="submit" class="btn" style="background-color: #29b7da; color: white; padding: 10px 25px; border: none; border-radius: 5px;">Register</button>
                <a href="login.php" class="btn" style="padding: 10px 25px; background-color: #5cb85c; color: white; text-decoration: none; border-radius: 5px;">Login</a>
            </div>

        </form>
    </div>

    <script>
        function validateRegister() {
            let u = document.getElementById('username').value.trim();
            let e = document.getElementById('email').value.trim();
            let p = document.getElementById('password').value.trim();
            if (!u || !e || !p) {
                alert('Please fill all required fields.');
                return false;
            }
            return true;
        }
    </script>

</body>

</html>