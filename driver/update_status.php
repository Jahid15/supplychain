<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'driver') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$driver_id = $_SESSION['Ref_ID'] ?? 0;
$shipment_id = $_GET['id'] ?? 0;

// Fetch shipment (only if assigned to this driver)
$stmt = $pdo->prepare("SELECT * FROM SHIPMENT WHERE ShipmentID=? AND Driver_ID=?");
$stmt->execute([$shipment_id, $driver_id]);
$shipment = $stmt->fetch();

if (!$shipment) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Invalid shipment or access denied.</div></div>';
    include("../templates/footer.php");
    exit;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['Delivery_status'];
    $stmt = $pdo->prepare("UPDATE SHIPMENT SET Delivery_status=? WHERE ShipmentID=? AND Driver_ID=?");
    $stmt->execute([$status, $shipment_id, $driver_id]);
    $msg = "<div class='alert alert-success'>Status updated!</div>";
    // Refresh shipment data
    $stmt = $pdo->prepare("SELECT * FROM SHIPMENT WHERE ShipmentID=? AND Driver_ID=?");
    $stmt->execute([$shipment_id, $driver_id]);
    $shipment = $stmt->fetch();
}
?>

<div class="container mt-4">
    <h2>Update Delivery Status</h2>
    <?= $msg ?>
    <form method="post">
        <div class="mb-3">
            <label>Current Status</label>
            <input class="form-control" value="<?= htmlspecialchars($shipment['Delivery_status']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label>New Status</label>
            <select name="Delivery_status" class="form-control" required>
                <option value="Pending" <?= $shipment['Delivery_status']=='Pending'?'selected':'' ?>>Pending</option>
                <option value="On the way" <?= $shipment['Delivery_status']=='On the way'?'selected':'' ?>>On the way</option>
                <option value="Delivered" <?= $shipment['Delivery_status']=='Delivered'?'selected':'' ?>>Delivered</option>
                <option value="Delayed" <?= $shipment['Delivery_status']=='Delayed'?'selected':'' ?>>Delayed</option>
                <option value="Cancelled" <?= $shipment['Delivery_status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
            </select>
        </div>
        <button class="btn btn-success">Update Status</button>
        <a href="my_shipments.php" class="btn btn-secondary">Back</a>
    </form>
</div>
<?php include("../templates/footer.php"); ?>
