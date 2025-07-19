<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'transport_manager') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$msg = "";
// Plan shipment logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['Shipment_Date'];
    $start = $_POST['Start_location'];
    $end = $_POST['End_location'];
    $distance = $_POST['Distance'];
    $batch = $_POST['Batch_ID'];
    $stmt = $pdo->prepare("INSERT INTO SHIPMENT (Shipment_Date, Start_location, End_location, Distance, Delivery_status, Batch_ID) VALUES (?, ?, ?, ?, 'Pending', ?)");
    $stmt->execute([$date, $start, $end, $distance, $batch]);
    $msg = "<div class='alert alert-success'>Shipment planned!</div>";
}

$batches = $pdo->query("SELECT b.Batch_ID, p.Product_Name FROM PACKAGING_BATCH b LEFT JOIN PRODUCT p ON b.Product_ID = p.ProductID")->fetchAll();
?>

<div class="container mt-4">
    <h2>Plan New Shipment</h2>
    <?= $msg ?>
    <form method="post" class="mb-4">
        <div class="row mb-3">
            <div class="col">
                <label>Date</label>
                <input type="date" name="Shipment_Date" class="form-control" required>
            </div>
            <div class="col">
                <label>Start Location</label>
                <input name="Start_location" class="form-control" required>
            </div>
            <div class="col">
                <label>End Location</label>
                <input name="End_location" class="form-control" required>
            </div>
            <div class="col">
                <label>Distance (km)</label>
                <input type="number" step="0.01" name="Distance" class="form-control" required>
            </div>
            <div class="col">
                <label>Batch</label>
                <select name="Batch_ID" class="form-select" required>
                    <option value="">Select Batch</option>
                    <?php foreach($batches as $b): ?>
                        <option value="<?= $b['Batch_ID'] ?>"><?= htmlspecialchars($b['Product_Name']) ?> (Batch <?= $b['Batch_ID'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button class="btn btn-success">Plan Shipment</button>
    </form>
</div>
<?php include("../templates/footer.php"); ?>
