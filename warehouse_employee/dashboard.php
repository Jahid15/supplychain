<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'employee') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$employee_id = $_SESSION['Ref_ID'] ?? 0;
// Get batches stored by this employee
$batches = $pdo->query("
    SELECT ps.*, pb.Pack_Date, pb.Total_Weight, p.Product_Name, w.Location AS Warehouse
    FROM PRODUCT_STORAGE ps
    LEFT JOIN PACKAGING_BATCH pb ON ps.Batch_ID = pb.Batch_ID
    LEFT JOIN PRODUCT p ON pb.Product_ID = p.ProductID
    LEFT JOIN WAREHOUSE w ON ps.Warehouse_ID = w.WarehouseID
    WHERE ps.Employee_ID = $employee_id
    ORDER BY ps.Storage_ID DESC LIMIT 5
")->fetchAll();
?>

<div class="container mt-4">
    <h2>Warehouse Employee Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['Username']) ?>!</p>

    <h4>Recently Handled Batches</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Batch</th>
                <th>Product</th>
                <th>Date</th>
                <th>Stored Weight</th>
                <th>Warehouse</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($batches as $b): ?>
            <tr>
                <td><?= $b['Batch_ID'] ?></td>
                <td><?= htmlspecialchars($b['Product_Name']) ?></td>
                <td><?= $b['Pack_Date'] ?></td>
                <td><?= $b['Stored_weight'] ?></td>
                <td><?= htmlspecialchars($b['Warehouse']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$batches): ?>
            <tr><td colspan="5">No batches handled yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="batches.php" class="btn btn-primary">View All Batches</a>
    <a href="stock.php" class="btn btn-success">View Warehouse Stock</a>
    <a href="receive_from_farmer.php" class="btn btn-info">Receive From Farmer</a>
</div>
<?php include("../templates/footer.php"); ?>
