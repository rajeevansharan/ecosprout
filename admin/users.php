<?php
require_once 'header.php';

// Handle requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['delete_user_id'])) {
        // Basic protection to prevent deleting the current admin
        if ($_POST['delete_user_id'] != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_POST['delete_user_id']]);
            echo "<div class='alert alert-success'>User deleted successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>You cannot delete your own account.</div>";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'create') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $role = $_POST['role'];
        
        if (!empty($name) && !empty($email) && !empty($password) && in_array($role, ['customer', 'admin', 'staff'])) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            try {
                $stmt->execute([$name, $email, $hashed, $role]);
                echo "<div class='alert alert-success'>Account created successfully!</div>";
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Failed to create account. Email might already exist.</div>";
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_role') {
        $user_id = (int)$_POST['user_id'];
        $role = $_POST['role'];
        
        if ($user_id != $_SESSION['user_id'] && in_array($role, ['customer', 'admin', 'staff'])) {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$role, $user_id]);
            echo "<div class='alert alert-success'>User role updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>You cannot change your own role.</div>";
        }
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY role, id")->fetchAll();
?>

<h1 class="mb-4">Manage Users & Staff Accounts</h1>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-dark text-white fw-bold">Add New Account (Admin, Staff, or Customer)</div>
    <div class="card-body">
        <form action="users.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Nursery Specialist" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="e.g. staff@ecosprout.com" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="e.g. min 6 chars" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="customer">Customer</option>
                        <option value="staff">Nursery Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-success px-4">Create Account</button>
        </form>
    </div>
</div>

<div class="table-responsive shadow-sm rounded">
    <table class="table table-bordered table-striped align-middle bg-white mb-0">
        <thead class="table-dark">
            <tr>
                <th style="width: 70px;">ID</th>
                <th>Name</th>
                <th>Email</th>
                <th style="width: 250px;">Role</th>
                <th>Joined</th>
                <th style="width: 120px;" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><strong class="text-secondary"><?php echo htmlspecialchars($u['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <form action="users.php" method="POST" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="action" value="update_role">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="customer" <?php echo $u['role']=='customer'?'selected':''; ?>>Customer</option>
                                    <option value="staff" <?php echo $u['role']=='staff'?'selected':''; ?>>Nursery Staff</option>
                                    <option value="admin" <?php echo $u['role']=='admin'?'selected':''; ?>>Administrator</option>
                                </select>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-danger p-2"><?php echo ucfirst($u['role']); ?> (You)</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                    <td class="text-center">
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <form action="users.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="delete_user_id" value="<?php echo $u['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm px-3">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
