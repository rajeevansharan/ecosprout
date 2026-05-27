<?php
require_once 'includes/header.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Determine selected items to purchase
$selected_items = [];
if (!empty($_SESSION['cart']) && !empty($_SESSION['selected_cart'])) {
    foreach ($_SESSION['cart'] as $p_id => $qty) {
        if (isset($_SESSION['selected_cart'][$p_id])) {
            $selected_items[$p_id] = $qty;
        }
    }
}

// Cart/Selection must not be empty
if (empty($selected_items)) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    try {
        $pdo->beginTransaction();
        
        // Calculate total
        $ids = implode(',', array_keys($selected_items));
        $stmt = $pdo->query("SELECT id, price, stock FROM plants WHERE id IN ($ids)");
        $plants_data = [];
        $total_amount = 0;
        
        while ($row = $stmt->fetch()) {
            $qty = $selected_items[$row['id']];
            if ($qty > $row['stock']) {
                throw new Exception("Not enough stock for plant ID " . $row['id']);
            }
            $plants_data[$row['id']] = $row;
            $total_amount += $row['price'] * $qty;
        }
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$user_id, $total_amount]);
        $order_id = $pdo->lastInsertId();
        
        // Insert order items and update stock
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, plant_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock = $pdo->prepare("UPDATE plants SET stock = stock - ? WHERE id = ?");
        
        foreach ($selected_items as $p_id => $qty) {
            $price = $plants_data[$p_id]['price'];
            $stmt_item->execute([$order_id, $p_id, $qty, $price]);
            $stmt_stock->execute([$qty, $p_id]);
            
            // Remove ONLY purchased items from cart and selection
            unset($_SESSION['cart'][$p_id]);
            unset($_SESSION['selected_cart'][$p_id]);
        }
        
        $pdo->commit();
        $success = "Order placed successfully! Your Order ID is #$order_id.";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to place order: " . $e->getMessage();
    }
}
?>

<div class="container my-5 text-center">
    <?php if ($success): ?>
        <div class="alert alert-success fs-4 p-5">
            <?php echo $success; ?>
            <br>
            <a href="plants.php" class="btn btn-success mt-3">Continue Shopping</a>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <a href="cart.php" class="btn btn-secondary mt-3">Back to Cart</a>
    <?php else: ?>
        <h1 class="mb-4">Checkout</h1>
        <div class="card mx-auto shadow" style="max-width: 500px;">
            <div class="card-body p-4">
                <p class="lead">You are about to place an order.</p>
                <form action="checkout.php" method="POST">
                    <button type="submit" name="place_order" class="btn btn-success btn-lg w-100">Confirm & Place Order</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
