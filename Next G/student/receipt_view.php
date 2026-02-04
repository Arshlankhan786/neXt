<?php
require_once '../admin/config/database.php';
require_once 'config/student_auth.php';
requireStudentLogin();

$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($payment_id === 0) {
    die("Invalid payment ID");
}

$student = getCurrentStudent();

// Get payment details with student info - VERIFY OWNERSHIP
$query = "SELECT 
    p.*,
    s.student_code,
    s.full_name as student_name,
    s.phone,
    s.email,
    s.address,
    s.total_fees,
    c.name as course_name,
    cat.name as category_name,
    a.full_name as admin_name,
    COALESCE((SELECT SUM(amount_paid) FROM payments WHERE student_id = s.id AND id <= p.id), 0) as total_paid_till_now,
    (s.total_fees - COALESCE((SELECT SUM(amount_paid) FROM payments WHERE student_id = s.id AND id <= p.id), 0)) as remaining_after_payment
FROM payments p
JOIN students s ON p.student_id = s.id
JOIN courses c ON s.course_id = c.id
JOIN categories cat ON s.category_id = cat.id
LEFT JOIN admins a ON p.created_by = a.id
WHERE p.id = ? AND s.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $payment_id, $student['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Receipt not found or access denied");
}

$payment = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?php echo $payment['receipt_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .receipt-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body style="background: #f0f0f0;">
    <!-- Action Buttons -->
    <div class="text-center my-3 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg me-2">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-lg">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <!-- Receipt Content -->
    <div class="receipt-container">
        <div class="receipt-header">
            <h1 style="color: #7c3aed;">🎓 NEXT ACADEMY</h1>
            <p class="mb-0">Payment Receipt</p>
            <h3 class="mt-2" style="color: #7c3aed;">#<?php echo htmlspecialchars($payment['receipt_number']); ?></h3>
        </div>

        <div class="receipt-info">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($payment['payment_date'])); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['payment_method']); ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>Receipt Date:</strong> <?php echo date('d M Y, h:i A'); ?></p>
                    <p><strong>Received By:</strong> <?php echo htmlspecialchars($payment['admin_name'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>

        <h5 style="color: #7c3aed; border-bottom: 2px solid #7c3aed; padding-bottom: 10px;">Student Details</h5>
        <div class="row mt-3">
            <div class="col-md-6">
                <p><strong>Student Code:</strong> <?php echo htmlspecialchars($payment['student_code']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($payment['student_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($payment['phone']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Category:</strong> <?php echo htmlspecialchars($payment['category_name']); ?></p>
                <p><strong>Course:</strong> <?php echo htmlspecialchars($payment['course_name']); ?></p>
            </div>
        </div>

        <table class="table table-bordered mt-4">
            <thead style="background: #f3e8ff;">
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Total Course Fees</strong></td>
                    <td class="text-end">₹<?php echo number_format($payment['total_fees'], 2); ?></td>
                </tr>
                <tr>
                    <td><strong>Amount Paid (This Payment)</strong></td>
                    <td class="text-end"><strong style="color: #10b981;">₹<?php echo number_format($payment['amount_paid'], 2); ?></strong></td>
                </tr>
                <tr>
                    <td><strong>Total Paid Till Now</strong></td>
                    <td class="text-end">₹<?php echo number_format($payment['total_paid_till_now'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="alert <?php echo $payment['remaining_after_payment'] > 0 ? 'alert-warning' : 'alert-success'; ?>">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-0">Remaining Balance:</h5>
                </div>
                <div class="col-md-6 text-end">
                    <h4 class="mb-0" style="color: <?php echo $payment['remaining_after_payment'] > 0 ? '#ef4444' : '#10b981'; ?>;">
                        ₹<?php echo number_format($payment['remaining_after_payment'], 2); ?>
                    </h4>
                    <?php if ($payment['remaining_after_payment'] <= 0): ?>
                    <p class="text-success mb-0"><strong>✓ Fully Paid</strong></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($payment['notes']): ?>
        <div class="mt-4">
            <h6 style="color: #7c3aed;">Notes:</h6>
            <p class="text-muted"><?php echo nl2br(htmlspecialchars($payment['notes'])); ?></p>
        </div>
        <?php endif; ?>

        <div class="text-center mt-5 pt-4" style="border-top: 2px dashed #7c3aed;">
            <p class="mb-1"><strong>Thank you for your payment!</strong></p>
            <p class="mb-0"><small>This is a computer-generated receipt.</small></p>
        </div>
    </div>
</body>
</html>