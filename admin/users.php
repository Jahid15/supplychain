<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'admin') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../includes/functions.php");
include("../templates/header.php");

// --- DELETE USER ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Prevent admin from deleting themselves
    if ($id != $_SESSION['UserID']) {
        $pdo->prepare("DELETE FROM users WHERE UserID=?")->execute([$id]);
        header("Location: users.php?msg=Deleted");
        exit;
    }
}

// --- ADD OR EDIT USER ---
$msg = "";
$roles = ['admin', 'warehouse_manager', 'employee', 'farmer', 'customer', 'retailer', 'transport_manager', 'driver'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['Username']);
    $email = trim($_POST['Email']);
    $role = $_POST['User_Role'];
    $ref_id = $_POST['Ref_ID'] ? intval($_POST['Ref_ID']) : NULL;
    // Password handling
    $password = $_POST['Password'] ? hashPassword($_POST['Password']) : null;

    if (isset($_POST['edit_id'])) {
        // EDIT
        $edit_id = intval($_POST['edit_id']);
        if ($password) {
            $sql = "UPDATE users SET Username=?, Email=?, User_Role=?, Ref_ID=?, Password=? WHERE UserID=?";
            $params = [$username, $email, $role, $ref_id, $password, $edit_id];
        } else {
            $sql = "UPDATE users SET Username=?, Email=?, User_Role=?, Ref_ID=? WHERE UserID=?";
            $params = [$username, $email, $role, $ref_id, $edit_id];
        }
        $stmt = $pdo->prepare($sql);
        if($stmt->execute($params)) {
            $msg = "User updated!";
        } else {
            $msg = "Update failed (duplicate username/email?)";
        }
    } else {
        // ADD
        if (!$password) $msg = "Password required for new users!";
        else {
            $sql = "INSERT INTO users (Username, Password, Email, User_Role, Ref_ID) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([$username, $password, $email, $role, $ref_id])) {
                $msg = "User added!";
            } else {
                $msg = "Add failed (duplicate username/email?)";
            }
        }
    }
}

// --- EDIT MODE ---
$edit_user = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE UserID=?");
    $stmt->execute([$edit_id]);
    $edit_user = $stmt->fetch();
}

// --- FETCH ALL USERS ---
$users = $pdo->query("SELECT * FROM users ORDER BY UserID DESC")->fetchAll();
?>

<div class="container mt-4">
    <h2>User Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD/EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header"><?= $edit_user ? "Edit User" : "Add New User" ?></div>
        <div class="card-body">
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label>Username</label>
                    <input name="Username" class="form-control" value="<?= $edit_user['Username'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input name="Email" type="email" class="form-control" value="<?= $edit_user['Email'] ?? "" ?>">
                </div>
                <div class="mb-3">
                    <label>Role</label>
                    <select name="User_Role" class="form-control" required>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r ?>" <?= (isset($edit_user) && $edit_user['User_Role']==$r) ? "selected" : "" ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Ref ID <small>(Optional: link to FarmerID, EmployeeID, etc.)</small></label>
                    <input name="Ref_ID" type="number" class="form-control" value="<?= $edit_user['Ref_ID'] ?? "" ?>">
                </div>
                <div class="mb-3">
                    <label>
                        <?php if($edit_user): ?>Change Password (leave blank to keep old)<?php else: ?>Password<?php endif; ?>
                    </label>
                    <input name="Password" type="password" class="form-control" <?= $edit_user ? "" : "required" ?>>
                </div>
                <?php if ($edit_user): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_user['UserID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_user ? "Update" : "Add" ?> User</button>
                <?php if ($edit_user): ?>
                    <a href="users.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- USERS TABLE -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Ref ID</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['UserID']) ?></td>
                <td><?= htmlspecialchars($u['Username']) ?></td>
                <td><?= htmlspecialchars($u['Email']) ?></td>
                <td><?= htmlspecialchars($u['User_Role']) ?></td>
                <td><?= htmlspecialchars($u['Ref_ID']) ?></td>
                <td><?= htmlspecialchars($u['Created_At']) ?></td>
                <td>
                    <a href="users.php?action=edit&id=<?= $u['UserID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <?php if ($u['UserID'] != $_SESSION['UserID']): ?>
                        <a href="users.php?action=delete&id=<?= $u['UserID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                    <?php else: ?>
                        <span class="badge bg-secondary">You</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
