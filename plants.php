<?php
require_once 'includes/header.php';

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$query = "SELECT * FROM plants WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}
if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$plants = $stmt->fetchAll();

// Get categories for filter
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM plants");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Our Plants</h1>
    
    <!-- Search and Filter Form -->
    <div class="row mb-4 justify-content-center">
        <div class="col-md-8">
            <form action="plants.php" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search plants..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="category" class="form-select w-auto">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-success">Search</button>
                <a href="plants.php" class="btn btn-outline-secondary">Clear</a>
            </form>
        </div>
    </div>

    <!-- Plants Grid -->
    <div class="row">
        <?php if (count($plants) > 0): ?>
            <?php foreach ($plants as $plant): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="assets/images/<?php echo htmlspecialchars($plant['image']); ?>" class="card-img-top plant-img" alt="<?php echo htmlspecialchars($plant['name']); ?>" onerror="this.src='https://via.placeholder.com/400x300?text=Plant'">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($plant['name']); ?></h5>
                            <p class="card-text text-muted mb-1"><small><?php echo htmlspecialchars($plant['category']); ?></small></p>
                            <p class="card-text fw-bold text-success">$<?php echo number_format($plant['price'], 2); ?></p>
                            <a href="plant-details.php?id=<?php echo $plant['id']; ?>" class="btn btn-outline-success btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">No plants found matching your criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
