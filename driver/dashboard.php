<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'driver') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$driver_id = $_SESSION['Ref_ID'] ?? 0;

// Recent shipments for this driver
$shipments = [];
if ($driver_id) {
    $shipments = $pdo->query("
        SELECT s.*, b.Batch_ID, v.Vehicle_Type 
        FROM SHIPMENT s
        LEFT JOIN PACKAGING_BATCH b ON s.Batch_ID = b.Batch_ID
        LEFT JOIN VEHICLE v ON s.Vehicle_ID = v.Vehicle_ID
        WHERE s.Driver_ID = $driver_id
        ORDER BY s.ShipmentID DESC LIMIT 5
    ")->fetchAll();
}
?>

<div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['Username']) ?>!</h2>
    <p>Your Driver Dashboard</p>

    <h4>Recent Shipments</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Vehicle</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($shipments as $s): ?>
            <tr>
                <td><?= $s['ShipmentID'] ?></td>
                <td><?= $s['Shipment_Date'] ?></td>
                <td><?= htmlspecialchars($s['Start_location']) ?></td>
                <td><?= htmlspecialchars($s['End_location']) ?></td>
                <td><?= htmlspecialchars($s['Vehicle_Type']) ?></td>
                <td><?= htmlspecialchars($s['Delivery_status']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$shipments): ?>
            <tr><td colspan="6">No shipments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="my_shipments.php" class="btn btn-primary">View All Shipments</a>
</div>
<?php include("../templates/footer.php"); ?>
