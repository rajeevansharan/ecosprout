<?php
require_once 'header.php';

// Handle Add/Edit
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $stock = $_POST['stock'];
        $desc = $_POST['description'];
        $care = $_POST['care_instructions'];
        
        $stmt = $pdo->prepare("INSERT INTO plants (name, description, price, category, care_instructions, stock) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $desc, $price, $category, $care, $stock])) {
            $message = "<div class='alert alert-success'>Plant added successfully.</div>";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['plant_id'];
        $stmt = $pdo->prepare("DELETE FROM plants WHERE id = ?");
        $stmt->execute([$id]);
        $message = "<div class='alert alert-success'>Plant deleted.</div>";
    }
}

$plants = $pdo->query("SELECT * FROM plants ORDER BY id DESC")->fetchAll();
?>

<h1 class="mb-4">Manage Plants</h1>
<?php echo $message; ?>

<div class="card mb-4">
    <div class="card-header bg-success text-white">Add New Plant</div>
    <div class="card-body">
        <form action="plants.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Care Instructions</label>
                    <textarea name="care_instructions" class="form-control" rows="2" required></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Add Plant</button>
        </form>
    </div>
</div>

<h3>Plant List</h3>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plants as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['category']); ?></td>
                    <td>$<?php echo number_format($p['price'], 2); ?></td>
                    <td><?php echo $p['stock']; ?></td>
                    <td>
                        <form action="plants.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this plant?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="plant_id" value="<?php echo $p['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
