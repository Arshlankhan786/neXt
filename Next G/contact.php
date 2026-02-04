<?php
require_once 'admin/config/database.php';

// Get active courses
$courses = $conn->query("SELECT id, name FROM courses WHERE status = 'Active' ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Next Academy</title>
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
            <h1 data-aos="fade-up">Contact Us</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="100">We'd love to hear from you</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Contact Form -->
                <div class="col-lg-7" data-aos="fade-right">
                    <div class="contact-form">
                        <h3 class="mb-4">Contact Us</h3>

                        <div id="alertMessage"></div>

                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <!--<label class="form-label">Full Name *</label>-->
                                    <input type="text" placeholder="Full Name" class="form-control" name="full_name" id="full_name" required>
                                </div>

                                <div class="col-md-6">
                                    <!--<label class="form-label">Contact Number *</label>-->
                                    <input type="tel" class="form-control" name="phone" id="phone"  placeholder="Contact Number"
                                           pattern="[0-9]{10}" title="10-digit mobile number" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Course Interested *</label>
                                    <select class="form-select" name="subject" id="subject" required>
                                        <option value="">Select Course</option>
                                        <?php while ($course = $courses->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($course['name']); ?>">
                                            <?php echo htmlspecialchars($course['name']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Message *</label>
                                    <textarea class="form-control" name="message" id="message" rows="5" required></textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-purple btn-lg" id="submitBtn">
                                        <i class="fas fa-paper-plane"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="contact-info">
                        <h3 class="mb-4">Get in Touch</h3>
                        
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h5>Address</h5>
                                <p>Next Academy  City Mall-2, SF14, inside Navjivan Bazar Road, Navjivan Mill Compound, Memon Market, <br> Kalol,Gujarat - 382721, <br>India</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h5>Phone</h5>
                                <p>+91 97379 49789</p>
                            </div>
                        </div>
                        
                        <!--<div class="contact-item">-->
                        <!--    <i class="fas fa-envelope"></i>-->
                        <!--    <div>-->
                        <!--        <h5>Email</h5>-->
                        <!--        <p>info@nextacademy.com<br>admissions@nextacademy.com</p>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h5>Office Hours</h5>
                                <p>Monday - Saturday<br>9:30 AM - 8:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Follow Us</h5>
                            <div class="social-links">
                                <a href="https://www.facebook.com/nextacademykalol/"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://www.instagram.com/next.academy.india"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                <a href="#"><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-5 bg-light">
        <div class="container" data-aos="fade-up">
            <h3 class="text-center mb-4">Find Us on Map</h3>
            <div class="ratio ratio-21x9">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29327.958442416875!2d72.49466375!3d23.2432761!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395c25aa9ecc4bef%3A0x5cfe4d1832b5f56e!2sNext%20Academy%20%7C%20Coding%2C%20AI%2C%20Digital%20Marketing%2C%20Web%20Development%20%26%20Graphic%20Design%20Class%20%7C%20Best%20Computer%20Class%20in%20Kalol!5e0!3m2!1sen!2sin!4v1769408923628!5m2!1sen!2sin" 
                        style="border:0; border-radius: 15px;" 
                        allowfullscreen="" 
                        loading="lazy"></iframe>
                      
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        fetch('inquiry_submit.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const alertDiv = document.getElementById('alertMessage');
            
            if (data.success) {
                alertDiv.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                document.getElementById('contactForm').reset();
            } else {
                alertDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
            }
            
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Scroll to alert
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        })
        .catch(error => {
            document.getElementById('alertMessage').innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> Something went wrong. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    </script>
</body>

</html>