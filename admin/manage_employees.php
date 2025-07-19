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
    $pdo->prepare("DELETE FROM WAREHOUSE_EMPLOYEE WHERE EmployeeID=?")->execute([$id]);
    header("Location: manage_employees.php?msg=Deleted");
    exit;
}

// --- GET WAREHOUSE LIST FOR DROPDOWN ---
$warehouses = $pdo->query("SELECT WarehouseID, Location FROM WAREHOUSE ORDER BY Location ASC")->fetchAll();

// --- ADD OR EDIT LOGIC ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['Employee_Name'];
    $role = $_POST['Role'];
    $contact = $_POST['Contact'];
    $warehouse_id = $_POST['WarehouseID'];

    if (isset($_POST['edit_id'])) {
        // EDIT
        $edit_id = intval($_POST['edit_id']);
        $stmt = $pdo->prepare("UPDATE WAREHOUSE_EMPLOYEE SET Employee_Name=?, Role=?, Contact=?, WarehouseID=? WHERE EmployeeID=?");
        $stmt->execute([$name, $role, $contact, $warehouse_id, $edit_id]);
        $msg = "Employee updated!";
    } else {
        // ADD
        $stmt = $pdo->prepare("INSERT INTO WAREHOUSE_EMPLOYEE (Employee_Name, Role, Contact, WarehouseID) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $role, $contact, $warehouse_id]);
        $msg = "Employee added!";
    }
}

// --- EDIT MODE ---
$edit_emp = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM WAREHOUSE_EMPLOYEE WHERE EmployeeID=?");
    $stmt->execute([$edit_id]);
    $edit_emp = $stmt->fetch();
}

// --- FETCH ALL EMPLOYEES (with warehouse name) ---
$employees = $pdo->query("SELECT e.*, w.Location AS WarehouseLocation FROM WAREHOUSE_EMPLOYEE e LEFT JOIN WAREHOUSE w ON e.WarehouseID = w.WarehouseID ORDER BY e.EmployeeID DESC")->fetchAll();
?>

<div class="container mt-4">
    <h2>Warehouse Employee Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD/EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header"><?= $edit_emp ? "Edit Employee" : "Add New Employee" ?></div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Name</label>
                    <input name="Employee_Name" class="form-control" value="<?= $edit_emp['Employee_Name'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Role</label>
                    <input name="Role" class="form-control" value="<?= $edit_emp['Role'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Contact</label>
                    <input name="Contact" class="form-control" value="<?= $edit_emp['Contact'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Warehouse</label>
                    <select name="WarehouseID" class="form-control" required>
                        <option value="">Select Warehouse</option>
                        <?php foreach($warehouses as $w): ?>
                            <option value="<?= $w['WarehouseID'] ?>" <?= (isset($edit_emp) && $edit_emp['WarehouseID'] == $w['WarehouseID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($w['Location']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($edit_emp): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_emp['EmployeeID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_emp ? "Update" : "Add" ?> Employee</button>
                <?php if ($edit_emp): ?>
                    <a href="manage_employees.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- TABLE OF ALL EMPLOYEES -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Contact</th>
                <th>Warehouse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($employees as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['EmployeeID']) ?></td>
                <td><?= htmlspecialchars($e['Employee_Name']) ?></td>
                <td><?= htmlspecialchars($e['Role']) ?></td>
                <td><?= htmlspecialchars($e['Contact']) ?></td>
                <td><?= htmlspecialchars($e['WarehouseLocation']) ?></td>
                <td>
                    <a href="manage_employees.php?action=edit&id=<?= $e['EmployeeID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_employees.php?action=delete&id=<?= $e['EmployeeID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this employee?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
