<?php
include("../includes/db.php");
include("../includes/functions.php");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = hashPassword($_POST['password']);
    $email = $_POST['email'];
    $role = $_POST['role'];
    $ref_id = NULL; // Set this if you link with other tables

    // Example: For demonstration only, production should do proper validation and check for role/ref_id
    $sql = "INSERT INTO users (Username, Password, Email, User_Role, Ref_ID) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$username, $password, $email, $role, $ref_id])) {
        $msg = "Registration successful. <a href='login.php'>Login here</a>.";
    } else {
        $msg = "Registration failed.";
    }
}
?>
<!-- Basic Bootstrap form example -->
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Register</h2>
    <?php if($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>
<form method="post">
    <div class="mb-3">
        <label>Username</label><input name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label><input name="email" type="email" class="form-control">
    </div>
    <div class="mb-3">
        <label>Password</label><input name="password" type="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
            <option value="customer">Customer</option>
            <option value="farmer">Farmer</option>
            <option value="retailer">Retailer</option>
        </select>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Register</button>
        <a href="login.php" class="btn btn-outline-secondary">Login</a>
    </div>
</form>

</div>
</body>
</html>
