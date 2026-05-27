<?php
require_once 'header.php';

// Fetch orders stats
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?? 0.00;
$pending_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'pending'")->fetchColumn() ?? 0.00;
$completed_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

// Fetch top selling plants
$top_plants = $pdo->query("
    SELECT p.name, p.botanical_name, SUM(oi.quantity) as total_qty, SUM(oi.quantity * oi.price) as total_sales
    FROM order_items oi
    JOIN plants p ON oi.plant_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'completed'
    GROUP BY p.id
    ORDER BY total_qty DESC
    LIMIT 5
")->fetchAll();

// Fetch orders list over time
$recent_orders = $pdo->query("
    SELECT o.id, u.name as customer, o.total_amount, o.status, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
")->fetchAll();
?>

<h1 class="mb-4">Sales & Analytical Reports</h1>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm text-center border-0 bg-success text-white">
            <div class="card-body py-4">
                <h6 class="text-uppercase tracking-wider opacity-75">Completed Revenue</h6>
                <h2 class="display-5 fw-bold">$<?php echo number_format($total_revenue, 2); ?></h2>
                <small><?php echo $completed_orders; ?> Completed Orders</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center border-0 bg-warning text-dark">
            <div class="card-body py-4">
                <h6 class="text-uppercase tracking-wider opacity-75">Pending Revenue</h6>
                <h2 class="display-5 fw-bold">$<?php echo number_format($pending_revenue, 2); ?></h2>
                <small><?php echo $pending_orders; ?> Pending Orders</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center border-0 bg-primary text-white">
            <div class="card-body py-4">
                <h6 class="text-uppercase tracking-wider opacity-75">Total Orders</h6>
                <h2 class="display-5 fw-bold"><?php echo ($completed_orders + $pending_orders); ?></h2>
                <small>Across all customer types</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center border-0 bg-info text-white">
            <div class="card-body py-4">
                <h6 class="text-uppercase tracking-wider opacity-75">Average Order Value</h6>
                <h2 class="display-5 fw-bold">
                    $<?php 
                    $total_count = $completed_orders + $pending_orders;
                    $total_rev = $total_revenue + $pending_revenue;
                    echo number_format($total_count > 0 ? $total_rev / $total_count : 0, 2); 
                    ?>
                </h2>
                <small>Average transaction size</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-dark text-white fw-bold py-3">🔥 Top Selling Plants</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Plant Name</th>
                                <th>Botanical Name</th>
                                <th class="text-center">Qty Sold</th>
                                <th class="text-end">Revenue Generated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($top_plants)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No completed sales recorded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($top_plants as $tp): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($tp['name']); ?></strong></td>
                                        <td><em><?php echo htmlspecialchars($tp['botanical_name'] ?? 'N/A'); ?></em></td>
                                        <td class="text-center fw-bold text-success"><?php echo $tp['total_qty']; ?></td>
                                        <td class="text-end fw-bold">$<?php echo number_format($tp['total_sales'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-dark text-white fw-bold py-3">🕒 Recent Orders Overview</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_orders)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No orders found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_orders as $ro): ?>
                                    <tr>
                                        <td>#<?php echo $ro['id']; ?></td>
                                        <td><?php echo htmlspecialchars($ro['customer']); ?></td>
                                        <td class="fw-bold">$<?php echo number_format($ro['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                echo $ro['status'] == 'completed' ? 'bg-success' : ($ro['status'] == 'cancelled' ? 'bg-danger' : 'bg-warning text-dark'); 
                                            ?>"><?php echo ucfirst($ro['status']); ?></span>
                                        </td>
                                        <td><?php echo date('M d, H:i', strtotime($ro['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
