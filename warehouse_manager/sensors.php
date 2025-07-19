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

$sensors = [];
if ($warehouse_id) {
    $sensors = $pdo->query("SELECT * FROM SENSOR WHERE Warehouse_ID = $warehouse_id")->fetchAll();
}
?>

<div class="container mt-4">
    <h2>Warehouse Sensors</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Status</th>
                <th>Last Reading</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($sensors as $s): ?>
            <tr>
                <td><?= $s['Sensor_ID'] ?></td>
                <td><?= htmlspecialchars($s['Sensor_type']) ?></td>
                <td><?= htmlspecialchars($s['Status']) ?></td>
                <td><?= $s['Last_reading'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$sensors): ?>
            <tr><td colspan="4">No sensors found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<?php include("../templates/footer.php"); ?>
