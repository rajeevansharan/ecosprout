<?php
require_once 'header.php';

// Handle Reply
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['inquiry_id']) && isset($_POST['reply'])) {
    $id = (int)$_POST['inquiry_id'];
    $reply = trim($_POST['reply']);
    
    if (!empty($reply)) {
        $stmt = $pdo->prepare("UPDATE inquiries SET reply = ? WHERE id = ?");
        if ($stmt->execute([$reply, $id])) {
            $message = "<div class='alert alert-success'>Response saved and sent successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to save response.</div>";
        }
    }
}

$inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY reply IS NULL DESC, created_at DESC")->fetchAll();
?>

<h1 class="mb-4">Customer Care Inquiries</h1>
<?php echo $message; ?>

<div class="row">
    <?php if (empty($inquiries)): ?>
        <div class="col-md-12">
            <div class="alert alert-info">No customer inquiries or care requests have been received yet.</div>
        </div>
    <?php else: ?>
        <?php foreach ($inquiries as $inq): ?>
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">From: <?php echo htmlspecialchars($inq['name']); ?> (<?php echo htmlspecialchars($inq['email']); ?>)</span>
                        <span class="badge <?php echo $inq['reply'] ? 'bg-success' : 'bg-warning text-dark'; ?>">
                            <?php echo $inq['reply'] ? 'Answered' : 'Pending Response'; ?>
                        </span>
                    </div>
                    <div class="card-body bg-white">
                        <h6 class="text-muted">Inquiry Date: <?php echo date('M d, Y H:i', strtotime($inq['created_at'])); ?></h6>
                        <div class="p-3 bg-light rounded mb-3 text-secondary" style="white-space: pre-wrap;"><?php echo htmlspecialchars($inq['message']); ?></div>
                        
                        <?php if ($inq['reply']): ?>
                            <div class="p-3 bg-success-subtle border-start border-success border-4 rounded text-success-emphasis">
                                <strong>Staff Response:</strong>
                                <p class="mb-0 mt-1" style="white-space: pre-wrap;"><?php echo htmlspecialchars($inq['reply']); ?></p>
                            </div>
                        <?php else: ?>
                            <form action="inquiries.php" method="POST">
                                <input type="hidden" name="inquiry_id" value="<?php echo $inq['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-success">Type Your Response:</label>
                                    <textarea name="reply" class="form-control" rows="3" placeholder="Provide professional gardening or care advice..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success px-4">Submit Answer</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
