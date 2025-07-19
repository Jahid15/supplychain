<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'farmer') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$farmer_id = $_SESSION['Ref_ID'] ?? 0;

// Farmer info (optional)
$stmt = $pdo->prepare("SELECT * FROM FARMER WHERE FarmerID=?");
$stmt->execute([$farmer_id]);
$farmer = $stmt->fetch();

// Count products for this farmer
$product_count = $pdo->query("SELECT COUNT(*) FROM PRODUCT WHERE FarmerID = $farmer_id")->fetchColumn();
$products = $pdo->query("SELECT * FROM PRODUCT WHERE FarmerID = $farmer_id ORDER BY ProductID DESC LIMIT 5")->fetchAll();
?>

<div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($farmer['Name'] ?? $_SESSION['Username']) ?>!</h2>
    <p>Your Farmer Dashboard</p>

    <div class="mb-4">
        <div class="alert alert-info">You have <b><?= $product_count ?></b> products in the system.</div>
    </div>

    <h4>Recent Products</h4>
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

    <a href="my_products.php" class="btn btn-primary">View All My Products</a>
</div>
<?php include("../templates/footer.php"); ?>
