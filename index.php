<?php
session_start();
include("templates/header.php");
?>

<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="mb-4">Supply Chain Management System</h1>
            <p class="lead">
                Welcome to your all-in-one platform for managing agricultural products, warehouse operations, logistics, and real-time shipment tracking.
            </p>
            <ul class="list-group mb-4">
                <li class="list-group-item">🌾 Farmers – View your crop status</li>
                <li class="list-group-item">🏢 Warehouse Managers & Employees – Oversee inventory & stock movement</li>
                <li class="list-group-item">🚚 Transport Managers & Drivers – Plan & deliver shipments</li>
                <li class="list-group-item">🛒 Retailers & Customers – Place and track orders</li>
                <li class="list-group-item">👤 Admin – Full system control & reports</li>
            </ul>
            <a href="common/login.php" class="btn btn-primary btn-lg">Login to System</a>
        </div>
        <div class="col-md-6 text-center">
            <img src="assets/images/img.png" alt="Supply Chain" style="max-width: 90%; height: auto;">
        </div>
    </div>

    <!-- Optional: Demo/test login info (remove in production) -->
    <div class="mt-5">
        <h4>How does it work?</h4>
        <ol>
            <li>Login as any user type (admin, warehouse manager, driver, etc.)</li>
            <li>Your dashboard shows relevant actions and data for your role</li>
            <li>Seamlessly manage agricultural products, shipments, inventory, and more!</li>
        </ol>
    </div>
</div>

<?php include("templates/footer.php"); ?>
