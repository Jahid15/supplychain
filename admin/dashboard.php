<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'admin') {
    header("Location: ../common/login.php");
    exit;
}
include("../templates/header.php");
?>
<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    <div class="row g-3">
        <div class="col-md-3">
            <a href="users.php" class="btn btn-outline-primary w-100">Manage Users</a>
        </div>
        <div class="col-md-3">
            <a href="manage_products.php" class="btn btn-outline-primary w-100">Manage Products</a>
        </div>
        <div class="col-md-3">
            <a href="manage_warehouses.php" class="btn btn-outline-primary w-100">Manage Warehouses</a>
        </div>
        <div class="col-md-3">
            <a href="manage_orders.php" class="btn btn-outline-primary w-100">Manage Orders</a>
        </div>
        <div class="col-md-3">
            <a href="manage_shipments.php" class="btn btn-outline-primary w-100">Manage Shipments</a>
        </div>
        <div class="col-md-3">
            <a href="reports.php" class="btn btn-outline-primary w-100">View Reports</a>
        </div>
        <div class="col-md-3">
            <a href="../common/logout.php" class="btn btn-outline-danger w-100">Logout</a>
        </div>
    </div>
</div>
<?php include("../templates/footer.php"); ?>
