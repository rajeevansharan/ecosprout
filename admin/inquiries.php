<?php
require_once 'header.php';

$inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY reply IS NULL DESC, created_at DESC")->fetchAll();
?>

<h1 class="mb-4">Customer Inquiries</h1>

<div class="row">
    <?php if (empty($inquiries)): ?>
        <div class="col-12"><p class="text-center text-muted">No inquiries found.</p></div>
    <?php else: ?>
        <?php foreach ($inquiries as $inq): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span>
                            <strong><?php echo htmlspecialchars($inq['name']); ?></strong>
                            &nbsp;(<a class="text-info" href="mailto:<?php echo htmlspecialchars($inq['email']); ?>"><?php echo htmlspecialchars($inq['email']); ?></a>)
                        </span>
                        <span class="badge <?php echo $inq['reply'] ? 'bg-success' : 'bg-warning text-dark'; ?>">
                            <?php echo $inq['reply'] ? 'Answered' : 'Awaiting Reply'; ?>
                        </span>
                    </div>
                    <div class="card-body bg-white">
                        <p class="text-muted small mb-1"><?php echo date('M d, Y H:i', strtotime($inq['created_at'])); ?></p>
                        <div class="p-3 bg-light rounded mb-3"><?php echo nl2br(htmlspecialchars($inq['message'])); ?></div>
                        <?php if ($inq['reply']): ?>
                            <div class="p-3 bg-success-subtle border-start border-success border-4 rounded small">
                                <strong>Staff Reply:</strong>
                                <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($inq['reply'])); ?></p>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">⏳ Awaiting response from nursery staff.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
