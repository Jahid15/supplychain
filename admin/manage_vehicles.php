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
    $pdo->prepare("DELETE FROM VEHICLE WHERE Vehicle_ID=?")->execute([$id]);
    header("Location: manage_vehicles.php?msg=Deleted");
    exit;
}

// --- DRIVER DROPDOWN ---
$drivers = $pdo->query("SELECT DriverID, Driver_Name FROM DRIVER ORDER BY Driver_Name ASC")->fetchAll();

// --- ADD OR EDIT LOGIC ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['Vehicle_Type'];
    $max_load = $_POST['Max_Load'];
    $driver_id = $_POST['Driver_ID'];

    if (isset($_POST['edit_id'])) {
        // EDIT
        $edit_id = intval($_POST['edit_id']);
        $stmt = $pdo->prepare("UPDATE VEHICLE SET Vehicle_Type=?, Max_Load=?, Driver_ID=? WHERE Vehicle_ID=?");
        $stmt->execute([$type, $max_load, $driver_id, $edit_id]);
        $msg = "Vehicle updated!";
    } else {
        // ADD
        $stmt = $pdo->prepare("INSERT INTO VEHICLE (Vehicle_Type, Max_Load, Driver_ID) VALUES (?, ?, ?)");
        $stmt->execute([$type, $max_load, $driver_id]);
        $msg = "Vehicle added!";
    }
}

// --- EDIT MODE ---
$edit_vehicle = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM VEHICLE WHERE Vehicle_ID=?");
    $stmt->execute([$edit_id]);
    $edit_vehicle = $stmt->fetch();
}

// --- FETCH ALL VEHICLES (with driver name) ---
$vehicles = $pdo->query("SELECT v.*, d.Driver_Name FROM VEHICLE v LEFT JOIN DRIVER d ON v.Driver_ID = d.DriverID ORDER BY v.Vehicle_ID DESC")->fetchAll();
?>

<div class="container mt-4">
    <h2>Vehicle Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD/EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header"><?= $edit_vehicle ? "Edit Vehicle" : "Add New Vehicle" ?></div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Vehicle Type</label>
                    <input name="Vehicle_Type" class="form-control" value="<?= $edit_vehicle['Vehicle_Type'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Max Load</label>
                    <input type="number" step="0.01" name="Max_Load" class="form-control" value="<?= $edit_vehicle['Max_Load'] ?? "" ?>" min="0.01" required>
                </div>
                <div class="mb-3">
                    <label>Driver</label>
                    <select name="Driver_ID" class="form-control" required>
                        <option value="">Select Driver</option>
                        <?php foreach($drivers as $d): ?>
                            <option value="<?= $d['DriverID'] ?>" <?= (isset($edit_vehicle) && $edit_vehicle['Driver_ID'] == $d['DriverID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($d['Driver_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($edit_vehicle): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_vehicle['Vehicle_ID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_vehicle ? "Update" : "Add" ?> Vehicle</button>
                <?php if ($edit_vehicle): ?>
                    <a href="manage_vehicles.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- TABLE OF ALL VEHICLES -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vehicle Type</th>
                <th>Max Load</th>
                <th>Driver</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($vehicles as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['Vehicle_ID']) ?></td>
                <td><?= htmlspecialchars($v['Vehicle_Type']) ?></td>
                <td><?= htmlspecialchars($v['Max_Load']) ?></td>
                <td><?= htmlspecialchars($v['Driver_Name']) ?></td>
                <td>
                    <a href="manage_vehicles.php?action=edit&id=<?= $v['Vehicle_ID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_vehicles.php?action=delete&id=<?= $v['Vehicle_ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this vehicle?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
