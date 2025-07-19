<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'customer') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

// Get customer info from users table, and matching CUSTOMER row (if needed)
$user_id = $_SESSION['UserID'];
// Find CustomerID from users.Ref_ID (you may have linked them during registration)
$customer_id = $_SESSION['Ref_ID'] ?? 0;

// Count orders
$total_orders = 0;
if ($customer_id) {
    $total_orders = $pdo->query("SELECT COUNT(*) FROM `ORDER` WHERE Customer_ID = $customer_id")->fetchColumn();
}

// Get last 5 orders
$orders = [];
if ($customer_id) {
    $orders = $pdo->query("SELECT * FROM `ORDER` WHERE Customer_ID = $customer_id ORDER BY OrderID DESC LIMIT 5")->fetchAll();
}
?>

<div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['Username']) ?>!</h2>
    <p>Your Customer Dashboard</p>

    <div class="mb-4">
        <div class="alert alert-info">Total Orders: <b><?= $total_orders ?></b></div>
    </div>

    <h4>Recent Orders</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Weight</th>
                <th>Batch</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($orders as $o): ?>
            <tr>
                <td><?= $o['OrderID'] ?></td>
                <td><?= $o['Order_date'] ?></td>
                <td><?= $o['Ordered_weight'] ?></td>
                <td><?= $o['Batch_ID'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$orders): ?>
            <tr><td colspan="4">No orders found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="place_order.php" class="btn btn-success">Place New Order</a>
    <a href="orders.php" class="btn btn-primary">View All Orders</a>
</div>
<?php include("../templates/footer.php"); ?>
