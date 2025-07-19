<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'customer') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$customer_id = $_SESSION['Ref_ID'] ?? 0;
$orders = [];
if ($customer_id) {
    $orders = $pdo->query("SELECT o.*, b.Pack_Date, p.Product_Name FROM `ORDER` o
        LEFT JOIN PACKAGING_BATCH b ON o.Batch_ID = b.Batch_ID
        LEFT JOIN PRODUCT p ON b.Product_ID = p.ProductID
        WHERE o.Customer_ID = $customer_id
        ORDER BY o.OrderID DESC")->fetchAll();
}
?>

<div class="container mt-4">
    <h2>My Orders</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Weight</th>
                <th>Batch</th>
                <th>Product</th>
                <th>Batch Date</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($orders as $o): ?>
            <tr>
                <td><?= $o['OrderID'] ?></td>
                <td><?= $o['Order_date'] ?></td>
                <td><?= $o['Ordered_weight'] ?></td>
                <td><?= $o['Batch_ID'] ?></td>
                <td><?= htmlspecialchars($o['Product_Name']) ?></td>
                <td><?= $o['Pack_Date'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$orders): ?>
            <tr><td colspan="6">No orders found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="place_order.php" class="btn btn-success">Place New Order</a>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<?php include("../templates/footer.php"); ?>
