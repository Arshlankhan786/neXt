
<?php

require_once 'admin/config/database.php';

// Get all active gallery items
$gallery_items = $conn->query("SELECT * FROM gallery WHERE status = 'active' ORDER BY display_order, created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Next Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="assets/css/public-style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Page Header -->
    <section class="hero-section" style="min-height: 300px;">
        <div class="container text-center">
            <h1 data-aos="fade-up">Our Gallery</h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="100">Take a visual tour of our campus and facilities</p>
        </div>
    </section>
    
    <!-- Gallery Section -->
    <section class="py-5">
        <div class="container">
            <?php if ($gallery_items->num_rows > 0): ?>
            <div class="gallery-grid">
                <?php 
                $delay = 100;
                while ($item = $gallery_items->fetch_assoc()): 
                    // For demo, using placeholder images
                    $image_url = file_exists('../' . $item['image_path']) 
                        ? '../' . $item['image_path'] 
                        : 'https://via.placeholder.com/400x300/7c3aed/ffffff?text=' . urlencode($item['title']);
                ?>
                <div class="gallery-item" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <?php if ($item['type'] === 'image'): ?>
                    <a href="<?php echo $image_url; ?>" data-lightbox="gallery" data-title="<?php echo htmlspecialchars($item['title']); ?>">
                        <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="gallery-overlay">
                            <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                            <?php if ($item['description']): ?>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php else: ?>
                    <!-- Video item -->
                    <div class="position-relative">
                        <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="gallery-overlay">
                            <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                            <?php if ($item['video_url']): ?>
                            <a href="<?php echo htmlspecialchars($item['video_url']); ?>" target="_blank" class="btn btn-light btn-sm mt-2">
                                <i class="fas fa-play"></i> Watch Video
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php 
                    $delay += 50;
                endwhile; 
                ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-images fa-5x text-muted mb-3"></i>
                <h4>Gallery Coming Soon</h4>
                <p class="text-muted">We're currently updating our gallery. Check back soon!</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Virtual Tour CTA -->
    <section class="py-5 bg-light">
        <div class="container text-center" data-aos="zoom-in">
            <h2 class="mb-4">Want to Visit Our Campus?</h2>
            <p class="lead mb-4">Schedule a campus tour and see our facilities in person</p>
            <a href="contact.php" class="btn btn-purple btn-lg">
                <i class="fas fa-calendar-check"></i> Schedule a Visit
            </a>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': 'Image %1 of %2'
        });
    </script>
</body>
</html>