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
    $pdo->prepare("DELETE FROM PACKAGING_BATCH WHERE Batch_ID=?")->execute([$id]);
    header("Location: manage_batches.php?msg=Deleted");
    exit;
}

// --- ADD OR EDIT LOGIC ---
$msg = "";
// Get all products for dropdown
$products = $pdo->query("SELECT ProductID, Product_Name FROM PRODUCT ORDER BY Product_Name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pack_date = $_POST['Pack_Date'];
    $total_weight = $_POST['Total_Weight'];
    $product_id = $_POST['Product_ID'];

    if (isset($_POST['edit_id'])) {
        // EDIT
        $edit_id = intval($_POST['edit_id']);
        $stmt = $pdo->prepare("UPDATE PACKAGING_BATCH SET Pack_Date=?, Total_Weight=?, Product_ID=? WHERE Batch_ID=?");
        $stmt->execute([$pack_date, $total_weight, $product_id, $edit_id]);
        $msg = "Batch updated!";
    } else {
        // ADD
        $stmt = $pdo->prepare("INSERT INTO PACKAGING_BATCH (Pack_Date, Total_Weight, Product_ID) VALUES (?, ?, ?)");
        $stmt->execute([$pack_date, $total_weight, $product_id]);
        $msg = "Batch added!";
    }
}

// --- EDIT MODE ---
$edit_batch = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM PACKAGING_BATCH WHERE Batch_ID=?");
    $stmt->execute([$edit_id]);
    $edit_batch = $stmt->fetch();
}

// --- FETCH ALL BATCHES (with product name) ---
$batches = $pdo->query("SELECT b.*, p.Product_Name FROM PACKAGING_BATCH b LEFT JOIN PRODUCT p ON b.Product_ID = p.ProductID ORDER BY b.Batch_ID DESC")->fetchAll();
?>

<div class="container mt-4">
    <h2>Packaging Batch Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD/EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header"><?= $edit_batch ? "Edit Batch" : "Add New Batch" ?></div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Pack Date</label>
                    <input type="date" name="Pack_Date" class="form-control" value="<?= $edit_batch['Pack_Date'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Total Weight</label>
                    <input type="number" step="0.01" name="Total_Weight" class="form-control" value="<?= $edit_batch['Total_Weight'] ?? "" ?>" min="0.01" required>
                </div>
                <div class="mb-3">
                    <label>Product</label>
                    <select name="Product_ID" class="form-control" required>
                        <option value="">Select Product</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?= $p['ProductID'] ?>" <?= (isset($edit_batch) && $edit_batch['Product_ID'] == $p['ProductID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($p['Product_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($edit_batch): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_batch['Batch_ID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_batch ? "Update" : "Add" ?> Batch</button>
                <?php if ($edit_batch): ?>
                    <a href="manage_batches.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- TABLE OF ALL BATCHES -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pack Date</th>
                <th>Total Weight</th>
                <th>Product</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($batches as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['Batch_ID']) ?></td>
                <td><?= htmlspecialchars($b['Pack_Date']) ?></td>
                <td><?= htmlspecialchars($b['Total_Weight']) ?></td>
                <td><?= htmlspecialchars($b['Product_Name']) ?></td>
                <td>
                    <a href="manage_batches.php?action=edit&id=<?= $b['Batch_ID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_batches.php?action=delete&id=<?= $b['Batch_ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this batch?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
