<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'transport_manager') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$shipments = $pdo->query("
    SELECT s.*, d.Driver_Name, v.Vehicle_Type
    FROM SHIPMENT s
    LEFT JOIN DRIVER d ON s.Driver_ID = d.DriverID
    LEFT JOIN VEHICLE v ON s.Vehicle_ID = v.Vehicle_ID
    ORDER BY s.ShipmentID DESC
")->fetchAll();
?>

<div class="container mt-4">
    <h2>Track Shipments</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Date</th><th>From</th><th>To</th><th>Status</th><th>Driver</th><th>Vehicle</th>
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
            </tr>
        <?php endforeach; ?>
        <?php if (!$shipments): ?>
            <tr><td colspan="7">No shipments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include("../templates/footer.php"); ?>
