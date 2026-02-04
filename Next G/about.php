<?php
require_once 'admin/config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Next Academy</title>
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
            <h1 data-aos="fade-up">About Next Academy</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="100">Empowering learners since inception</p>
        </div>
    </section>
    
    <!-- About Content -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="https://via.placeholder.com/600x400/7c3aed/ffffff?text=Our+Campus" 
                         class="img-fluid rounded shadow" 
                         alt="About Us">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h2 class="mb-4">Our Story</h2>
                    <p class="lead">Next Academy was founded with a vision to provide quality, accessible education to aspiring professionals.</p>
                    <p>We believe in practical, hands-on learning that prepares students for real-world challenges. Our courses are designed by industry experts and delivered by experienced instructors who are passionate about teaching.</p>
                    <p>Over the years, we have trained thousands of students who have gone on to build successful careers in various fields including web development, graphic design, digital marketing, and data science.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Mission & Vision -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="feature-box h-100">
                        <div class="feature-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Our Mission</h3>
                        <p>To provide world-class education and training that empowers individuals to achieve their career goals and contribute meaningfully to society.</p>
                        <ul class="text-start mt-3">
                            <li>Deliver industry-relevant curriculum</li>
                            <li>Ensure practical, hands-on learning</li>
                            <li>Provide personalized mentorship</li>
                            <li>Foster innovation and creativity</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-box h-100">
                        <div class="feature-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Our Vision</h3>
                        <p>To be the leading education provider, recognized for excellence in teaching, innovation, and student success.</p>
                        <ul class="text-start mt-3">
                            <li>Become industry leader in skill development</li>
                            <li>Expand to multiple locations</li>
                            <li>Partner with top companies for placements</li>
                            <li>Continuously innovate our teaching methods</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Core Values -->
    <section class="py-5">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Our Core Values</h2>
                <p>The principles that guide everything we do</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4>Excellence</h4>
                        <p>We strive for excellence in everything we do, from curriculum design to student support.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4>Integrity</h4>
                        <p>We maintain the highest standards of honesty and ethical conduct in all our operations.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4>Innovation</h4>
                        <p>We embrace new technologies and teaching methods to stay ahead of the curve.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Community</h4>
                        <p>We build a supportive learning community where everyone can thrive.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Team Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Meet Our Team</h2>
                <p>Dedicated professionals committed to your success</p>
            </div>
            
            <div class="row g-4">
                <?php
                $team = [
                    ['name' => 'Dr. Rajesh Kumar', 'role' => 'Founder & Director', 'icon' => 'user-tie'],
                    ['name' => 'Priya Sharma', 'role' => 'Head of Academics', 'icon' => 'user-graduate'],
                    ['name' => 'Amit Patel', 'role' => 'Lead Instructor - Web Dev', 'icon' => 'code'],
                    ['name' => 'Neha Singh', 'role' => 'Lead Instructor - Design', 'icon' => 'palette']
                ];
                
                $delay = 100;
                foreach ($team as $member):
                ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-<?php echo $member['icon']; ?>"></i>
                        </div>
                        <h5><?php echo $member['name']; ?></h5>
                        <p class="text-muted"><?php echo $member['role']; ?></p>
                    </div>
                </div>
                <?php 
                    $delay += 100;
                endforeach; 
                ?>
            </div>
        </div>
    </section>
    
    <!-- Why Choose Us -->
    <section class="py-5">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Why Students Choose Us</h2>
                <p>What makes Next Academy different</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success fs-3 me-3"></i>
                        <div>
                            <h5>Industry-Recognized Curriculum</h5>
                            <p>Our courses are designed in consultation with industry leaders to ensure relevance.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success fs-3 me-3"></i>
                        <div>
                            <h5>Flexible Learning Options</h5>
                            <p>Choose from various course durations and schedules that fit your lifestyle.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success fs-3 me-3"></i>
                        <div>
                            <h5>Lifetime Support</h5>
                            <p>Get continued support even after course completion through our alumni network.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success fs-3 me-3"></i>
                        <div>
                            <h5>State-of-the-Art Facilities</h5>
                            <p>Learn in modern classrooms with the latest technology and equipment.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success fs-3 me-3"></i>
                        <div>
                            <h5>Affordable Fees</h5>
                            <p>Quality education at competitive prices with flexible payment options.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success fs-3 me-3"></i>
                        <div>
                            <h5>Job Placement Assistance</h5>
                            <p>We help you land your dream job with our extensive industry connections.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>