<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'admin') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

// KPIs
$kpis = [
    "products"    => $pdo->query("SELECT COUNT(*) FROM PRODUCT")->fetchColumn(),
    "batches"     => $pdo->query("SELECT COUNT(*) FROM PACKAGING_BATCH")->fetchColumn(),
    "warehouses"  => $pdo->query("SELECT COUNT(*) FROM WAREHOUSE")->fetchColumn(),
    "employees"   => $pdo->query("SELECT COUNT(*) FROM WAREHOUSE_EMPLOYEE")->fetchColumn(),
    "drivers"     => $pdo->query("SELECT COUNT(*) FROM DRIVER")->fetchColumn(),
    "vehicles"    => $pdo->query("SELECT COUNT(*) FROM VEHICLE")->fetchColumn(),
    "shipments"   => $pdo->query("SELECT COUNT(*) FROM SHIPMENT")->fetchColumn(),
    "orders"      => $pdo->query("SELECT COUNT(*) FROM `ORDER`")->fetchColumn(),
];

// Recent data tables
$latest_orders    = $pdo->query("SELECT o.*, c.Customer_Name FROM `ORDER` o LEFT JOIN CUSTOMER c ON o.Customer_ID = c.CustomerID ORDER BY o.OrderID DESC LIMIT 5")->fetchAll();
$latest_shipments = $pdo->query("SELECT s.*, v.Vehicle_Type, d.Driver_Name FROM SHIPMENT s LEFT JOIN VEHICLE v ON s.Vehicle_ID = v.Vehicle_ID LEFT JOIN DRIVER d ON s.Driver_ID = d.DriverID ORDER BY s.ShipmentID DESC LIMIT 5")->fetchAll();
$latest_batches   = $pdo->query("SELECT b.*, p.Product_Name FROM PACKAGING_BATCH b LEFT JOIN PRODUCT p ON b.Product_ID = p.ProductID ORDER BY b.Batch_ID DESC LIMIT 5")->fetchAll();
?>

<div class="container mt-4">
    <h2>Reports & Dashboard</h2>
    <!-- KPIs -->
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card text-bg-primary"><div class="card-body"><h5 class="card-title">Products</h5><p class="card-text fs-3"><?= $kpis['products'] ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-bg-secondary"><div class="card-body"><h5 class="card-title">Batches</h5><p class="card-text fs-3"><?= $kpis['batches'] ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-bg-success"><div class="card-body"><h5 class="card-title">Warehouses</h5><p class="card-text fs-3"><?= $kpis['warehouses'] ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-bg-danger"><div class="card-body"><h5 class="card-title">Employees</h5><p class="card-text fs-3"><?= $kpis['employees'] ?></p></div></div></div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card text-bg-warning"><div class="card-body"><h5 class="card-title">Drivers</h5><p class="card-text fs-3"><?= $kpis['drivers'] ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-bg-info"><div class="card-body"><h5 class="card-title">Vehicles</h5><p class="card-text fs-3"><?= $kpis['vehicles'] ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-bg-light"><div class="card-body"><h5 class="card-title">Shipments</h5><p class="card-text fs-3"><?= $kpis['shipments'] ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-bg-dark"><div class="card-body"><h5 class="card-title">Orders</h5><p class="card-text fs-3"><?= $kpis['orders'] ?></p></div></div></div>
    </div>

    <!-- Recent Orders Table -->
    <div class="mb-4">
        <h4>Recent Orders</h4>
        <table class="table table-bordered table-striped">
            <thead><tr>
                <th>ID</th><th>Date</th><th>Weight</th><th>Customer</th>
            </tr></thead>
            <tbody>
            <?php foreach($latest_orders as $o): ?>
                <tr>
                    <td><?= $o['OrderID'] ?></td>
                    <td><?= $o['Order_date'] ?></td>
                    <td><?= $o['Ordered_weight'] ?></td>
                    <td><?= htmlspecialchars($o['Customer_Name']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Recent Shipments Table -->
    <div class="mb-4">
        <h4>Recent Shipments</h4>
        <table class="table table-bordered table-striped">
            <thead><tr>
                <th>ID</th><th>Date</th><th>Start</th><th>End</th><th>Vehicle</th><th>Driver</th>
            </tr></thead>
            <tbody>
            <?php foreach($latest_shipments as $s): ?>
                <tr>
                    <td><?= $s['ShipmentID'] ?></td>
                    <td><?= $s['Shipment_Date'] ?></td>
                    <td><?= htmlspecialchars($s['Start_location']) ?></td>
                    <td><?= htmlspecialchars($s['End_location']) ?></td>
                    <td><?= htmlspecialchars($s['Vehicle_Type']) ?></td>
                    <td><?= htmlspecialchars($s['Driver_Name']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Recent Batches Table -->
    <div class="mb-4">
        <h4>Recent Packaging Batches</h4>
        <table class="table table-bordered table-striped">
            <thead><tr>
                <th>ID</th><th>Date</th><th>Total Weight</th><th>Product</th>
            </tr></thead>
            <tbody>
            <?php foreach($latest_batches as $b): ?>
                <tr>
                    <td><?= $b['Batch_ID'] ?></td>
                    <td><?= $b['Pack_Date'] ?></td>
                    <td><?= $b['Total_Weight'] ?></td>
                    <td><?= htmlspecialchars($b['Product_Name']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- You can add charts/graphs here if you want, using Chart.js -->
</div>

<?php include("../templates/footer.php"); ?>
