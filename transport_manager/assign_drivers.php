<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'transport_manager') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$msg = "";
// Assign logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipment_id = $_POST['ShipmentID'];
    $driver_id = $_POST['DriverID'];
    $vehicle_id = $_POST['Vehicle_ID'];
    $stmt = $pdo->prepare("UPDATE SHIPMENT SET Driver_ID=?, Vehicle_ID=? WHERE ShipmentID=?");
    $stmt->execute([$driver_id, $vehicle_id, $shipment_id]);
    $msg = "<div class='alert alert-success'>Driver/Vehicle assigned!</div>";
}

// Get shipments without assigned driver/vehicle
$unassigned = $pdo->query("SELECT * FROM SHIPMENT WHERE Driver_ID IS NULL OR Vehicle_ID IS NULL")->fetchAll();
$drivers = $pdo->query("SELECT * FROM DRIVER")->fetchAll();
$vehicles = $pdo->query("SELECT * FROM VEHICLE")->fetchAll();
?>

<div class="container mt-4">
    <h2>Assign Drivers & Vehicles to Shipments</h2>
    <?= $msg ?>
    <table class="table table-bordered table-striped">
        <thead><tr>
            <th>ID</th><th>Date</th><th>From</th><th>To</th><th>Status</th><th>Assign</th>
        </tr></thead>
        <tbody>
        <?php foreach($unassigned as $s): ?>
            <tr>
                <td><?= $s['ShipmentID'] ?></td>
                <td><?= $s['Shipment_Date'] ?></td>
                <td><?= htmlspecialchars($s['Start_location']) ?></td>
                <td><?= htmlspecialchars($s['End_location']) ?></td>
                <td><?= htmlspecialchars($s['Delivery_status']) ?></td>
                <td>
                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="ShipmentID" value="<?= $s['ShipmentID'] ?>">
                        <select name="DriverID" class="form-select" required>
                            <option value="">Select Driver</option>
                            <?php foreach($drivers as $d): ?>
                                <option value="<?= $d['DriverID'] ?>"><?= htmlspecialchars($d['Driver_Name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="Vehicle_ID" class="form-select" required>
                            <option value="">Select Vehicle</option>
                            <?php foreach($vehicles as $v): ?>
                                <option value="<?= $v['Vehicle_ID'] ?>"><?= htmlspecialchars($v['Vehicle_Type']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary btn-sm">Assign</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$unassigned): ?>
            <tr><td colspan="6">All shipments assigned.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include("../templates/footer.php"); ?>
