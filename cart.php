<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['selected_cart'])) {
    $_SESSION['selected_cart'] = [];
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
        $_SESSION['selected_cart'][$plant_id] = true;
        $redirect = $_POST['redirect'] ?? 'cart.php';
        header("Location: " . $redirect);
        exit;
    } elseif ($action === 'remove' && isset($_POST['plant_id'])) {
        $plant_id = (int)$_POST['plant_id'];
        unset($_SESSION['cart'][$plant_id]);
        unset($_SESSION['selected_cart'][$plant_id]);
        $redirect = $_POST['redirect'] ?? 'cart.php';
        header("Location: " . $redirect);
        exit;
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        $_SESSION['selected_cart'] = [];
        header("Location: cart.php");
        exit;
    } elseif ($action === 'toggle_select' && isset($_POST['plant_id'])) {
        $plant_id = (int)$_POST['plant_id'];
        $selected = (isset($_POST['selected']) && $_POST['selected'] == 1) ? true : false;
        
        if ($selected) {
            $_SESSION['selected_cart'][$plant_id] = true;
        } else {
            unset($_SESSION['selected_cart'][$plant_id]);
        }
        
        header('Content-Type: application/json');
        $total_price = 0;
        if (!empty($_SESSION['cart'])) {
            $ids = implode(',', array_keys($_SESSION['cart']));
            $stmt = $pdo->query("SELECT id, price FROM plants WHERE id IN ($ids)");
            while ($row = $stmt->fetch()) {
                if (isset($_SESSION['selected_cart'][$row['id']])) {
                    $total_price += $row['price'] * $_SESSION['cart'][$row['id']];
                }
            }
        }
        echo json_encode([
            'status' => 'success',
            'total_price' => number_format($total_price, 2)
        ]);
        exit;
    }
}

require_once 'includes/header.php';

// Fetch cart items from DB
$cart_items = [];
$total_price = 0;
$has_selected = false;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT id, name, price, image FROM plants WHERE id IN ($ids)");
    while ($row = $stmt->fetch()) {
        $qty = $_SESSION['cart'][$row['id']];
        $subtotal = $row['price'] * $qty;
        
        $is_selected = isset($_SESSION['selected_cart'][$row['id']]);
        if ($is_selected) {
            $total_price += $subtotal;
            $has_selected = true;
        }
        
        $row['quantity'] = $qty;
        $row['subtotal'] = $subtotal;
        $row['selected'] = $is_selected;
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
                        <th style="width: 80px;" class="text-center">Select</th>
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
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input select-item" data-id="<?php echo $item['id']; ?>" <?php echo $item['selected'] ? 'checked' : ''; ?> style="width: 1.3rem; height: 1.3rem; cursor: pointer;">
                            </td>
                            <td>
                                <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" width="50" height="50" class="me-2 rounded object-fit-cover" onerror="this.src='https://via.placeholder.com/50'">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td>LKR <?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>LKR <?php echo number_format($item['subtotal'], 2); ?></td>
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
                        <td colspan="4" class="text-end fw-bold">Total:</td>
                        <td colspan="2" class="fw-bold text-success fs-5" id="cart-total-price">LKR <?php echo number_format($total_price, 2); ?></td>
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
                <a href="checkout.php" class="btn btn-success btn-lg checkout-btn <?php echo !$has_selected ? 'disabled' : ''; ?>">Proceed to Checkout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-warning btn-lg checkout-btn <?php echo !$has_selected ? 'disabled' : ''; ?>">Login to Checkout</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.select-item').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const plantId = this.getAttribute('data-id');
        const isChecked = this.checked ? 1 : 0;
        
        const formData = new FormData();
        formData.append('action', 'toggle_select');
        formData.append('plant_id', plantId);
        formData.append('selected', isChecked);
        
        fetch('cart.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('cart-total-price').textContent = 'LKR ' + data.total_price;
                const anyChecked = Array.from(document.querySelectorAll('.select-item')).some(cb => cb.checked);
                document.querySelectorAll('.checkout-btn').forEach(btn => {
                    if (anyChecked) {
                        btn.classList.remove('disabled');
                    } else {
                        btn.classList.add('disabled');
                    }
                });
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
