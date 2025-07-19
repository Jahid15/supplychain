<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'admin') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

// --- DELETE LOGIC ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pdo->prepare("DELETE FROM SHIPMENT WHERE ShipmentID=?")->execute([$id]);
    header("Location: manage_shipments.php?msg=Deleted");
    exit;
}

// --- DROPDOWNS ---
$batches = $pdo->query("SELECT Batch_ID FROM PACKAGING_BATCH ORDER BY Batch_ID DESC")->fetchAll();
$vehicles = $pdo->query("SELECT Vehicle_ID, Vehicle_Type FROM VEHICLE ORDER BY Vehicle_Type ASC")->fetchAll();
$drivers = $pdo->query("SELECT DriverID, Driver_Name FROM DRIVER ORDER BY Driver_Name ASC")->fetchAll();

// --- ADD OR EDIT LOGIC ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipment_date = $_POST['Shipment_Date'];
    $start_location = $_POST['Start_location'];
    $end_location = $_POST['End_location'];
    $distance = $_POST['Distance'];
    $delivery_status = $_POST['Delivery_status'];
    $batch_id = $_POST['Batch_ID'];
    $vehicle_id = $_POST['Vehicle_ID'];
    $driver_id = $_POST['Driver_ID'];

    if (isset($_POST['edit_id'])) {
        // EDIT
        $edit_id = intval($_POST['edit_id']);
        $stmt = $pdo->prepare("UPDATE SHIPMENT SET Shipment_Date=?, Start_location=?, End_location=?, Distance=?, Delivery_status=?, Batch_ID=?, Vehicle_ID=?, Driver_ID=? WHERE ShipmentID=?");
        $stmt->execute([$shipment_date, $start_location, $end_location, $distance, $delivery_status, $batch_id, $vehicle_id, $driver_id, $edit_id]);
        $msg = "Shipment updated!";
    } else {
        // ADD
        $stmt = $pdo->prepare("INSERT INTO SHIPMENT (Shipment_Date, Start_location, End_location, Distance, Delivery_status, Batch_ID, Vehicle_ID, Driver_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$shipment_date, $start_location, $end_location, $distance, $delivery_status, $batch_id, $vehicle_id, $driver_id]);
        $msg = "Shipment added!";
    }
}

// --- EDIT MODE ---
$edit_shipment = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM SHIPMENT WHERE ShipmentID=?");
    $stmt->execute([$edit_id]);
    $edit_shipment = $stmt->fetch();
}

// --- FETCH ALL SHIPMENTS (with batch, vehicle, and driver names) ---
$shipments = $pdo->query("
    SELECT s.*, b.Batch_ID, v.Vehicle_Type, d.Driver_Name
    FROM SHIPMENT s
    LEFT JOIN PACKAGING_BATCH b ON s.Batch_ID = b.Batch_ID
    LEFT JOIN VEHICLE v ON s.Vehicle_ID = v.Vehicle_ID
    LEFT JOIN DRIVER d ON s.Driver_ID = d.DriverID
    ORDER BY s.ShipmentID DESC
")->fetchAll();
?>

<div class="container mt-4">
    <h2>Shipment Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD/EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header"><?= $edit_shipment ? "Edit Shipment" : "Add New Shipment" ?></div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Shipment Date</label>
                    <input type="date" name="Shipment_Date" class="form-control" value="<?= $edit_shipment['Shipment_Date'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Start Location</label>
                    <input name="Start_location" class="form-control" value="<?= $edit_shipment['Start_location'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>End Location</label>
                    <input name="End_location" class="form-control" value="<?= $edit_shipment['End_location'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Distance</label>
                    <input type="number" step="0.01" name="Distance" class="form-control" value="<?= $edit_shipment['Distance'] ?? "" ?>" min="0" required>
                </div>
                <div class="mb-3">
                    <label>Delivery Status</label>
                    <input name="Delivery_status" class="form-control" value="<?= $edit_shipment['Delivery_status'] ?? "Pending" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Batch</label>
                    <select name="Batch_ID" class="form-control" required>
                        <option value="">Select Batch</option>
                        <?php foreach($batches as $b): ?>
                            <option value="<?= $b['Batch_ID'] ?>" <?= (isset($edit_shipment) && $edit_shipment['Batch_ID'] == $b['Batch_ID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($b['Batch_ID']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Vehicle</label>
                    <select name="Vehicle_ID" class="form-control" required>
                        <option value="">Select Vehicle</option>
                        <?php foreach($vehicles as $v): ?>
                            <option value="<?= $v['Vehicle_ID'] ?>" <?= (isset($edit_shipment) && $edit_shipment['Vehicle_ID'] == $v['Vehicle_ID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($v['Vehicle_Type']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Driver</label>
                    <select name="Driver_ID" class="form-control" required>
                        <option value="">Select Driver</option>
                        <?php foreach($drivers as $d): ?>
                            <option value="<?= $d['DriverID'] ?>" <?= (isset($edit_shipment) && $edit_shipment['Driver_ID'] == $d['DriverID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($d['Driver_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($edit_shipment): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_shipment['ShipmentID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_shipment ? "Update" : "Add" ?> Shipment</button>
                <?php if ($edit_shipment): ?>
                    <a href="manage_shipments.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- TABLE OF ALL SHIPMENTS -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Shipment Date</th>
                <th>Start Location</th>
                <th>End Location</th>
                <th>Distance</th>
                <th>Status</th>
                <th>Batch</th>
                <th>Vehicle</th>
                <th>Driver</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($shipments as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['ShipmentID']) ?></td>
                <td><?= htmlspecialchars($s['Shipment_Date']) ?></td>
                <td><?= htmlspecialchars($s['Start_location']) ?></td>
                <td><?= htmlspecialchars($s['End_location']) ?></td>
                <td><?= htmlspecialchars($s['Distance']) ?></td>
                <td><?= htmlspecialchars($s['Delivery_status']) ?></td>
                <td><?= htmlspecialchars($s['Batch_ID']) ?></td>
                <td><?= htmlspecialchars($s['Vehicle_Type']) ?></td>
                <td><?= htmlspecialchars($s['Driver_Name']) ?></td>
                <td>
                    <a href="manage_shipments.php?action=edit&id=<?= $s['ShipmentID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_shipments.php?action=delete&id=<?= $s['ShipmentID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this shipment?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
