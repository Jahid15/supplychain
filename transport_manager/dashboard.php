<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'transport_manager') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

// KPIs
$total_shipments = $pdo->query("SELECT COUNT(*) FROM SHIPMENT")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM SHIPMENT WHERE Delivery_status='Pending'")->fetchColumn();
$on_way = $pdo->query("SELECT COUNT(*) FROM SHIPMENT WHERE Delivery_status='On the way'")->fetchColumn();
$delivered = $pdo->query("SELECT COUNT(*) FROM SHIPMENT WHERE Delivery_status='Delivered'")->fetchColumn();

$recent = $pdo->query("
    SELECT s.*, v.Vehicle_Type, d.Driver_Name 
    FROM SHIPMENT s
    LEFT JOIN VEHICLE v ON s.Vehicle_ID = v.Vehicle_ID
    LEFT JOIN DRIVER d ON s.Driver_ID = d.DriverID
    ORDER BY s.ShipmentID DESC LIMIT 5
")->fetchAll();
?>

<div class="container mt-4">
    <h2>Transport Manager Dashboard</h2>
    <div class="row mb-4 g-4">
        <div class="col"><div class="card text-bg-info"><div class="card-body"><b>Total Shipments</b><div class="fs-4"><?= $total_shipments ?></div></div></div></div>
        <div class="col"><div class="card text-bg-warning"><div class="card-body"><b>Pending</b><div class="fs-4"><?= $pending ?></div></div></div></div>
        <div class="col"><div class="card text-bg-primary"><div class="card-body"><b>On the way</b><div class="fs-4"><?= $on_way ?></div></div></div></div>
        <div class="col"><div class="card text-bg-success"><div class="card-body"><b>Delivered</b><div class="fs-4"><?= $delivered ?></div></div></div></div>
    </div>

    <h4>Recent Shipments</h4>
    <table class="table table-bordered table-striped">
        <thead><tr>
            <th>ID</th><th>Date</th><th>From</th><th>To</th><th>Status</th><th>Vehicle</th><th>Driver</th>
        </tr></thead>
        <tbody>
        <?php foreach($recent as $s): ?>
            <tr>
                <td><?= $s['ShipmentID'] ?></td>
                <td><?= $s['Shipment_Date'] ?></td>
                <td><?= htmlspecialchars($s['Start_location']) ?></td>
                <td><?= htmlspecialchars($s['End_location']) ?></td>
                <td><?= htmlspecialchars($s['Delivery_status']) ?></td>
                <td><?= htmlspecialchars($s['Vehicle_Type']) ?></td>
                <td><?= htmlspecialchars($s['Driver_Name']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?>
            <tr><td colspan="7">No shipments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include("../templates/footer.php"); ?>
