<?php
require_once 'header.php';

// Quick stats
$total_plants = $pdo->query("SELECT COUNT(*) FROM plants")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
$total_inquiries = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
?>

<h1 class="mb-4">Dashboard</h1>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Total Plants</h5>
                <h2 class="display-4"><?php echo $total_plants; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Orders</h5>
                <h2 class="display-4"><?php echo $total_orders; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Customers</h5>
                <h2 class="display-4"><?php echo $total_users; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Inquiries</h5>
                <h2 class="display-4"><?php echo $total_inquiries; ?></h2>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
