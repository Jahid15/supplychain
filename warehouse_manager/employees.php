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

$employees = [];
if ($warehouse_id) {
    $employees = $pdo->query("SELECT * FROM WAREHOUSE_EMPLOYEE WHERE WarehouseID = $warehouse_id")->fetchAll();
}
?>

<div class="container mt-4">
    <h2>Warehouse Employees</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th><th>Role</th><th>Contact</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($employees as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['Employee_Name']) ?></td>
                <td><?= htmlspecialchars($e['Role']) ?></td>
                <td><?= htmlspecialchars($e['Contact']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$employees): ?>
            <tr><td colspan="3">No employees assigned.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<?php include("../templates/footer.php"); ?>
