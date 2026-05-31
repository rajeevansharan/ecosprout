<?php
require_once 'includes/header.php';

$services = $pdo->query("SELECT * FROM services ORDER BY id ASC")->fetchAll();
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold" style="color: #198754;">🏡 Our Gardening Services</h1>
        <p class="lead text-muted mx-auto" style="max-width: 640px;">From professional garden design to indoor plant styling, our skilled nursery team brings green expertise right to your doorstep. Contact us to book any service.</p>
    </div>

    <?php if (empty($services)): ?>
        <div class="alert alert-info text-center py-5">
            <h4 class="mb-2">No services listed yet.</h4>
            <p class="text-muted mb-0">Our team is updating the services catalog. Please check back soon or <a href="contact.php">contact us</a>.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($services as $srv): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body bg-white d-flex flex-column p-4">
                            <?php if (!empty($srv['image']) && $srv['image'] !== 'default_service.jpg'): ?>
                                <img src="assets/images/<?php echo htmlspecialchars($srv['image']); ?>" class="card-img-top mb-3 rounded-3" alt="<?php echo htmlspecialchars($srv['name']); ?>" style="height: 180px; width: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-success bg-opacity-10 rounded-3 p-3 mb-3 text-center" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 3.5rem;">🌿</span>
                                </div>
                            <?php endif; ?>
                            <h5 class="fw-bold text-dark"><?php echo htmlspecialchars($srv['name']); ?></h5>
                            <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars($srv['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <span class="fs-5 fw-bold text-success">From LKR <?php echo number_format($srv['price'], 2); ?></span>
                                <a href="<?php echo isset($_SESSION['user_id']) ? 'payment.php?service_id=' . $srv['id'] : 'login.php'; ?>" class="btn btn-outline-success btn-sm px-3">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5 p-5 rounded-4" style="background-color: #f0faf3;">
            <h3 class="fw-bold text-success">Need a Custom Service?</h3>
            <p class="text-muted mb-4">Our horticultural experts can design a personalized gardening solution for your home, office, or commercial space.</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'payment.php?service_id=custom' : 'login.php'; ?>" class="btn btn-success btn-lg px-5">Get a Free Consultation</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
