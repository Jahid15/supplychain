<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'warehouse_manager') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$manager_id = $_SESSION['Ref_ID'] ?? 0;
$stmt = $pdo->prepare("SELECT WarehouseID FROM WAREHOUSE_EMPLOYEE WHERE EmployeeID=?");
$stmt->execute([$manager_id]);
$warehouse_id = $stmt->fetchColumn();

$stock = [];
if ($warehouse_id) {
    $stock = $pdo->query("
        SELECT ps.*, pb.Pack_Date, p.Product_Name
        FROM PRODUCT_STORAGE ps
        LEFT JOIN PACKAGING_BATCH pb ON ps.Batch_ID = pb.Batch_ID
        LEFT JOIN PRODUCT p ON pb.Product_ID = p.ProductID
        WHERE ps.Warehouse_ID = $warehouse_id
        ORDER BY ps.Storage_ID DESC
    ")->fetchAll();
}
?>

<div class="container mt-4">
    <h2>Warehouse Inventory</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Batch</th>
                <th>Product</th>
                <th>Pack Date</th>
                <th>Stored Weight</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($stock as $s): ?>
            <tr>
                <td><?= $s['Batch_ID'] ?></td>
                <td><?= htmlspecialchars($s['Product_Name']) ?></td>
                <td><?= $s['Pack_Date'] ?></td>
                <td><?= $s['Stored_weight'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$stock): ?>
            <tr><td colspan="4">No inventory records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<?php include("../templates/footer.php"); ?>
