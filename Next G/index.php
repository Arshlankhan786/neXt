<?php
require_once 'admin/config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next Academy - Quality Education for Your Future</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="assets/css/public-style.css">
    <link rel="icon" type="icon" href="skill-development.png">
    
    
<meta name="description" content="Next Academy India offers professional courses in digital marketing, graphic design, web development and more with job assistance and real-world training.">

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">

<!-- Optional for social preview -->
<meta property="og:title" content="Next Academy India">
<meta property="og:description" content="Professional career-oriented training with real projects and job placement support.">
<meta property="og:url" content="https://nextacademyindia.com/">
<meta property="og:type" content="website">
<meta name="keywords" content="digital marketing course, web development training, graphic design institute, job oriented courses, next academy india">

</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right">
                    <h1>Transform Your Future with Quality Education</h1>
                    <p>Join Next Academy and master the skills that matter. Expert instructors, practical training, and
                        career-focused courses.</p>
                    <a href="courses.php" class="btn btn-hero me-3">
                        <i class="fas fa-book"></i> Explore Courses
                    </a>
                    <a href="contact.php" class="btn btn-outline-light btn-hero">
                        <i class="fas fa-phone"></i> Contact Us
                    </a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <!-- You can add an illustration or image here -->
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Why Choose Next Academy?</h2>
                <p>We provide world-class education with industry-relevant curriculum</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h4>Expert Instructors</h4>
                        <p>Learn from industry professionals with years of real-world experience</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h4>Hands-on Training</h4>
                        <p>Practical projects and assignments to build your portfolio</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h4>Industry Certification</h4>
                        <p>Receive recognized certificates upon course completion</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h4>Career Support</h4>
                        <p>Job placement assistance and career guidance</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses -->
    <section class="py-5">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Featured Courses</h2>
                <p>Explore our most popular programs</p>
            </div>

            <div class="row g-4">
                <?php
                $courses = $conn->query("SELECT c.*, cat.name as category_name 
                                        FROM courses c 
                                        JOIN categories cat ON c.category_id = cat.id 
                                        WHERE c.status = 'Active' 
                                        LIMIT 6");

                $delay = 100;
                while ($course = $courses->fetch_assoc()):
                    // Get fee range
                    $fees = $conn->query("SELECT MIN(fee_amount) as min_fee, MAX(fee_amount) as max_fee 
                                         FROM course_fees WHERE course_id = {$course['id']}");
                    $fee_range = $fees->fetch_assoc();
                    ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card course-card">
                            <img src="https://via.placeholder.com/400x200/7c3aed/ffffff?text=<?php echo urlencode($course['name']); ?>"
                                class="card-img-top" alt="<?php echo htmlspecialchars($course['name']); ?>">
                            <div class="card-body">
                                <span class="course-badge"><?php echo htmlspecialchars($course['category_name']); ?></span>
                                <h5 class="card-title mt-2"><?php echo htmlspecialchars($course['name']); ?></h5>
                                <p class="card-text text-muted">
                                    <?php echo substr(htmlspecialchars($course['description']), 0, 80); ?>...</p>

                                <div class="course-meta">
                                    <span><i class="fas fa-clock"></i> 3-24 months</span>
                                    <span class="text-primary fw-bold">
                                        ₹<?php echo number_format($fee_range['min_fee']); ?>+
                                    </span>
                                </div>

                                <a href="courses.php#course-<?php echo $course['id']; ?>" class="btn btn-enroll w-100">
                                    <i class="fas fa-arrow-right"></i> Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 100;
                endwhile;
                ?>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="courses.php" class="btn btn-purple btn-lg">
                    View All Courses <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-box">
                        <h2 class="text-primary mb-2">
                            <?php
                            $total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc();
                            echo number_format($total_students['count']);
                            ?>+
                        </h2>
                        <h5>Happy Students</h5>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-box">
                        <h2 class="text-primary mb-2">
                            <?php
                            $total_courses = $conn->query("SELECT COUNT(*) as count FROM courses WHERE status = 'Active'")->fetch_assoc();
                            echo $total_courses['count'];
                            ?>+
                        </h2>
                        <h5>Quality Courses</h5>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-box">
                        <h2 class="text-primary mb-2">15+</h2>
                        <h5>Expert Instructors</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-box">
                        <h2 class="text-primary mb-2">95%</h2>
                        <h5>Success Rate</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
        <div class="container text-center text-white" data-aos="zoom-in">
            <h2 class="mb-4">Ready to Start Your Learning Journey?</h2>
            <p class="lead mb-4">Join thousands of students who have transformed their careers with us</p>
            <a href="contact.php" class="btn btn-light btn-lg me-3">
                <i class="fas fa-phone"></i> Contact Us
            </a>
            <a href="courses.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-book"></i> Browse Courses
            </a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>