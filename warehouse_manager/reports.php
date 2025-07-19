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

$stock_count = $pdo->query("SELECT COUNT(*) FROM PRODUCT_STORAGE WHERE Warehouse_ID = $warehouse_id")->fetchColumn();
$employee_count = $pdo->query("SELECT COUNT(*) FROM WAREHOUSE_EMPLOYEE WHERE WarehouseID = $warehouse_id")->fetchColumn();
$sensor_count = $pdo->query("SELECT COUNT(*) FROM SENSOR WHERE Warehouse_ID = $warehouse_id")->fetchColumn();
?>

<div class="container mt-4">
    <h2>Warehouse Report</h2>
    <ul class="list-group mb-4">
        <li class="list-group-item">Total Stock Records: <b><?= $stock_count ?></b></li>
        <li class="list-group-item">Total Employees: <b><?= $employee_count ?></b></li>
        <li class="list-group-item">Total Sensors: <b><?= $sensor_count ?></b></li>
    </ul>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<?php include("../templates/footer.php"); ?>
