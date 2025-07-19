<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'admin') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

// --- DELETE LOGIC ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pdo->prepare("DELETE FROM WAREHOUSE WHERE WarehouseID=?")->execute([$id]);
    header("Location: manage_warehouses.php?msg=Deleted");
    exit;
}

// --- ADD OR EDIT LOGIC ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = $_POST['Location'];
    $capacity = $_POST['Max_Capacity'];
    if (isset($_POST['edit_id'])) {
        // EDIT
        $edit_id = intval($_POST['edit_id']);
        $stmt = $pdo->prepare("UPDATE WAREHOUSE SET Location=?, Max_Capacity=? WHERE WarehouseID=?");
        $stmt->execute([$location, $capacity, $edit_id]);
        $msg = "Warehouse updated!";
    } else {
        // ADD
        $stmt = $pdo->prepare("INSERT INTO WAREHOUSE (Location, Max_Capacity) VALUES (?, ?)");
        $stmt->execute([$location, $capacity]);
        $msg = "Warehouse added!";
    }
}

// --- EDIT MODE ---
$edit_wh = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM WAREHOUSE WHERE WarehouseID=?");
    $stmt->execute([$edit_id]);
    $edit_wh = $stmt->fetch();
}

// --- FETCH ALL WAREHOUSES ---
$warehouses = $pdo->query("SELECT * FROM WAREHOUSE ORDER BY WarehouseID DESC")->fetchAll();
?>

<div class="container mt-4">
    <h2>Warehouse Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD/EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header"><?= $edit_wh ? "Edit Warehouse" : "Add New Warehouse" ?></div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Location</label>
                    <input name="Location" class="form-control" value="<?= $edit_wh['Location'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Max Capacity (kg/ton/etc.)</label>
                    <input type="number" name="Max_Capacity" class="form-control" value="<?= $edit_wh['Max_Capacity'] ?? "" ?>" min="1" required>
                </div>
                <?php if ($edit_wh): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_wh['WarehouseID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_wh ? "Update" : "Add" ?> Warehouse</button>
                <?php if ($edit_wh): ?>
                    <a href="manage_warehouses.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- TABLE OF ALL WAREHOUSES -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Location</th>
                <th>Max Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($warehouses as $wh): ?>
            <tr>
                <td><?= htmlspecialchars($wh['WarehouseID']) ?></td>
                <td><?= htmlspecialchars($wh['Location']) ?></td>
                <td><?= htmlspecialchars($wh['Max_Capacity']) ?></td>
                <td>
                    <a href="manage_warehouses.php?action=edit&id=<?= $wh['WarehouseID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_warehouses.php?action=delete&id=<?= $wh['WarehouseID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this warehouse?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
