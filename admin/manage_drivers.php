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
    $pdo->prepare("DELETE FROM DRIVER WHERE DriverID=?")->execute([$id]);
    header("Location: manage_drivers.php?msg=Deleted");
    exit;
}

// --- ADD OR EDIT LOGIC ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['Driver_Name'];
    $license = $_POST['LicenseNo'];
    $phone = $_POST['Phone'];

    if (isset($_POST['edit_id'])) {
        // EDIT
        $edit_id = intval($_POST['edit_id']);
        $stmt = $pdo->prepare("UPDATE DRIVER SET Driver_Name=?, LicenseNo=?, Phone=? WHERE DriverID=?");
        $stmt->execute([$name, $license, $phone, $edit_id]);
        $msg = "Driver updated!";
    } else {
        // ADD
        $stmt = $pdo->prepare("INSERT INTO DRIVER (Driver_Name, LicenseNo, Phone) VALUES (?, ?, ?)");
        $stmt->execute([$name, $license, $phone]);
        $msg = "Driver added!";
    }
}

// --- EDIT MODE ---
$edit_driver = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM DRIVER WHERE DriverID=?");
    $stmt->execute([$edit_id]);
    $edit_driver = $stmt->fetch();
}

// --- FETCH ALL DRIVERS ---
$drivers = $pdo->query("SELECT * FROM DRIVER ORDER BY DriverID DESC")->fetchAll();
?>

<div class="container mt-4">
    <h2>Driver Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD/EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header"><?= $edit_driver ? "Edit Driver" : "Add New Driver" ?></div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Name</label>
                    <input name="Driver_Name" class="form-control" value="<?= $edit_driver['Driver_Name'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>License No</label>
                    <input name="LicenseNo" class="form-control" value="<?= $edit_driver['LicenseNo'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Phone</label>
                    <input name="Phone" class="form-control" value="<?= $edit_driver['Phone'] ?? "" ?>" required>
                </div>
                <?php if ($edit_driver): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_driver['DriverID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_driver ? "Update" : "Add" ?> Driver</button>
                <?php if ($edit_driver): ?>
                    <a href="manage_drivers.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- TABLE OF ALL DRIVERS -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>License No</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($drivers as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['DriverID']) ?></td>
                <td><?= htmlspecialchars($d['Driver_Name']) ?></td>
                <td><?= htmlspecialchars($d['LicenseNo']) ?></td>
                <td><?= htmlspecialchars($d['Phone']) ?></td>
                <td>
                    <a href="manage_drivers.php?action=edit&id=<?= $d['DriverID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_drivers.php?action=delete&id=<?= $d['DriverID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this driver?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
