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
$error   = '';

// --- Pre-fetch plant data for order summary display ---
$ids          = implode(',', array_keys($selected_items));
$stmt_display = $pdo->query("SELECT id, name, price FROM plants WHERE id IN ($ids)");
$plants_display = [];
$display_total  = 0;
while ($row = $stmt_display->fetch()) {
    $qty = $selected_items[$row['id']];
    $plants_display[$row['id']] = $row;
    $display_total += $row['price'] * $qty;
}

// --- Handle POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {

    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $card_number      = trim($_POST['card_number'] ?? '');
    $card_name        = trim($_POST['card_name'] ?? '');
    $card_expiry      = trim($_POST['card_expiry'] ?? '');
    $card_cvv         = trim($_POST['card_cvv'] ?? '');

    // Validation
    if (empty($shipping_address) || empty($phone)) {
        $error = "Please fill in your delivery address and phone number.";
    } elseif (!preg_match('/^\+?[0-9\s\-]{7,15}$/', $phone)) {
        $error = "Please enter a valid phone number.";
    } elseif (empty($card_number) || empty($card_name) || empty($card_expiry) || empty($card_cvv)) {
        $error = "Please fill in all payment details.";
    } else {
        try {
            $pdo->beginTransaction();

            // Recalculate total from DB
            $stmt    = $pdo->query("SELECT id, price, stock FROM plants WHERE id IN ($ids)");
            $plants_data  = [];
            $total_amount = 0;

            while ($row = $stmt->fetch()) {
                $qty = $selected_items[$row['id']];
                if ($qty > $row['stock']) {
                    throw new Exception("Not enough stock for: " . htmlspecialchars($row['name']));
                }
                $plants_data[$row['id']] = $row;
                $total_amount += $row['price'] * $qty;
            }

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address, phone) VALUES (?, ?, 'completed', ?, ?)");
            $stmt->execute([$user_id, $total_amount, $shipping_address, $phone]);
            $order_id = $pdo->lastInsertId();

            // Insert order items and update stock
            $stmt_item  = $pdo->prepare("INSERT INTO order_items (order_id, plant_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt_stock = $pdo->prepare("UPDATE plants SET stock = stock - ? WHERE id = ?");

            foreach ($selected_items as $p_id => $qty) {
                $price = $plants_data[$p_id]['price'];
                $stmt_item->execute([$order_id, $p_id, $qty, $price]);
                $stmt_stock->execute([$qty, $p_id]);

                unset($_SESSION['cart'][$p_id]);
                unset($_SESSION['selected_cart'][$p_id]);
            }

            $pdo->commit();
            $success = "Payment successful! Your Order ID is <strong>#$order_id</strong>. Thank you for shopping with EcoSprout 🌱";

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to place order: " . $e->getMessage();
        }
    }
}
?>

