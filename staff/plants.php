<?php
require_once 'header.php';

// Handle Add/Edit/Delete
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $name = trim($_POST['name']);
        $botanical = trim($_POST['botanical_name']);
        $price = (float)$_POST['price'];
        $category = trim($_POST['category']);
        $stock = (int)$_POST['stock'];
        $desc = trim($_POST['description']);
        $care = trim($_POST['care_instructions']);
        
        $image = 'default.jpg'; // fallback
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image']['tmp_name'];
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            if (move_uploaded_file($tmp_name, '../assets/images/' . $file_name)) {
                $image = $file_name;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO plants (name, botanical_name, description, price, category, care_instructions, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $botanical, $desc, $price, $category, $care, $stock, $image])) {
            $message = "<div class='alert alert-success'>Plant variety added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add plant.</div>";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = (int)$_POST['plant_id'];
        $stmt = $pdo->prepare("DELETE FROM plants WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "<div class='alert alert-success'>Plant variety deleted successfully.</div>";
        }
    }
}

$plants = $pdo->query("SELECT * FROM plants ORDER BY id DESC")->fetchAll();
?>

<h1 class="mb-4">Greenhouse Inventory Management</h1>
<?php echo $message; ?>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-success text-white fw-bold">🌱 Add New Plant Variety</div>
    <div class="card-body">
        <form action="plants.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Plant Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Fiddle Leaf Fig" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Botanical Name (Scientific Info)</label>
                    <input type="text" name="botanical_name" class="form-control" placeholder="e.g. Ficus lyrata" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Price ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 19.99" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="stock" class="form-control" placeholder="e.g. 10" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="Indoor">Indoor</option>
                        <option value="Outdoor">Outdoor</option>
                        <option value="Flowering">Flowering</option>
                        <option value="Succulent">Succulent</option>
                    </select>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Plant Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Describe the plant variety features..." required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Care Instructions</label>
                    <textarea name="care_instructions" class="form-control" rows="2" placeholder="Include lighting, soil, and watering specifications..." required></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-success px-4">Save to Inventory</button>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white fw-bold">Active Plant Stock</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle bg-white mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th style="width: 70px;">Image</th>
                        <th>Name</th>
                        <th>Botanical Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th style="width: 100px;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plants as $p): ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td><img src="../assets/images/<?php echo htmlspecialchars($p['image']); ?>" width="50" height="50" class="rounded object-fit-cover" onerror="this.src='https://via.placeholder.com/50'"></td>
                            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                            <td><em class="text-success"><?php echo htmlspecialchars($p['botanical_name'] ?? 'N/A'); ?></em></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['category']); ?></span></td>
                            <td class="fw-bold">LKR <?php echo number_format($p['price'], 2); ?></td>
                            <td>
                                <?php if ($p['stock'] == 0): ?>
                                    <span class="badge bg-danger">Out of stock</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?php echo $p['stock']; ?> items</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <form action="plants.php" method="POST" onsubmit="return confirm('Remove this plant from inventory?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="plant_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm px-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
