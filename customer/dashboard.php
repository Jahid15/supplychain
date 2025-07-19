<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'customer') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$user_id = $_SESSION['UserID'];
$customer_id = $_SESSION['Ref_ID'] ?? 0;

// Total Orders Count
$total_orders = 0;
if ($customer_id) {
    $total_orders = $pdo->query("SELECT COUNT(*) FROM `ORDER` WHERE Customer_ID = $customer_id")->fetchColumn();
}

// Last 5 Orders
$orders = [];
if ($customer_id) {
    $orders = $pdo->query("SELECT * FROM `ORDER` WHERE Customer_ID = $customer_id ORDER BY OrderID DESC LIMIT 5")->fetchAll();
}
?>

<div style="max-width: 800px; margin: 50px auto; padding: 30px; background-color: rgba(41, 183, 218, 0.1); border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.08); font-family: Arial, sans-serif;">

    <h2 style="text-align: center; margin-bottom: 15px;">Welcome, <?= htmlspecialchars($_SESSION['Username']) ?>!</h2>
    <p style="text-align: center; font-size: 16px; color: #444;">Your Customer Dashboard</p>

    <div style="margin: 25px 0; padding: 12px 20px; background: #d9edf7; border-left: 5px solid #31708f; color: #31708f; font-size: 15px;">
        Total Orders: <b><?= $total_orders ?></b>
    </div>

    <h4 style="margin-bottom: 15px;">Recent Orders</h4>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <thead>
            <tr style="background-color: #29b7da; color: white;">
                <th style="padding: 10px; border: 1px solid #ccc;">Order ID</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Date</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Weight</th>
                <th style="padding: 10px; border: 1px solid #ccc;">Batch</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;"><?= $o['OrderID'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ccc;"><?= $o['Order_date'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ccc;"><?= $o['Ordered_weight'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ccc;"><?= $o['Batch_ID'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$orders): ?>
                <tr>
                    <td colspan="4" style="padding: 10px; border: 1px solid #ccc; text-align: center;">No orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
        <a href="place_order.php" style="padding: 10px 20px; background-color: #5cb85c; color: white; text-decoration: none; border-radius: 5px;">Place New Order</a>
        <a href="orders.php" style="padding: 10px 20px; background-color: #29b7da; color: white; text-decoration: none; border-radius: 5px;">View All Orders</a>
        <a href="../common/logout.php" style="padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px;">Logout</a>
    </div>

</div>

<?php include("../templates/footer.php"); ?>