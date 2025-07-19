<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'employee') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$employee_id = $_SESSION['Ref_ID'] ?? 0;

// Get all warehouses assigned to this employee
$stmt = $pdo->prepare("SELECT WarehouseID FROM WAREHOUSE_EMPLOYEE WHERE EmployeeID=?");
$stmt->execute([$employee_id]);
$warehouse_id = $stmt->fetchColumn();

// For dropdowns
$batches = $pdo->query("SELECT b.Batch_ID, p.Product_Name FROM PACKAGING_BATCH b LEFT JOIN PRODUCT p ON b.Product_ID = p.ProductID ORDER BY b.Batch_ID DESC")->fetchAll();

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_id = $_POST['Batch_ID'];
    $stored_weight = $_POST['Stored_weight'];

    $stmt = $pdo->prepare("INSERT INTO PRODUCT_STORAGE (Warehouse_ID, Batch_ID, Stored_weight, Employee_ID) VALUES (?, ?, ?, ?)");
    $stmt->execute([$warehouse_id, $batch_id, $stored_weight, $employee_id]);
    $msg = "<div class='alert alert-success'>Batch received from farmer and stored!</div>";
}
?>

<div class="container mt-4">
    <h2>Receive Batch from Farmer</h2>
    <?= $msg ?>
    <form method="post">
        <div class="mb-3">
            <label>Batch</label>
            <select name="Batch_ID" class="form-control" required>
                <option value="">Select Batch</option>
                <?php foreach($batches as $b): ?>
                    <option value="<?= $b['Batch_ID'] ?>"><?= htmlspecialchars($b['Product_Name']) ?> (Batch <?= $b['Batch_ID'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Stored Weight</label>
            <input type="number" step="0.01" name="Stored_weight" class="form-control" required>
        </div>
        <button class="btn btn-success">Receive & Store</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
<?php include("../templates/footer.php"); ?>
