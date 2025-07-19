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

$shipments = [];
if ($warehouse_id) {
    $shipments = $pdo->query("
        SELECT s.*, d.Driver_Name, v.Vehicle_Type, pb.Batch_ID
        FROM SHIPMENT s
        LEFT JOIN DRIVER d ON s.Driver_ID = d.DriverID
        LEFT JOIN VEHICLE v ON s.Vehicle_ID = v.Vehicle_ID
        LEFT JOIN PACKAGING_BATCH pb ON s.Batch_ID = pb.Batch_ID
        WHERE pb.Batch_ID IN (
            SELECT Batch_ID FROM PRODUCT_STORAGE WHERE Warehouse_ID = $warehouse_id
        )
        ORDER BY s.ShipmentID DESC
    ")->fetchAll();
}
?>

<div class="container mt-4">
    <h2>Related Shipments</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Date</th><th>From</th><th>To</th><th>Status</th><th>Driver</th><th>Vehicle</th><th>Batch</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($shipments as $s): ?>
            <tr>
                <td><?= $s['ShipmentID'] ?></td>
                <td><?= $s['Shipment_Date'] ?></td>
                <td><?= htmlspecialchars($s['Start_location']) ?></td>
                <td><?= htmlspecialchars($s['End_location']) ?></td>
                <td><?= htmlspecialchars($s['Delivery_status']) ?></td>
                <td><?= htmlspecialchars($s['Driver_Name']) ?></td>
                <td><?= htmlspecialchars($s['Vehicle_Type']) ?></td>
                <td><?= $s['Batch_ID'] ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$shipments): ?>
            <tr><td colspan="8">No shipments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<?php include("../templates/footer.php"); ?>
