<?php
require_once 'header.php';

// Quick stats
$total_plants = $pdo->query("SELECT COUNT(*) FROM plants")->fetchColumn();
$pending_inquiries = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE reply IS NULL")->fetchColumn();
$total_workshops = $pdo->query("SELECT COUNT(*) FROM workshops")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM workshop_registrations")->fetchColumn();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="bg-white p-5 rounded-4 shadow-sm border-0 position-relative overflow-hidden mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold text-success">Welcome Back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                    <p class="lead text-muted">Manage your greenhouse plants, respond to client inquiries, organize seasonal workshops, and update gardening services from one central dashboard.</p>
                    <a href="plants.php" class="btn btn-success px-4 py-2 mt-2">Manage Plant Inventory</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-4 text-success">🌿</span>
                    <span class="badge bg-success-subtle text-success p-2">Plants</span>
                </div>
                <h5 class="card-title text-muted fw-semibold">Total Inventory</h5>
                <h2 class="display-6 fw-bold mb-0 text-dark"><?php echo $total_plants; ?></h2>
                <small class="text-muted">Unique plant varieties</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-4 text-warning">✉️</span>
                    <span class="badge bg-warning-subtle text-warning p-2">Queries</span>
                </div>
                <h5 class="card-title text-muted fw-semibold">Pending Replies</h5>
                <h2 class="display-6 fw-bold mb-0 text-dark"><?php echo $pending_inquiries; ?></h2>
                <small class="text-muted">Customer care inquiries</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-4 text-primary">🎓</span>
                    <span class="badge bg-primary-subtle text-primary p-2">Workshops</span>
                </div>
                <h5 class="card-title text-muted fw-semibold">Scheduled Events</h5>
                <h2 class="display-6 fw-bold mb-0 text-dark"><?php echo $total_workshops; ?></h2>
                <small class="text-muted">Educational sessions</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-4 text-danger">🎟️</span>
                    <span class="badge bg-danger-subtle text-danger p-2">Bookings</span>
                </div>
                <h5 class="card-title text-muted fw-semibold">Total Registrants</h5>
                <h2 class="display-6 fw-bold mb-0 text-dark"><?php echo $total_bookings; ?></h2>
                <small class="text-muted">Workshop participants</small>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
