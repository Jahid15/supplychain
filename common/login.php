<?php
session_start();
include("../includes/db.php");
include("../includes/functions.php");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if($user && verifyPassword($password, $user['Password'])) {
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['User_Role'] = $user['User_Role'];
        $_SESSION['Ref_ID'] = $user['Ref_ID'];
        // Redirect based on role
        switch($user['User_Role']) {
            case 'admin': header("Location: ../admin/dashboard.php"); break;
            case 'warehouse_manager': header("Location: ../warehouse_manager/dashboard.php"); break;
            case 'employee': header("Location: ../warehouse_employee/dashboard.php"); break;
            case 'farmer': header("Location: ../farmer/dashboard.php"); break;
            case 'customer': header("Location: ../customer/dashboard.php"); break;
            case 'retailer': header("Location: ../customer/dashboard.php"); break;
            case 'transport_manager': header("Location: ../transport_manager/dashboard.php"); break;
            case 'driver': header("Location: ../driver/dashboard.php"); break;
            default: header("Location: ../index.php");
        }
        exit;
    } else {
        $msg = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Login</h2>
    <?php if($msg) echo "<div class='alert alert-danger'>$msg</div>"; ?>
<form method="post">
    <div class="mb-3">
        <label>Username</label>
        <input name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input name="password" type="password" class="form-control" required>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Login</button>
        <a href="register.php" class="btn btn-outline-secondary">Register</a>
    </div>
</form>

</div>
</body>
</html>
