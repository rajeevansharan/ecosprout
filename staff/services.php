<?php
require_once 'header.php';

// Handle Add/Delete
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $price = (float)$_POST['price'];
        
        $image = 'default_service.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image']['tmp_name'];
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            if (move_uploaded_file($tmp_name, '../assets/images/' . $file_name)) {
                $image = $file_name;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO services (name, description, price, image) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $desc, $price, $image])) {
            $message = "<div class='alert alert-success'>Service offered added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add service.</div>";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id = (int)$_POST['service_id'];
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $price = (float)$_POST['price'];
        
        $image_query = "";
        $params = [$name, $desc, $price, $id];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image']['tmp_name'];
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            if (move_uploaded_file($tmp_name, '../assets/images/' . $file_name)) {
                $image_query = ", image = ?";
                $params = [$name, $desc, $price, $file_name, $id];
            }
        }
        
        $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, price = ? $image_query WHERE id = ?");
        if ($stmt->execute($params)) {
            $message = "<div class='alert alert-success'>Service updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to update service.</div>";
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

$edit_service = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([(int)$_GET['edit_id']]);
    $edit_service = $stmt->fetch();
}
?>

<h1 class="mb-4">Gardening & Landscaping Services Management</h1>
<?php echo $message; ?>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white fw-bold"><?php echo $edit_service ? '✏️ Edit Service' : '🏡 Add New Gardening Service'; ?></div>
            <div class="card-body">
                <form action="services.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $edit_service ? 'edit' : 'add'; ?>">
                    <?php if ($edit_service): ?>
                        <input type="hidden" name="service_id" value="<?php echo $edit_service['id']; ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Service Title</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Lawn Restoration & Fertilization" value="<?php echo htmlspecialchars($edit_service['name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price Description/Base Rate ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 45.00" value="<?php echo $edit_service ? number_format($edit_service['price'], 2, '.', '') : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Detail the landscaping or gardening package..." required><?php echo htmlspecialchars($edit_service['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Service Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if ($edit_service && !empty($edit_service['image'])): ?>
                            <small class="text-muted d-block mt-1">Leave empty to keep current image.</small>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><?php echo $edit_service ? 'Update Service' : 'Publish Service'; ?></button>
                    <?php if ($edit_service): ?>
                        <a href="services.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>
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
                                <th style="width: 70px;">Image</th>
                                <th>Service</th>
                                <th>Base Cost</th>
                                <th style="width: 100px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No gardening services published.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($services as $srv): ?>
                                    <tr>
                                        <td><img src="../assets/images/<?php echo htmlspecialchars($srv['image'] ?? 'default_service.jpg'); ?>" width="50" height="50" class="rounded object-fit-cover" onerror="this.src='https://via.placeholder.com/50'"></td>
                                        <td>
                                            <strong class="text-success"><?php echo htmlspecialchars($srv['name']); ?></strong>
                                            <p class="text-muted small mb-0 mt-1"><?php echo htmlspecialchars($srv['description']); ?></p>
                                        </td>
                                        <td class="fw-bold text-dark">LKR <?php echo number_format($srv['price'], 2); ?></td>
                                        <td class="text-center align-middle">
                                            <a href="services.php?edit_id=<?php echo $srv['id']; ?>" class="btn btn-outline-primary btn-sm px-3 mb-2 w-100">Edit</a>
                                            <form action="services.php" method="POST" onsubmit="return confirm('Discontinue this service?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="service_id" value="<?php echo $srv['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-3 w-100">Remove</button>
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