<style>
/* ── Checkout page styles ── */
.checkout-wrapper {
    max-width: 960px;
    margin: 48px auto;
    padding: 0 16px 60px;
}
.checkout-wrapper h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 32px;
    color: #1a1a1a;
}
.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 28px;
}
@media (max-width: 768px) {
    .checkout-grid { grid-template-columns: 1fr; }
}
.co-card {
    background: #fff;
    border: 1px solid #e4e4e4;
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
}
.co-card h5 {
    font-size: 1rem;
    font-weight: 700;
    color: #2d6a4f;
    margin-bottom: 18px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
/* Order summary */
.order-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .95rem;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}
.order-line:last-child { border-bottom: none; }
.order-total {
    display: flex;
    justify-content: space-between;
    font-weight: 700;
    font-size: 1.1rem;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 2px solid #e4e4e4;
    color: #1a1a1a;
}
/* Form */
.co-label {
    font-size: .82rem;
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
    display: block;
}
.co-input {
    width: 100%;
    border: 1px solid #d4d4d4;
    border-radius: 8px;
    padding: 10px 13px;
    font-size: .95rem;
    color: #1a1a1a;
    outline: none;
    transition: border .2s;
    box-sizing: border-box;
    background: #fafafa;
}
.co-input:focus { border-color: #40916c; background: #fff; }
.co-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.co-section { margin-bottom: 22px; }
.co-divider {
    height: 1px; background: #ececec;
    margin: 4px 0 20px;
}
/* Card icons row */
.card-icons { display: flex; gap: 8px; margin-bottom: 16px; }
.card-icon {
    background: #f1f1f1;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: .75rem;
    font-weight: 700;
    color: #444;
    letter-spacing: .03em;
}
/* Pay button */
.btn-pay {
    width: 100%;
    background: linear-gradient(135deg, #40916c, #2d6a4f);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 14px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    margin-top: 20px;
    transition: opacity .2s, transform .15s;
    letter-spacing: .02em;
}
.btn-pay:hover { opacity: .92; transform: translateY(-1px); }
.secure-note {
    text-align: center;
    font-size: .78rem;
    color: #888;
    margin-top: 10px;
}
.secure-note svg { vertical-align: middle; margin-right: 4px; }
/* Alert overrides */
.co-alert-success {
    background: #d8f3dc;
    border: 1px solid #74c69d;
    color: #1b4332;
    border-radius: 12px;
    padding: 28px 32px;
    text-align: center;
}
.co-alert-danger {
    background: #ffe3e3;
    border: 1px solid #f08080;
    color: #7f1d1d;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 18px;
    font-size: .95rem;
}
</style>

<div class="checkout-wrapper">
    <h1>Checkout</h1>

    <?php if ($success): ?>
        <div class="co-alert-success">
            <div style="font-size:2.5rem;margin-bottom:8px;">✅</div>
            <div style="font-size:1.15rem;font-weight:700;margin-bottom:6px;">Payment Successful!</div>
            <div style="font-size:.97rem;"><?php echo $success; ?></div>
            <a href="plants.php" class="btn btn-success mt-4">Continue Shopping</a>
        </div>
    <?php else: ?>

        <?php if ($error): ?>
            <div class="co-alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" id="checkoutForm">
            <div class="checkout-grid">

                <!-- LEFT COLUMN: Delivery + Payment -->
                <div>
                    <!-- Delivery Details -->
                    <div class="co-card" style="margin-bottom:24px;">
                        <h5>🚚 Delivery Details</h5>

                        <div class="co-section">
                            <label class="co-label" for="shipping_address">Shipping Address *</label>
                            <textarea
                                class="co-input"
                                id="shipping_address"
                                name="shipping_address"
                                rows="3"
                                placeholder="Street, City, State, ZIP / Postal Code"
                                required><?php echo htmlspecialchars($_POST['shipping_address'] ?? ''); ?></textarea>
                        </div>

                        <div class="co-section" style="margin-bottom:0;">
                            <label class="co-label" for="phone">Phone Number *</label>
                            <input
                                type="tel"
                                class="co-input"
                                id="phone"
                                name="phone"
                                placeholder="+1 555 000 0000"
                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                required>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="co-card">
                        <h5>💳 Payment Details</h5>

                        <div class="card-icons">
                            <span class="card-icon">VISA</span>
                            <span class="card-icon">MC</span>
                            <span class="card-icon">AMEX</span>
                        </div>
                        <div class="co-divider"></div>

                        <div class="co-section">
                            <label class="co-label" for="card_number">Card Number *</label>
                            <input
                                type="text"
                                class="co-input"
                                id="card_number"
                                name="card_number"
                                placeholder="1234 5678 9012 3456"
                                maxlength="19"
                                autocomplete="cc-number"
                                required>
                        </div>

                        <div class="co-section">
                            <label class="co-label" for="card_name">Name on Card *</label>
                            <input
                                type="text"
                                class="co-input"
                                id="card_name"
                                name="card_name"
                                placeholder="John Smith"
                                value="<?php echo htmlspecialchars($_POST['card_name'] ?? ''); ?>"
                                autocomplete="cc-name"
                                required>
                        </div>

                        <div class="co-row co-section" style="margin-bottom:0;">
                            <div>
                                <label class="co-label" for="card_expiry">Expiry Date *</label>
                                <input
                                    type="text"
                                    class="co-input"
                                    id="card_expiry"
                                    name="card_expiry"
                                    placeholder="MM / YY"
                                    maxlength="7"
                                    autocomplete="cc-exp"
                                    value="<?php echo htmlspecialchars($_POST['card_expiry'] ?? ''); ?>"
                                    required>
                            </div>
                            <div>
                                <label class="co-label" for="card_cvv">CVV *</label>
                                <input
                                    type="password"
                                    class="co-input"
                                    id="card_cvv"
                                    name="card_cvv"
                                    placeholder="•••"
                                    maxlength="4"
                                    autocomplete="cc-csc"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Order Summary -->
                <div>
                    <div class="co-card" style="position:sticky;top:20px;">
                        <h5>🧾 Order Summary</h5>

                        <?php foreach ($plants_display as $p_id => $plant):
                            $qty = $selected_items[$p_id];
                            $subtotal = $plant['price'] * $qty;
                        ?>
                        <div class="order-line">
                            <span><?php echo htmlspecialchars($plant['name']); ?>
                                <span style="color:#888;font-size:.85rem;"> × <?php echo $qty; ?></span>
                            </span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <?php endforeach; ?>

                        <div class="order-line" style="border-bottom:none;margin-top:4px;">
                            <span style="color:#555;">Shipping</span>
                            <span style="color:#2d6a4f;font-weight:600;">Free</span>
                        </div>

                        <div class="order-total">
                            <span>Total</span>
                            <span>$<?php echo number_format($display_total, 2); ?></span>
                        </div>

                        <button type="submit" name="place_order" class="btn-pay">
                            Pay $<?php echo number_format($display_total, 2); ?> →
                        </button>

                        <p class="secure-note">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#888"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                            Secure 256-bit SSL encrypted payment
                        </p>

                        <div style="text-align:center;margin-top:14px;">
                            <a href="cart.php" style="font-size:.85rem;color:#888;text-decoration:none;">← Back to Cart</a>
                        </div>
                    </div>
                </div>

            </div><!-- /checkout-grid -->
        </form>

    <?php endif; ?>
</div>

<script>
// Auto-format card number with spaces
document.getElementById('card_number').addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '').substring(0, 16);
    e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
});
// Auto-format expiry MM / YY
document.getElementById('card_expiry').addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 3) v = v.substring(0,2) + ' / ' + v.substring(2);
    e.target.value = v;
});
</script>

<?php require_once 'includes/footer.php'; ?>
