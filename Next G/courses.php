<?php
require_once 'admin/config/database.php';

// Get all active categories
$categories = $conn->query("SELECT * FROM categories WHERE status = 'Active' ORDER BY name");

// Get filter
$filter_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Courses - Next Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="assets/css/public-style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Page Header -->
    <section class="hero-section" style="min-height: 300px;">
        <div class="container text-center">
            <h1 data-aos="fade-up">Our Courses</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="100">Explore our comprehensive range of professional courses</p>
        </div>
    </section>
    
    <!-- Courses Section -->
    <section class="py-5">
        <div class="container">
            <!-- Category Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 justify-content-center" data-aos="fade-up">
                        <a href="courses.php" class="btn <?php echo $filter_category == 0 ? 'btn-purple' : 'btn-outline-secondary'; ?>">
                            All Courses
                        </a>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                        <a href="courses.php?category=<?php echo $cat['id']; ?>" 
                           class="btn <?php echo $filter_category == $cat['id'] ? 'btn-purple' : 'btn-outline-secondary'; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            
            <!-- Courses Grid -->
            <div class="row g-4">
                <?php
                $query = "SELECT c.*, cat.name as category_name 
                         FROM courses c 
                         JOIN categories cat ON c.category_id = cat.id 
                         WHERE c.status = 'Active'";
                
                if ($filter_category > 0) {
                    $query .= " AND c.category_id = $filter_category";
                }
                
                $query .= " ORDER BY c.name";
                
                $courses = $conn->query($query);
                
                if ($courses->num_rows > 0):
                    $delay = 100;
                    while ($course = $courses->fetch_assoc()):
                        // Get fee details
                        $fees_query = $conn->query("SELECT duration_months, fee_amount 
                                                    FROM course_fees 
                                                    WHERE course_id = {$course['id']} 
                                                    ORDER BY duration_months");
                ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>" id="course-<?php echo $course['id']; ?>">
                    <div class="card course-card">
                        <img src="https://via.placeholder.com/400x200/7c3aed/ffffff?text=<?php echo urlencode($course['name']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($course['name']); ?>">
                        <div class="card-body">
                            <span class="course-badge"><?php echo htmlspecialchars($course['category_name']); ?></span>
                            <h5 class="card-title mt-2"><?php echo htmlspecialchars($course['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($course['description']); ?></p>
                            
                            <!-- Fee Structure -->
                            <div class="mt-3">
                                <h6 class="text-primary"><i class="fa-solid fa-indian-rupee-sign"></i> Fee Structure:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Duration</th>
                                                <th>Fees</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            if ($fees_query->num_rows > 0):
                                                while ($fee = $fees_query->fetch_assoc()): 
                                            ?>
                                            <tr>
                                                <td><?php echo $fee['duration_months']; ?> Months</td>
                                                <td><strong>₹<?php echo number_format($fee['fee_amount']); ?></strong></td>
                                            </tr>
                                            <?php 
                                                endwhile;
                                            else:
                                            ?>
                                            <tr>
                                                <td colspan="2" class="text-center">Contact for fees</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <a href="contact.php?course=<?php echo $course['id']; ?>" class="btn btn-enroll w-100 mt-3">
                                <i class="fas fa-paper-plane"></i> Enquire Now
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                        $delay += 100;
                    endwhile;
                else:
                ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No courses found in this category.
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-5 bg-light">
        <div class="container text-center" data-aos="zoom-in">
            <h2 class="mb-4">Can't Find What You're Looking For?</h2>
            <p class="lead mb-4">Contact us for customized training programs</p>
            <a href="contact.php" class="btn btn-purple btn-lg">
                <i class="fas fa-envelope"></i> Get in Touch
            </a>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>