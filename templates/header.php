<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supply Chain System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    

    <!-- Optional: Your custom CSS -->
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body>
   <?php
if (session_status() === PHP_SESSION_NONE) session_start();
$role = $_SESSION['User_Role'] ?? '';
$dashboardLink = '/index.php'; // Default

switch ($role) {
    case 'admin':
        $dashboardLink = '../admin/dashboard.php'; break;
    case 'warehouse_manager':
        $dashboardLink = '../warehouse_manager/dashboard.php'; break;
    case 'employee':
        $dashboardLink = '../warehouse_employee/dashboard.php'; break;
    case 'farmer':
        $dashboardLink = '../farmer/dashboard.php'; break;
    case 'customer':
        $dashboardLink = '../customer/dashboard.php'; break;
    case 'retailer':
        $dashboardLink = '../customer/dashboard.php'; break; // Or /retailer/dashboard.php if you have it
    case 'transport_manager':
        $dashboardLink = '../transport_manager/dashboard.php'; break;
    case 'driver':
        $dashboardLink = '../driver/dashboard.php'; break;
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?= $dashboardLink ?>">SupplyChain</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>



<?php



?>
