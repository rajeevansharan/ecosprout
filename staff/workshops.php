<?php
require_once 'header.php';

// Handle Add/Delete
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $title = trim($_POST['title']);
        $desc = trim($_POST['description']);
        $date = $_POST['date'];
        $instructor = trim($_POST['instructor']);
        $capacity = (int)$_POST['capacity'];
        
        $stmt = $pdo->prepare("INSERT INTO workshops (title, description, date, instructor, capacity) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $desc, $date, $instructor, $capacity])) {
            $message = "<div class='alert alert-success'>Workshop scheduled successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to schedule workshop.</div>";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = (int)$_POST['workshop_id'];
        $stmt = $pdo->prepare("DELETE FROM workshops WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "<div class='alert alert-success'>Workshop deleted successfully.</div>";
        }
    }
}

// Fetch workshops and registrations
$workshops = $pdo->query("SELECT * FROM workshops ORDER BY date ASC")->fetchAll();

// Fetch registrations detailed list
$registrations = $pdo->query("
    SELECT wr.workshop_id, w.title as workshop_title, u.name as user_name, u.email as user_email, wr.created_at
    FROM workshop_registrations wr
    JOIN users u ON wr.user_id = u.id
    JOIN workshops w ON wr.workshop_id = w.id
    ORDER BY wr.created_at DESC
")->fetchAll();

// Group registrations by workshop
$grouped_regs = [];
foreach ($registrations as $r) {
    $grouped_regs[$r['workshop_id']][] = $r;
}
?>

<h1 class="mb-4">Seasonal Workshop Coordinator</h1>
<?php echo $message; ?>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white fw-bold">🎓 Schedule New Event</div>
            <div class="card-body">
                <form action="workshops.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Workshop Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Bonsai Care Workshop" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Syllabus/Topics..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Date & Time</label>
                        <input type="datetime-local" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lead Instructor</label>
                        <input type="text" name="instructor" class="form-control" placeholder="e.g. Dr. Rose" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Capacity (Persons)</label>
                        <input type="number" name="capacity" class="form-control" placeholder="e.g. 15" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Schedule Event</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold">Scheduled Classes & Participants</div>
            <div class="card-body p-4 bg-white">
                <?php if (empty($workshops)): ?>
                    <div class="text-muted py-3">No classes scheduled yet.</div>
                <?php else: ?>
                    <?php foreach ($workshops as $w): ?>
                        <div class="border-bottom pb-4 mb-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h4 class="text-success mb-1"><?php echo htmlspecialchars($w['title']); ?></h4>
                                    <span class="badge bg-secondary me-2">Instructor: <?php echo htmlspecialchars($w['instructor']); ?></span>
                                    <span class="badge bg-light text-dark">Date: <?php echo date('M d, Y - H:i', strtotime($w['date'])); ?></span>
                                </div>
                                <form action="workshops.php" method="POST" onsubmit="return confirm('Cancel this workshop? All registered students will be unbooked.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="workshop_id" value="<?php echo $w['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            </div>
                            <p class="text-muted small"><?php echo htmlspecialchars($w['description']); ?></p>
                            
                            <h6 class="mt-3 fw-bold text-secondary">
                                Registered Students (<?php 
                                    $cnt = isset($grouped_regs[$w['id']]) ? count($grouped_regs[$w['id']]) : 0; 
                                    echo "$cnt / " . $w['capacity'];
                                ?>)
                            </h6>
                            
                            <?php if ($cnt === 0): ?>
                                <p class="text-muted small italic mb-0">No bookings registered for this class yet.</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush rounded border mt-2">
                                    <?php foreach ($grouped_regs[$w['id']] as $reg): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center small py-2">
                                            <span><strong><?php echo htmlspecialchars($reg['user_name']); ?></strong> (<?php echo htmlspecialchars($reg['user_email']); ?>)</span>
                                            <span class="text-muted small">Registered: <?php echo date('M d', strtotime($reg['created_at'])); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
