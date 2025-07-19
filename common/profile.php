<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

// You can fetch extra info if you want (optional)
$stmt = $pdo->prepare("SELECT * FROM users WHERE UserID=?");
$stmt->execute([$_SESSION['UserID']]);
$user = $stmt->fetch();
?>
<div class="container mt-4">
    <h2>Profile</h2>
    <p><b>Username:</b> <?= htmlspecialchars($user['Username']) ?></p>
    <p><b>Email:</b> <?= htmlspecialchars($user['Email']) ?></p>
    <p><b>Role:</b> <?= htmlspecialchars($user['User_Role']) ?></p>
</div>
<?php include("../templates/footer.php"); ?>
