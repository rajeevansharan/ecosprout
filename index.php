<?php
require_once 'includes/header.php';
// Fetch a few featured plants
$stmt = $pdo->query("SELECT * FROM plants LIMIT 3");
$featured_plants = $stmt->fetchAll();
?>

<div class="hero-section">
    <div class="container">
        <h1 class="display-3 fw-bold">Welcome to EcoSprout</h1>
        <p class="lead">Your one-stop destination for beautiful plants, expert gardening services, and more.</p>
        <a href="plants.php" class="btn btn-success btn-lg mt-3">Shop Now</a>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center mb-4">Featured Plants</h2>
    <div class="row">
        <?php foreach ($featured_plants as $plant): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="assets/images/<?php echo htmlspecialchars($plant['image']); ?>" class="card-img-top plant-img" alt="<?php echo htmlspecialchars($plant['name']); ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($plant['name']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($plant['category']); ?></p>
                        <p class="card-text fw-bold text-success">$<?php echo number_format($plant['price'], 2); ?></p>
                        <a href="plant-details.php?id=<?php echo $plant['id']; ?>" class="btn btn-outline-success">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
        <a href="plants.php" class="btn btn-success">View All Plants</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
