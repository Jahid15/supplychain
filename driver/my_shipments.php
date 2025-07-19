<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'driver') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$driver_id = $_SESSION['Ref_ID'] ?? 0;
$shipments = [];
if ($driver_id) {
    $shipments = $pdo->query("
        SELECT s.*, b.Batch_ID, v.Vehicle_Type
        FROM SHIPMENT s
        LEFT JOIN PACKAGING_BATCH b ON s.Batch_ID = b.Batch_ID
        LEFT JOIN VEHICLE v ON s.Vehicle_ID = v.Vehicle_ID
        WHERE s.Driver_ID = $driver_id
        ORDER BY s.ShipmentID DESC
    ")->fetchAll();
}
?>

<div class="container mt-4">
    <h2>My Shipments</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Distance</th>
                <th>Vehicle</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($shipments as $s): ?>
            <tr>
                <td><?= $s['ShipmentID'] ?></td>
                <td><?= $s['Shipment_Date'] ?></td>
                <td><?= htmlspecialchars($s['Start_location']) ?></td>
                <td><?= htmlspecialchars($s['End_location']) ?></td>
                <td><?= htmlspecialchars($s['Distance']) ?></td>
                <td><?= htmlspecialchars($s['Vehicle_Type']) ?></td>
                <td><?= htmlspecialchars($s['Delivery_status']) ?></td>
                <td>
                    <a href="update_status.php?id=<?= $s['ShipmentID'] ?>" class="btn btn-sm btn-warning">Update</a>
                </td>
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
