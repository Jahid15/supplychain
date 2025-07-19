<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'farmer') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$farmer_id = $_SESSION['Ref_ID'] ?? 0;

// Get all products from this farmer
$products = $pdo->query("SELECT * FROM PRODUCT WHERE FarmerID = $farmer_id ORDER BY ProductID DESC")->fetchAll();
?>

<div class="container mt-4">
    <h2>My Products</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Harvest Date</th>
                <th>Shelf Life</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($products as $p): ?>
            <tr>
                <td><?= $p['ProductID'] ?></td>
                <td><?= htmlspecialchars($p['Product_Name']) ?></td>
                <td><?= htmlspecialchars($p['Product_Type']) ?></td>
                <td><?= $p['Harvest_date'] ?></td>
                <td><?= $p['Shelf_Life_Days'] ?> days</td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$products): ?>
            <tr><td colspan="5">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
<?php include("../templates/footer.php"); ?>
