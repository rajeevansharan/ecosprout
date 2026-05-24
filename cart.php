<?php
require_once 'includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' && isset($_POST['plant_id']) && isset($_POST['quantity'])) {
        $plant_id = (int)$_POST['plant_id'];
        $quantity = (int)$_POST['quantity'];
        
        if (isset($_SESSION['cart'][$plant_id])) {
            $_SESSION['cart'][$plant_id] += $quantity;
        } else {
            $_SESSION['cart'][$plant_id] = $quantity;
        }
        header("Location: cart.php");
        exit;
    } elseif ($action === 'remove' && isset($_POST['plant_id'])) {
        $plant_id = (int)$_POST['plant_id'];
        unset($_SESSION['cart'][$plant_id]);
        header("Location: cart.php");
        exit;
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        header("Location: cart.php");
        exit;
    }
}

// Fetch cart items from DB
$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT id, name, price, image FROM plants WHERE id IN ($ids)");
    while ($row = $stmt->fetch()) {
        $qty = $_SESSION['cart'][$row['id']];
        $subtotal = $row['price'] * $qty;
        $total_price += $subtotal;
        
        $row['quantity'] = $qty;
        $row['subtotal'] = $subtotal;
        $cart_items[] = $row;
    }
}
?>

<div class="container my-5">
    <h1 class="mb-4">Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">Your cart is empty. <a href="plants.php">Browse plants</a></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" width="50" height="50" class="me-2 rounded object-fit-cover" onerror="this.src='https://via.placeholder.com/50'">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                            <td>
                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="plant_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total:</td>
                        <td colspan="2" class="fw-bold text-success fs-5">$<?php echo number_format($total_price, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <form action="cart.php" method="POST">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="btn btn-outline-danger">Clear Cart</button>
            </form>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-warning btn-lg">Login to Checkout</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
