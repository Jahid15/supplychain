<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'warehouse_manager') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$manager_id = $_SESSION['Ref_ID'] ?? 0;

// Find manager's warehouse(s)
$stmt = $pdo->prepare("SELECT WarehouseID FROM WAREHOUSE_EMPLOYEE WHERE EmployeeID=?");
$stmt->execute([$manager_id]);
$warehouse_id = $stmt->fetchColumn();

// Employees in this warehouse
$employees = [];
if ($warehouse_id) {
    $employees = $pdo->query("SELECT * FROM WAREHOUSE_EMPLOYEE WHERE WarehouseID = $warehouse_id")->fetchAll();
}

// Inventory in this warehouse
$stock_count = 0;
if ($warehouse_id) {
    $stock_count = $pdo->query("SELECT COUNT(*) FROM PRODUCT_STORAGE WHERE Warehouse_ID = $warehouse_id")->fetchColumn();
}
?>

<div class="container mt-4">
    <h2>Warehouse Manager Dashboard</h2>
    <div class="row mb-4">
        <div class="col">
            <div class="card text-bg-info">
                <div class="card-body"><b>Employees</b><div class="fs-4"><?= count($employees) ?></div></div>
            </div>
        </div>
        <div class="col">
            <div class="card text-bg-success">
                <div class="card-body"><b>Inventory Records</b><div class="fs-4"><?= $stock_count ?></div></div>
            </div>
        </div>
    </div>
    <h4>Employees in Your Warehouse</h4>
    <ul>
    <?php foreach($employees as $e): ?>
        <li><?= htmlspecialchars($e['Employee_Name']) ?> (<?= htmlspecialchars($e['Role']) ?>)</li>
    <?php endforeach; ?>
    <?php if (!$employees): ?>
        <li>No employees assigned.</li>
    <?php endif; ?>
    </ul>
    <a href="employees.php" class="btn btn-primary">Manage Employees</a>
    <a href="inventory.php" class="btn btn-success">View Inventory</a>
</div>
<?php include("../templates/footer.php"); ?>
