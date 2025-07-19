<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'admin') {
    header("Location: ../common/login.php");
    exit;
}
include("../templates/header.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <div style="max-width: 700px; margin: 60px auto; padding: 30px; background-color: rgba(41, 183, 218, 0.1); border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.08);">

        <h2 style="text-align: center; margin-bottom: 25px;">Admin Dashboard</h2>

        <div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;">

            <a href="users.php"
                style="flex: 1 1 200px; text-align: center; padding: 15px 0; background: white; border: 1px solid #29b7da; color: #29b7da; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Manage Users
            </a>

            <a href="manage_products.php"
                style="flex: 1 1 200px; text-align: center; padding: 15px 0; background: white; border: 1px solid #29b7da; color: #29b7da; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Manage Products
            </a>

            <a href="manage_warehouses.php"
                style="flex: 1 1 200px; text-align: center; padding: 15px 0; background: white; border: 1px solid #29b7da; color: #29b7da; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Manage Warehouses
            </a>

            <a href="manage_orders.php"
                style="flex: 1 1 200px; text-align: center; padding: 15px 0; background: white; border: 1px solid #29b7da; color: #29b7da; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Manage Orders
            </a>

            <a href="manage_shipments.php"
                style="flex: 1 1 200px; text-align: center; padding: 15px 0; background: white; border: 1px solid #29b7da; color: #29b7da; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Manage Shipments
            </a>

            <a href="reports.php"
                style="flex: 1 1 200px; text-align: center; padding: 15px 0; background: white; border: 1px solid #29b7da; color: #29b7da; text-decoration: none; border-radius: 6px; font-weight: bold;">
                View Reports
            </a>

            <a href="../common/logout.php"
                style="flex: 1 1 200px; text-align: center; padding: 15px 0; background: #dc3545; border: none; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Logout
            </a>

        </div>
    </div>

</body>

</html>

<?php include("../templates/footer.php"); ?>