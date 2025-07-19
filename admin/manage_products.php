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
    $stmt = $pdo->prepare("DELETE FROM PRODUCT WHERE ProductID=?");
    $stmt->execute([$id]);
    header("Location: manage_products.php?msg=Deleted");
    exit;
}

// Fetch farmers for dropdown
$farmers = $pdo->query("SELECT FarmerID, Name FROM FARMER ORDER BY Name ASC")->fetchAll();

// --- ADD OR EDIT FORM HANDLING ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['Product_Name'];
    $type = $_POST['Product_Type'];
    $harvest = $_POST['Harvest_date'];
    $shelf = $_POST['Shelf_Life_Days'];
    $farmer = $_POST['FarmerID'];

    if (isset($_POST['edit_id'])) {
        // EDIT PRODUCT
        $edit_id = intval($_POST['edit_id']);
        $sql = "UPDATE PRODUCT SET Product_Name=?, Product_Type=?, Harvest_date=?, Shelf_Life_Days=?, FarmerID=? WHERE ProductID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $type, $harvest, $shelf, $farmer, $edit_id]);
        $msg = "Product updated!";
    } else {
        // ADD PRODUCT
        $sql = "INSERT INTO PRODUCT (Product_Name, Product_Type, Harvest_date, Shelf_Life_Days, FarmerID) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $type, $harvest, $shelf, $farmer]);
        $msg = "Product added!";
    }
}

// --- FETCH FOR EDIT ---
$edit_product = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM PRODUCT WHERE ProductID=?");
    $stmt->execute([$edit_id]);
    $edit_product = $stmt->fetch();
}

// Fetch all products
$stmt = $pdo->query("SELECT p.*, f.Name AS FarmerName FROM PRODUCT p LEFT JOIN FARMER f ON p.FarmerID = f.FarmerID ORDER BY p.ProductID DESC");
$products = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h2>Product Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD or EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header">
            <?= $edit_product ? "Edit Product" : "Add New Product" ?>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Product Name</label>
                    <input name="Product_Name" class="form-control" value="<?= $edit_product['Product_Name'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Product Type</label>
                    <input name="Product_Type" class="form-control" value="<?= $edit_product['Product_Type'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Harvest Date</label>
                    <input type="date" name="Harvest_date" class="form-control" value="<?= $edit_product['Harvest_date'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Shelf Life (Days)</label>
                    <input type="number" name="Shelf_Life_Days" class="form-control" value="<?= $edit_product['Shelf_Life_Days'] ?? "" ?>" min="1" required>
                </div>
                <div class="mb-3">
                    <label>Farmer</label>
                    <select name="FarmerID" class="form-control" required>
                        <option value="">Select Farmer</option>
                        <?php foreach($farmers as $f): ?>
                            <option value="<?= $f['FarmerID'] ?>" <?= (isset($edit_product) && $edit_product['FarmerID'] == $f['FarmerID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($f['Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($edit_product): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_product['ProductID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_product ? "Update" : "Add" ?> Product</button>
                <?php if ($edit_product): ?>
                    <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- PRODUCT LIST TABLE -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Harvest Date</th>
                <th>Shelf Life</th>
                <th>Farmer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['ProductID']) ?></td>
                <td><?= htmlspecialchars($product['Product_Name']) ?></td>
                <td><?= htmlspecialchars($product['Product_Type']) ?></td>
                <td><?= htmlspecialchars($product['Harvest_date']) ?></td>
                <td><?= htmlspecialchars($product['Shelf_Life_Days']) ?></td>
                <td><?= htmlspecialchars($product['FarmerName']) ?></td>
                <td>
                    <a href="manage_products.php?action=edit&id=<?= $product['ProductID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_products.php?action=delete&id=<?= $product['ProductID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
