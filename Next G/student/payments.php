<?php
include 'includes/header.php';

// Get payment summary
$payment_summary = $conn->query("
    SELECT 
        s.total_fees,
        COALESCE(SUM(p.amount_paid), 0) as total_paid,
        (s.total_fees - COALESCE(SUM(p.amount_paid), 0)) as pending_fees,
        COUNT(p.id) as payment_count
    FROM students s
    LEFT JOIN payments p ON s.id = p.student_id
    WHERE s.id = {$student['id']}
    GROUP BY s.id
")->fetch_assoc();

// Get all payments
$payments = $conn->query("
    SELECT p.*, a.full_name as admin_name 
    FROM payments p 
    LEFT JOIN admins a ON p.created_by = a.id 
    WHERE p.student_id = {$student['id']} 
    ORDER BY p.payment_date DESC, p.created_at DESC
");
?>

<div class="page-header">
    <h2><i class="fas fa-money-bill-wave text-purple"></i> Payment History</h2>
    <p class="text-muted mb-0">View all your payment transactions</p>
</div>

<!-- Payment Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Fees</p>
                        <h4 class="mb-0">₹<?php echo number_format($payment_summary['total_fees'], 2); ?></h4>
                    </div>
                    <div class="card-icon icon-purple">
                       <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Paid</p>
                        <h4 class="mb-0 text-success">₹<?php echo number_format($payment_summary['total_paid'], 2); ?></h4>
                    </div>
                    <div class="card-icon icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Pending</p>
                        <h4 class="mb-0 text-danger">₹<?php echo number_format($payment_summary['pending_fees'], 2); ?></h4>
                    </div>
                    <div class="card-icon icon-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Transactions</p>
                        <h4 class="mb-0"><?php echo $payment_summary['payment_count']; ?></h4>
                    </div>
                    <div class="card-icon icon-warning">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Table -->
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-purple mb-0"><i class="fas fa-history"></i> All Transactions</h5>
        <?php if ($payments->num_rows > 0): ?>
        <button class="btn btn-sm btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
        <?php endif; ?>
    </div>
    
    <?php if ($payments->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Receipt Number</th>
                    <th>Payment Date</th>
                    <th>Amount Paid</th>
                    <th>Payment Method</th>
                    <th>Received By</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sno = 1;
                while ($payment = $payments->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $sno++; ?></td>
                    <td><strong><?php echo htmlspecialchars($payment['receipt_number'] ?? 'N/A'); ?></strong></td>
                    <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                    <td><span class="badge bg-success fs-6">₹<?php echo number_format($payment['amount_paid'], 2); ?></span></td>
                    <td><span class="badge bg-info"><?php echo $payment['payment_method']; ?></span></td>
                    <td><small><?php echo htmlspecialchars($payment['admin_name'] ?? 'N/A'); ?></small></td>
                    <td><small><?php echo htmlspecialchars($payment['notes'] ?? '-'); ?></small></td>
                    <td>
                        <a href="receipt_view.php?id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-receipt"></i> Receipt
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td colspan="3"><strong>Total Paid:</strong></td>
                    <td colspan="5"><strong class="text-success fs-5">₹<?php echo number_format($payment_summary['total_paid'], 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No payment records found.
    </div>
    <?php endif; ?>
</div>

<?php if ($payment_summary['pending_fees'] > 0): ?>
<div class="alert alert-warning mt-4">
    <i class="fas fa-exclamation-triangle"></i> <strong>Pending Fees:</strong> You have ₹<?php echo number_format($payment_summary['pending_fees'], 2); ?> pending. Please contact the academy office for payment.
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>