<?php
require_once 'header.php';

// Handle Add/Delete
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $price = (float)$_POST['price'];
        
        $stmt = $pdo->prepare("INSERT INTO services (name, description, price) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $desc, $price])) {
            $message = "<div class='alert alert-success'>Service offered added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add service.</div>";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = (int)$_POST['service_id'];
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "<div class='alert alert-success'>Service removed successfully.</div>";
        }
    }
}

$services = $pdo->query("SELECT * FROM services ORDER BY id DESC")->fetchAll();
?>

<h1 class="mb-4">Gardening & Landscaping Services Management</h1>
<?php echo $message; ?>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white fw-bold">🏡 Add New Gardening Service</div>
            <div class="card-body">
                <form action="services.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Service Title</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Lawn Restoration & Fertilization" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price Description/Base Rate ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 45.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Detail the landscaping or gardening package..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Publish Service</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold">Current Published Services</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle bg-white mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Service</th>
                                <th>Base Cost</th>
                                <th style="width: 100px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No gardening services published.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($services as $srv): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-success"><?php echo htmlspecialchars($srv['name']); ?></strong>
                                            <p class="text-muted small mb-0 mt-1"><?php echo htmlspecialchars($srv['description']); ?></p>
                                        </td>
                                        <td class="fw-bold text-dark">$<?php echo number_format($srv['price'], 2); ?></td>
                                        <td class="text-center">
                                            <form action="services.php" method="POST" onsubmit="return confirm('Discontinue this service?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="service_id" value="<?php echo $srv['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-3">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
