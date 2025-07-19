<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== 'admin') {
    header("Location: ../common/login.php");
    exit;
}
include("../includes/db.php");
include("../templates/header.php");

// --- DELETE LOGIC ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pdo->prepare("DELETE FROM `ORDER` WHERE OrderID=?")->execute([$id]);
    header("Location: manage_orders.php?msg=Deleted");
    exit;
}

// --- DROPDOWNS ---
$batches = $pdo->query("SELECT Batch_ID FROM PACKAGING_BATCH ORDER BY Batch_ID DESC")->fetchAll();
$customers = $pdo->query("SELECT CustomerID, Customer_Name FROM CUSTOMER ORDER BY Customer_Name ASC")->fetchAll();
$retailers = $pdo->query("SELECT Retailer_ID, Retailer_Name FROM RETAILER ORDER BY Retailer_Name ASC")->fetchAll();

// --- ADD OR EDIT LOGIC ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_date = $_POST['Order_date'];
    $ordered_weight = $_POST['Ordered_weight'];
    $batch_id = $_POST['Batch_ID'];
    $customer_id = !empty($_POST['Customer_ID']) ? $_POST['Customer_ID'] : null;
    $retailer_id = !empty($_POST['Retailer_ID']) ? $_POST['Retailer_ID'] : null;

    if (isset($_POST['edit_id'])) {
        // EDIT
        $edit_id = intval($_POST['edit_id']);
        $stmt = $pdo->prepare("UPDATE `ORDER` SET Order_date=?, Ordered_weight=?, Batch_ID=?, Customer_ID=?, Retailer_ID=? WHERE OrderID=?");
        $stmt->execute([$order_date, $ordered_weight, $batch_id, $customer_id, $retailer_id, $edit_id]);
        $msg = "Order updated!";
    } else {
        // ADD
        $stmt = $pdo->prepare("INSERT INTO `ORDER` (Order_date, Ordered_weight, Batch_ID, Customer_ID, Retailer_ID) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$order_date, $ordered_weight, $batch_id, $customer_id, $retailer_id]);
        $msg = "Order added!";
    }
}

// --- EDIT MODE ---
$edit_order = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM `ORDER` WHERE OrderID=?");
    $stmt->execute([$edit_id]);
    $edit_order = $stmt->fetch();
}

// --- FETCH ALL ORDERS ---
$orders = $pdo->query("
    SELECT o.*, b.Batch_ID, c.Customer_Name, r.Retailer_Name
    FROM `ORDER` o
    LEFT JOIN PACKAGING_BATCH b ON o.Batch_ID = b.Batch_ID
    LEFT JOIN CUSTOMER c ON o.Customer_ID = c.CustomerID
    LEFT JOIN RETAILER r ON o.Retailer_ID = r.Retailer_ID
    ORDER BY o.OrderID DESC
")->fetchAll();
?>

<div class="container mt-4">
    <h2>Order Management</h2>
    <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <!-- ADD/EDIT FORM -->
    <div class="card mb-4">
        <div class="card-header"><?= $edit_order ? "Edit Order" : "Add New Order" ?></div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Order Date</label>
                    <input type="date" name="Order_date" class="form-control" value="<?= $edit_order['Order_date'] ?? "" ?>" required>
                </div>
                <div class="mb-3">
                    <label>Ordered Weight</label>
                    <input type="number" step="0.01" name="Ordered_weight" class="form-control" value="<?= $edit_order['Ordered_weight'] ?? "" ?>" min="0.01" required>
                </div>
                <div class="mb-3">
                    <label>Batch</label>
                    <select name="Batch_ID" class="form-control" required>
                        <option value="">Select Batch</option>
                        <?php foreach($batches as $b): ?>
                            <option value="<?= $b['Batch_ID'] ?>" <?= (isset($edit_order) && $edit_order['Batch_ID'] == $b['Batch_ID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($b['Batch_ID']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Customer</label>
                    <select name="Customer_ID" class="form-control">
                        <option value="">Select Customer (if any)</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?= $c['CustomerID'] ?>" <?= (isset($edit_order) && $edit_order['Customer_ID'] == $c['CustomerID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($c['Customer_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Retailer</label>
                    <select name="Retailer_ID" class="form-control">
                        <option value="">Select Retailer (if any)</option>
                        <?php foreach($retailers as $r): ?>
                            <option value="<?= $r['Retailer_ID'] ?>" <?= (isset($edit_order) && $edit_order['Retailer_ID'] == $r['Retailer_ID']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($r['Retailer_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($edit_order): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_order['OrderID'] ?>">
                <?php endif; ?>
                <button class="btn btn-success"><?= $edit_order ? "Update" : "Add" ?> Order</button>
                <?php if ($edit_order): ?>
                    <a href="manage_orders.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- TABLE OF ALL ORDERS -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order Date</th>
                <th>Weight</th>
                <th>Batch</th>
                <th>Customer</th>
                <th>Retailer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($orders as $o): ?>
            <tr>
                <td><?= htmlspecialchars($o['OrderID']) ?></td>
                <td><?= htmlspecialchars($o['Order_date']) ?></td>
                <td><?= htmlspecialchars($o['Ordered_weight']) ?></td>
                <td><?= htmlspecialchars($o['Batch_ID']) ?></td>
                <td><?= htmlspecialchars($o['Customer_Name']) ?></td>
                <td><?= htmlspecialchars($o['Retailer_Name']) ?></td>
                <td>
                    <a href="manage_orders.php?action=edit&id=<?= $o['OrderID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_orders.php?action=delete&id=<?= $o['OrderID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("../templates/footer.php"); ?>
