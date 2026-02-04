<?php
include 'includes/header.php';

// Get all payments with receipts
$receipts = $conn->query("
    SELECT p.*, a.full_name as admin_name 
    FROM payments p 
    LEFT JOIN admins a ON p.created_by = a.id 
    WHERE p.student_id = {$student['id']} 
    AND p.receipt_number IS NOT NULL
    ORDER BY p.payment_date DESC, p.created_at DESC
");
?>

<div class="page-header">
    <h2><i class="fas fa-receipt text-purple"></i> My Receipts</h2>
    <p class="text-muted mb-0">Download and view your payment receipts</p>
</div>

<div class="table-card">
    <?php if ($receipts->num_rows > 0): ?>
    <div class="row g-4">
        <?php while ($receipt = $receipts->fetch_assoc()): ?>
        <div class="col-lg-6">
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1">Receipt #<?php echo htmlspecialchars($receipt['receipt_number']); ?></h6>
                            <small class="text-muted"><?php echo date('d M Y', strtotime($receipt['payment_date'])); ?></small>
                        </div>
                        <span class="badge bg-success fs-6">₹<?php echo number_format($receipt['amount_paid'], 2); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Payment Method:</small>
                        <span class="badge bg-info ms-2"><?php echo $receipt['payment_method']; ?></span>
                    </div>
                    
                    <?php if ($receipt['notes']): ?>
                    <div class="mb-3">
                        <small class="text-muted">Notes:</small>
                        <p class="mb-0"><small><?php echo htmlspecialchars($receipt['notes']); ?></small></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <a href="receipt_view.php?id=<?php echo $receipt['id']; ?>" class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-eye"></i> View Receipt
                        </a>
                        <!-- <a href="receipt_download.php?id=<?php echo $receipt['id']; ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download"></i> Download PDF
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h5>No Receipts Available</h5>
        <p class="mb-0">You don't have any payment receipts yet.</p>
    </div>
    <?php endif; ?>
</div>



<?php include 'includes/footer.php'; ?>