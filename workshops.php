<?php
require_once 'includes/header.php';

$success = '';
$error = '';

// Handle workshop registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_workshop_id'])) {
    if (!isset($_SESSION['user_id'])) {
        $error = "You must be <a href='login.php'>logged in</a> to register for a workshop.";
    } else {
        $workshop_id = (int)$_POST['register_workshop_id'];
        $user_id = $_SESSION['user_id'];

        // Check capacity
        $stmt = $pdo->prepare("SELECT capacity FROM workshops WHERE id = ?");
        $stmt->execute([$workshop_id]);
        $workshop = $stmt->fetch();

        $registered = $pdo->prepare("SELECT COUNT(*) FROM workshop_registrations WHERE workshop_id = ?");
        $registered->execute([$workshop_id]);
        $count = $registered->fetchColumn();

        if ($count >= $workshop['capacity']) {
            $error = "Sorry, this workshop is fully booked.";
        } else {
            // Check duplicate
            $dup = $pdo->prepare("SELECT id FROM workshop_registrations WHERE workshop_id = ? AND user_id = ?");
            $dup->execute([$workshop_id, $user_id]);
            if ($dup->fetch()) {
                $error = "You are already registered for this workshop!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO workshop_registrations (workshop_id, user_id) VALUES (?, ?)");
                if ($stmt->execute([$workshop_id, $user_id])) {
                    $success = "You have successfully registered! We look forward to seeing you.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}

// Fetch workshops with registration counts
$workshops = $pdo->query("
    SELECT w.*, COUNT(wr.id) as registered_count
    FROM workshops w
    LEFT JOIN workshop_registrations wr ON w.id = wr.id
    GROUP BY w.id
    ORDER BY w.date ASC
")->fetchAll();

// Get user's existing registrations
$user_bookings = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT workshop_id FROM workshop_registrations WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    foreach ($stmt->fetchAll() as $b) {
        $user_bookings[] = $b['workshop_id'];
    }
}

// Re-fetch proper count
$workshops_data = $pdo->query("
    SELECT w.*, (SELECT COUNT(*) FROM workshop_registrations WHERE workshop_id = w.id) as registered_count
    FROM workshops w
    ORDER BY w.date ASC
")->fetchAll();
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold" style="color: #198754;">🎓 Workshops & Educational Events</h1>
        <p class="lead text-muted mx-auto" style="max-width: 640px;">Join our expert-led workshops to learn plant care, garden design, and sustainable growing techniques. Spaces are limited — register early!</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($workshops_data)): ?>
        <div class="alert alert-info text-center py-5">
            <h4 class="mb-2">No workshops scheduled yet.</h4>
            <p class="text-muted mb-0">Check back soon — we regularly schedule seasonal gardening events!</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($workshops_data as $ws):
                $seats_left = $ws['capacity'] - $ws['registered_count'];
                $is_full = $seats_left <= 0;
                $already_registered = in_array($ws['id'], $user_bookings);
                $is_past = strtotime($ws['date']) < time();
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header text-white py-3" style="background-color: #198754;">
                            <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($ws['title']); ?></h5>
                        </div>
                        <div class="card-body d-flex flex-column gap-2 bg-white">
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($ws['description']); ?></p>

                            <div class="d-flex align-items-center gap-2">
                                <span class="text-success fs-5">📅</span>
                                <span class="fw-semibold"><?php echo date('D, d M Y — h:i A', strtotime($ws['date'])); ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-success fs-5">👩‍🏫</span>
                                <span><?php echo htmlspecialchars($ws['instructor']); ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-success fs-5">🪑</span>
                                <span>
                                    <?php if ($is_full): ?>
                                        <span class="badge bg-danger">Fully Booked</span>
                                    <?php else: ?>
                                        <span class="text-dark"><?php echo $seats_left; ?> of <?php echo $ws['capacity']; ?> seats available</span>
                                    <?php endif; ?>
                                </span>
                            </div>

                            <div class="mt-auto pt-3">
                                <?php if ($is_past): ?>
                                    <button class="btn btn-secondary w-100" disabled>Event Ended</button>
                                <?php elseif ($already_registered): ?>
                                    <button class="btn btn-outline-success w-100" disabled>✅ You're Registered</button>
                                <?php elseif ($is_full): ?>
                                    <button class="btn btn-outline-danger w-100" disabled>Fully Booked</button>
                                <?php elseif (!isset($_SESSION['user_id'])): ?>
                                    <a href="login.php" class="btn btn-warning w-100">Login to Register</a>
                                <?php else: ?>
                                    <form action="workshops.php" method="POST">
                                        <input type="hidden" name="register_workshop_id" value="<?php echo $ws['id']; ?>">
                                        <button type="submit" class="btn btn-success w-100 fw-bold">Register Now</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
