<?php
include 'includes/header.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get ALL students who paid during the period
$paidStudents = $conn->query("
    SELECT 
        s.id,
        s.student_code,
        s.full_name,
        s.phone,
        COALESCE(SUM(p.amount_paid), 0) as total_paid,
        MIN(p.payment_date) as first_payment_date,
        MAX(p.payment_date) as last_payment_date,
        COUNT(p.id) as payment_count,
        c.name as course_name
    FROM students s
    JOIN payments p ON s.id = p.student_id 
        AND p.payment_date BETWEEN '$start_date' AND '$end_date'
    JOIN courses c ON s.course_id = c.id
    WHERE s.status = 'Active'
    GROUP BY s.id
    ORDER BY total_paid DESC
");

$total_count = $paidStudents->num_rows;
$total_collection = 0;
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="fas fa-check-circle text-success"></i> Paid Students - Full List</h2>
        <p class="text-muted mb-0">Students who paid between <?php echo date('d M Y', strtotime($start_date)); ?> - <?php echo date('d M Y', strtotime($end_date)); ?></p>
    </div>
    <a href="index.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- Statistics Card -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Students Paid</p>
                        <h3 class="mb-0 text-success"><?php echo $total_count; ?></h3>
                        <small class="text-muted">During selected period</small>
                    </div>
                    <div class="card-icon icon-success">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Collection</p>
                        <h3 class="mb-0 text-purple">
                            <?php 
                            $paidStudents->data_seek(0);
                            while ($s = $paidStudents->fetch_assoc()) {
                                $total_collection += $s['total_paid'];
                            }
                            echo '₹' . number_format($total_collection, 2);
                            ?>
                        </h3>
                        <small class="text-muted">From <?php echo $total_count; ?> students</small>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Students Table -->
<div class="table-card">
    <div class="mb-3">
        <input type="text" class="form-control" id="searchStudent" placeholder="Search by name, code, or phone...">
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-striped" id="paidStudentsTable">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Student Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Course</th>
                    <th>Amount Paid</th>
                    <th>Payment Period</th>
                    <th>Payments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $paidStudents->data_seek(0);
                $sno = 1;
                while ($student = $paidStudents->fetch_assoc()): 
                ?>
                <tr>
                    <td><?php echo $sno++; ?></td>
                    <td><strong><?php echo htmlspecialchars($student['student_code']); ?></strong></td>
                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                    <td><small><?php echo htmlspecialchars($student['course_name']); ?></small></td>
                    <td><span class="badge bg-success fs-6">₹<?php echo number_format($student['total_paid'], 2); ?></span></td>
                    <td>
                        <small>
                            <i class="fas fa-calendar"></i>
                            <?php if ($student['first_payment_date'] == $student['last_payment_date']): ?>
                                <?php echo date('d M Y', strtotime($student['first_payment_date'])); ?>
                            <?php else: ?>
                                <?php echo date('d M', strtotime($student['first_payment_date'])); ?> - <?php echo date('d M Y', strtotime($student['last_payment_date'])); ?>
                            <?php endif; ?>
                        </small>
                    </td>
                    <td><span class="badge bg-info"><?php echo $student['payment_count']; ?> payment(s)</span></td>
                    <td>
                        <a href="student_details.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchStudent').addEventListener('keyup', function() {
    const filter = this.value.toUpperCase();
    const rows = document.querySelectorAll('#paidStudentsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent || row.innerText;
        row.style.display = text.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>