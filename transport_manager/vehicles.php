<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'transport_manager') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

$vehicles = $pdo->query("
    SELECT v.*, d.Driver_Name
    FROM VEHICLE v
    LEFT JOIN DRIVER d ON v.Driver_ID = d.DriverID
    ORDER BY v.Vehicle_ID DESC
")->fetchAll();
?>

<div class="container mt-4">
    <h2>All Vehicles</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Max Load</th>
                <th>Driver</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($vehicles as $v): ?>
            <tr>
                <td><?= $v['Vehicle_ID'] ?></td>
                <td><?= htmlspecialchars($v['Vehicle_Type']) ?></td>
                <td><?= $v['Max_Load'] ?></td>
                <td><?= htmlspecialchars($v['Driver_Name']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$vehicles): ?>
            <tr><td colspan="4">No vehicles found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include("../templates/footer.php"); ?>
