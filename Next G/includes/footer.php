<footer class="footer-public">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5><i class="fas fa-graduation-cap"></i> Next Academy</h5>
                <p>Empowering students with quality education and practical skills for a successful future.</p>
                <div class="social-links mt-3">
                    <a href="https://www.facebook.com/nextacademykalol/"><i class="fab fa-facebook-f"></i></a>
                    <!--<a href="#"><i class="fab fa-twitter"></i></a>-->
                    <a href="https://www.instagram.com/next.academy.india"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4">
                <h5>Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="courses.php">Courses</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Popular Courses</h5>
                <ul class="footer-links">
                    <li><a href="courses.php">Web Development</a></li>
                    <li><a href="courses.php">Graphic Design</a></li>
                    <li><a href="courses.php">Digital Marketing</a></li>
                    <li><a href="courses.php">Mobile Development</a></li>
                    <li><a href="courses.php">Data Science</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Contact Info</h5>
                <ul class="footer-links">
                    <li class="d-flex gap-2"><i class="fas fa-map-marker-alt mt-2"></i><p> Next Academy City Mall-2, SF14, inside Navjivan Bazar Road, Navjivan Mill Compound, Memon Market,
Kalol,Gujarat - 382721,
India</p></li>
                    <li><i class="fas fa-phone"></i> +91 97379 49789</li>
                    <!--<li><i class="fas fa-envelope"></i> info@nextacademy.com</li>-->
                    <li><i class="fas fa-clock"></i> Mon - Sat: 9AM - 6PM</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Next Academy. All rights reserved. | Designed with <i class="fas fa-heart text-danger"></i> for Education</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    // Initialize AOS animations
    AOS.init({
        duration: 800,
        once: true
    });
    
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
</body>
</html>