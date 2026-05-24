<?php
require_once 'header.php';

$inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC")->fetchAll();
?>

<h1 class="mb-4">View Inquiries</h1>

<div class="row">
    <?php foreach ($inquiries as $inq): ?>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <strong>From:</strong> <?php echo htmlspecialchars($inq['name']); ?> 
                    (<a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>"><?php echo htmlspecialchars($inq['email']); ?></a>)
                    <span class="float-end text-muted small"><?php echo date('M d, Y H:i', strtotime($inq['created_at'])); ?></span>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($inq['message'])); ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if(empty($inquiries)): ?>
        <div class="col-12"><p class="text-center text-muted">No inquiries found.</p></div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
