<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'customer') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$customer_id = $_SESSION['Ref_ID'] ?? 0;
$msg = "";

// Get available batches (with product info)
$batches = $pdo->query("
    SELECT b.Batch_ID, b.Total_Weight, p.Product_Name
    FROM PACKAGING_BATCH b
    LEFT JOIN PRODUCT p ON b.Product_ID = p.ProductID
    ORDER BY p.Product_Name ASC, b.Batch_ID DESC
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $customer_id) {
    $batch_id = $_POST['Batch_ID'];
    $weight = $_POST['Ordered_weight'];
    $order_date = date('Y-m-d');

    // Optionally, check if enough weight is available in batch
    $stmt = $pdo->prepare("SELECT Total_Weight FROM PACKAGING_BATCH WHERE Batch_ID=?");
    $stmt->execute([$batch_id]);
    $available = $stmt->fetchColumn();

    if ($weight > $available) {
        $msg = "<div class='alert alert-danger'>Not enough stock in batch.</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO `ORDER` (Order_date, Ordered_weight, Batch_ID, Customer_ID) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_date, $weight, $batch_id, $customer_id]);
        $msg = "<div class='alert alert-success'>Order placed successfully!</div>";
    }
}
?>

<div class="container mt-4">
    <h2>Place New Order</h2>
    <?= $msg ?>
    <form method="post">
        <div class="mb-3">
            <label>Product Batch</label>
            <select name="Batch_ID" class="form-control" required>
                <option value="">Select Batch</option>
                <?php foreach($batches as $b): ?>
                    <option value="<?= $b['Batch_ID'] ?>">
                        <?= htmlspecialchars($b['Product_Name']) ?> (Batch <?= $b['Batch_ID'] ?>, Available: <?= $b['Total_Weight'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Order Weight</label>
            <input type="number" step="0.01" min="0.01" name="Ordered_weight" class="form-control" required>
        </div>
        <button class="btn btn-success">Place Order</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
<?php include("../templates/footer.php"); ?>
