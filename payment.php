<?php
require_once 'includes/header.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$services = $pdo->query("SELECT * FROM services ORDER BY id ASC")->fetchAll();
$selected_service_id = $_GET['service_id'] ?? '';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_service'])) {
    $service_id = $_POST['service_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $address = trim($_POST['address'] ?? '');
    
    // Validations & pseudo booking logic
    if (empty($service_id) || empty($date) || empty($address)) {
        $error = "Please fill in all booking details.";
    } else {
        // pseudo commit
        $success = "Your service has been successfully booked for " . htmlspecialchars(date('M j, Y h:i A', strtotime($date))) . "! Our team will contact you shortly.";
    }
}
?>

<style>
/* Checkout page styles reused */
.payment-wrapper { max-width: 960px; margin: 48px auto; padding: 0 16px 60px; }
.payment-wrapper h1 { font-size: 2rem; font-weight: 700; margin-bottom: 32px; color: #1a1a1a; }
.payment-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }
@media (max-width: 768px) { .payment-grid { grid-template-columns: 1fr; } }
.co-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 14px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,.07); }
.co-card h5 { font-size: 1rem; font-weight: 700; color: #2d6a4f; margin-bottom: 18px; text-transform: uppercase; letter-spacing: .04em; }
.co-label { font-size: .82rem; font-weight: 600; color: #555; margin-bottom: 5px; display: block; }
.co-input { width: 100%; border: 1px solid #d4d4d4; border-radius: 8px; padding: 10px 13px; font-size: .95rem; color: #1a1a1a; outline: none; transition: border .2s; box-sizing: border-box; background: #fafafa; }
.co-input:focus { border-color: #40916c; background: #fff; }
.co-section { margin-bottom: 22px; }
.btn-pay { width: 100%; background: linear-gradient(135deg, #40916c, #2d6a4f); color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 1rem; font-weight: 700; cursor: pointer; margin-top: 20px; transition: opacity .2s, transform .15s; }
.btn-pay:hover { opacity: .92; transform: translateY(-1px); }
.co-alert-success { background: #d8f3dc; border: 1px solid #74c69d; color: #1b4332; border-radius: 12px; padding: 28px 32px; text-align: center; }
.co-alert-danger { background: #ffe3e3; border: 1px solid #f08080; color: #7f1d1d; border-radius: 10px; padding: 14px 18px; margin-bottom: 18px; font-size: .95rem; }
.co-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.card-icons { display: flex; gap: 8px; margin-bottom: 16px; }
.card-icon { background: #f1f1f1; border-radius: 6px; padding: 4px 10px; font-size: .75rem; font-weight: 700; color: #444; }
.co-divider { height: 1px; background: #ececec; margin: 4px 0 20px; }
</style>

<div class="payment-wrapper">
    <h1>Book a Service</h1>
    
    <?php if ($success): ?>
        <div class="co-alert-success">
            <div style="font-size:2.5rem;margin-bottom:8px;">✅</div>
            <div style="font-size:1.15rem;font-weight:700;margin-bottom:6px;">Booking Confirmed!</div>
            <div style="font-size:.97rem;"><?php echo $success; ?></div>
            <a href="services.php" class="btn btn-success mt-4">Back to Services</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="co-alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form action="payment.php" method="POST">
            <div class="payment-grid">
                
                <!-- Booking Details -->
                <div class="co-card" style="margin-bottom:24px;">
                    <h5>📅 Booking Details</h5>
                    
                    <div class="co-section">
                        <label class="co-label" for="service_id">Select Service *</label>
                        <select class="co-input" id="service_id" name="service_id" required>
                            <option value="">-- Choose a Service --</option>
                            <?php foreach ($services as $srv): ?>
                                <option value="<?php echo $srv['id']; ?>" <?php echo (strval($selected_service_id) === strval($srv['id'])) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($srv['name']) . " (from LKR " . number_format($srv['price'], 2) . ")"; ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="custom" <?php echo ($selected_service_id === 'custom') ? 'selected' : ''; ?>>Need a Custom Service? (Free Consultation)</option>
                        </select>
                    </div>
                    
                    <div class="co-section">
                        <label class="co-label" for="date">Preferred Date & Time *</label>
                        <input type="datetime-local" class="co-input" id="date" name="date" required>
                    </div>
                    
                    <div class="co-section" style="margin-bottom:0;">
                        <label class="co-label" for="address">Service Address *</label>
                        <textarea class="co-input" id="address" name="address" rows="3" placeholder="Street, City, State, ZIP" required></textarea>
                    </div>
                </div>
                
                <!-- Payment Details -->
                <div class="co-card">
                    <h5>💳 Payment Information</h5>
                    
                    <div class="card-icons">
                        <span class="card-icon">VISA</span>
                        <span class="card-icon">MC</span>
                        <span class="card-icon">AMEX</span>
                    </div>
                    <div class="co-divider"></div>

                    <div class="co-section">
                        <label class="co-label" for="card_name">Name on Card *</label>
                        <input type="text" class="co-input" id="card_name" placeholder="John Smith" required>
                    </div>

                    <div class="co-section">
                        <label class="co-label" for="card_number">Card Number *</label>
                        <input type="text" class="co-input" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                    </div>

                    <div class="co-row co-section" style="margin-bottom:0;">
                        <div>
                            <label class="co-label" for="card_expiry">Expiry *</label>
                            <input type="text" class="co-input" id="card_expiry" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div>
                            <label class="co-label" for="card_cvv">CVV *</label>
                            <input type="password" class="co-input" id="card_cvv" placeholder="•••" maxlength="4" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="book_service" class="btn-pay">Confirm Booking</button>
                    
                    <div style="text-align:center;margin-top:14px;">
                        <a href="services.php" style="font-size:.85rem;color:#888;text-decoration:none;">← Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.getElementById('card_number')?.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '').substring(0, 16);
    e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
});
document.getElementById('card_expiry')?.addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 3) v = v.substring(0,2) + '/' + v.substring(2);
    e.target.value = v;
});
</script>

<?php require_once 'includes/footer.php'; ?>
