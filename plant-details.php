<?php
require_once 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: plants.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM plants WHERE id = ?");
$stmt->execute([$id]);
$plant = $stmt->fetch();

if (!$plant) {
    echo "<div class='container my-5'><h2>Plant not found.</h2><a href='plants.php'>Go back</a></div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container my-5">
    <div class="row">
        <!-- Plant Image -->
        <div class="col-md-6 mb-4">
            <img src="assets/images/<?php echo htmlspecialchars($plant['image']); ?>" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($plant['name']); ?>">
        </div>
        
        <!-- Plant Details -->
        <div class="col-md-6">
            <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($plant['name']); ?></h1>
            <p class="text-muted fs-5"><?php echo htmlspecialchars($plant['category']); ?></p>
            <h2 class="text-success mb-4">$<?php echo number_format($plant['price'], 2); ?></h2>
            
            <p class="lead"><?php echo nl2br(htmlspecialchars($plant['description'])); ?></p>
            
            <div class="card bg-light mb-4 border-0">
                <div class="card-body">
                    <h5 class="card-title">Care Instructions:</h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($plant['care_instructions'])); ?></p>
                </div>
            </div>
            
            <p><strong>Stock Available:</strong> <?php echo $plant['stock']; ?></p>
            
            <?php if ($plant['stock'] > 0): ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['cart'][$plant['id']])): ?>
                        <form action="cart.php" method="POST" class="d-flex align-items-center gap-3">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="plant_id" value="<?php echo $plant['id']; ?>">
                            <input type="hidden" name="redirect" value="plant-details.php?id=<?php echo $plant['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-lg">Remove</button>
                        </form>
                    <?php else: ?>
                        <form action="cart.php" method="POST" class="d-flex align-items-center gap-3">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="plant_id" value="<?php echo $plant['id']; ?>">
                            <input type="hidden" name="redirect" value="plant-details.php?id=<?php echo $plant['id']; ?>">
                            <div class="input-group w-auto">
                                <span class="input-group-text">Qty</span>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $plant['stock']; ?>" style="width: 80px;">
                            </div>
                            <button type="submit" class="btn btn-success btn-lg">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-danger">Out of Stock</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
