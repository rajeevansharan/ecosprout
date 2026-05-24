<?php
require_once 'header.php';

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    echo "<div class='alert alert-success'>Order status updated.</div>";
}

$orders = $pdo->query("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.id DESC
")->fetchAll();
?>

<h1 class="mb-4">View Orders</h1>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?php echo $o['id']; ?></td>
                    <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                    <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($o['created_at'])); ?></td>
                    <td>
                        <?php 
                        $badge = 'bg-secondary';
                        if($o['status'] == 'completed') $badge = 'bg-success';
                        elseif($o['status'] == 'pending') $badge = 'bg-warning text-dark';
                        elseif($o['status'] == 'cancelled') $badge = 'bg-danger';
                        ?>
                        <span class="badge <?php echo $badge; ?>"><?php echo ucfirst($o['status']); ?></span>
                    </td>
                    <td>
                        <form action="orders.php" method="POST" class="d-flex gap-2">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <select name="status" class="form-select form-select-sm" style="width:auto;">
                                <option value="pending" <?php echo $o['status']=='pending'?'selected':'';?>>Pending</option>
                                <option value="completed" <?php echo $o['status']=='completed'?'selected':'';?>>Completed</option>
                                <option value="cancelled" <?php echo $o['status']=='cancelled'?'selected':'';?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($orders)): ?>
                <tr><td colspan="6" class="text-center">No orders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
