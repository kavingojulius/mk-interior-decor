
<?php
// Start session and include database connection at the top
session_start();
require_once './config/config.php';

// Initialize variables for consultation form
$consultationEmail = $consultationRequest = '';
$consultationSuccess = $consultationError = '';

// Process consultation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['consultation_submit'])) {
    // Check if form was already submitted
    if (isset($_SESSION['form_submitted']) && $_SESSION['form_submitted'] === true) {
        // Form was already submitted, redirect to prevent resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    // Sanitize and validate input
    $consultationEmail = filter_input(INPUT_POST, 'consultation_email', FILTER_SANITIZE_EMAIL);
    $consultationRequest = filter_input(INPUT_POST, 'consultation_request', FILTER_SANITIZE_STRING);
    
    // Validate email
    if (!filter_var($consultationEmail, FILTER_VALIDATE_EMAIL)) {
        $consultationError = 'Please enter a valid email address.';
    } elseif (empty($consultationRequest)) {
        $consultationError = 'Please enter your request.';
    } else {
        try {
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO consultation_requests (email, request_text) VALUES (:email, :request_text)");
            
            // Bind parameters
            $stmt->bindParam(':email', $consultationEmail);
            $stmt->bindParam(':request_text', $consultationRequest);
            
            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['form_submitted'] = true;
                $_SESSION['success_message'] = 'Your consultation request has been submitted successfully! We will contact you soon.';
                // Redirect to clear POST data
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                $consultationError = 'There was an error submitting your request. Please try again.';
            }
        } catch(PDOException $e) {
            $consultationError = 'Database error: ' . $e->getMessage();
        }
    }
}

// Check for success message in session
if (isset($_SESSION['success_message'])) {
    $consultationSuccess = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying
}

// Clear form submission flag if not submitting
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['form_submitted']);
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="description" content="">
        
        <meta name="author" content="Mk Interior & Decor">

        <title>MK Interior & Decor</title>

        <!-- CSS FILES -->

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Hepta+Slab:wght@1..900&display=swap" rel="stylesheet">                
        
        <link href="./css/bootstrap.min.css" rel="stylesheet">

        <link href="./css/bootstrap-icons.css" rel="stylesheet">

        <link href="./css/owl.carousel.min.css" rel="stylesheet">

        <link href="./css/styles.css" rel="stylesheet">
        

    </head>
    
    <body>

        <!-- Start Nav bar -->

        <?php include_once './includes/navbar.php'; ?>

        <!-- End Nav bar -->

        <main>

        <!-- Start Hero section -->

            <section class="hero-section hero-slide d-flex justify-content-center align-items-center" id="section_1">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-8 col-12 text-center mx-auto">
                            <div class="hero-section-text">
                                <small class="section-small-title">Welcome to MK Interior & Decor<i class="hero-icon bi-house"></i></small>

                                <h3 class="hero-title text-dark mt-2 mb-4">Stylish Interiors, Personalized for Your Taste</h3>

                                <form class="custom-form hero-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" role="form">
                                    <div class="row">
                                        <h4>Free Design Consultation</h4>
                                        
                                        <?php if (!empty($consultationSuccess)): ?>
                                            <div class="col-12">
                                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                    <?php echo $consultationSuccess; ?>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            </div>
                                        <?php elseif (!empty($consultationError)): ?>
                                            <div class="col-12">
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <?php echo $consultationError; ?>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="col-lg-5 col-md-6 col-12">                                            
                                            <div class="input-group align-items-center">                                                
                                                <input type="email" name="consultation_email" id="consultation_email" class="form-control" 
                                                    placeholder="Input your email" required
                                                    value="<?php echo htmlspecialchars($consultationEmail); ?>">                                                
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-md-6 col-12">
                                            <div class="input-group align-items-center">
                                                <input type="text" name="consultation_request" id="consultation_request" class="form-control" 
                                                    placeholder="Input your request" required
                                                    value="<?php echo htmlspecialchars($consultationRequest); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 col-12">
                                            <button type="submit" name="consultation_submit" class="form-control">Send Request</button>
                                        </div>
                                    </div>
                                </form>

                                <div class="hero-btn d-flex justify-content-center align-items-center">
                                    <a class="bi-arrow-down hero-btn-link smoothscroll" href="#section_2"></a>
                                </div> 
                            </div>
                        </div>

                    </div>
                </div>
            </section>

        <!-- End Hero section -->

            <section class="about-section section-padding" id="section_2">
                <div class="container">
                    <div class="row align-items-center">

                        <div class="col-lg-5 col-12">
                            <small class="section-small-title">Our Expertise</small>

                            <h3 class="mt-2 mb-4 fst-oblique">
                                <span class="text-muted">Defining</span> MK Interior & Decor
                            </h3>

                            <p class="text-muted mb-3 fw-bold">
                                Where Vision Meets Masterful Design
                            </p>

                            <p class="text-muted" style="font-style: oblique;">
                                At MK Interior & Decor, we blend artistic vision with meticulous craftsmanship to create spaces that inspire. 
                                Our team of passionate designers and artisans work closely with you, ensuring every detail reflects your unique style. 
                                From concept to execution, we deliver timeless elegance, innovative solutions, and uncompromising quality—because exceptional interiors begin with exceptional care.
                            </p>
                        </div>

                        <div class="col-lg-3 col-md-5 col-5 mx-lg-auto">
                            <img src="./images/sharing-design-ideas-with-family.jpg" class="about-image about-image-small img-fluid" alt="">
                        </div>

                        <div class="col-lg-4 col-md-7 col-7">
                            <img src="./images/living-room-interior-wall-mockup-warm-tones-with-leather-sofa-which-is-kitchen-3d-rendering.jpg" class="about-image img-fluid" alt="">
                        </div>

                    </div>
                </div>
            </section>


            <section class="featured-section section-padding">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-5 col-12">
                            <div class="custom-block featured-custom-block">
                                <h2 class="mt-2 mb-4">Opening Hours</h2>

                                <div class="d-flex">
                                    <i class="featured-icon bi-clock me-3"></i>
                                    
                                    <div>
                                        <p class="mb-2">
                                            Mon - Friday ~
                                            <strong class="d-inline">
                                                8:00 AM - 6:00 PM
                                            </strong>
                                        </p>

                                        <p class="mb-2">
                                            Saturday ~
                                            <strong class="d-inline">
                                                10:00 AM - 10:00 PM
                                            </strong>
                                        </p>

                                        <p>Sunday ~ Closed</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>


            <section class="shop-section section-padding" id="section_3">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-12 col-12">
                            <small class="section-small-title">MK Interior Design Shop</small>

                            <h2 class="mt-2 mb-4"><span class="tooplate-red">Interior</span> Products</h2>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="shop-thumb">
                                <div class="shop-image-wrap">
                                    <a href="shop-detail.html">
                                        <img src="./images/shop/minimal-bathroom-interior-design-with-wooden-furniture.jpg" class="shop-image img-fluid" alt="">
                                    </a>

                                    <div class="shop-icons-wrap">
                                        <div class="shop-icons d-flex flex-column align-items-center">
                                            <a href="#" class="shop-icon bi-eye"></a>

                                            
                                        </div>

                                        <p class="shop-pricing mb-0 mt-3">
                                            <span class="badge custom-badge">View</span>
                                        </p>
                                    </div>

                                    <div class="shop-btn-wrap">
                                        <a href="./shop-detail" class="shop-btn custom-btn btn d-flex align-items-center align-items-center">Learn more</a>
                                    </div>
                                </div>

                                <div class="shop-body">
                                    <h4>Bathroom</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="shop-thumb">
                                <div class="shop-image-wrap">
                                    <a href="shop-detail.html">
                                        <img src="./images/shop/mock-up-poster-modern-dining-room-interior-design-with-white-empty-wall.jpg" class="shop-image img-fluid" alt="">
                                    </a>

                                    <div class="shop-icons-wrap">
                                        <div class="shop-icons d-flex flex-column align-items-center">
                                            <a href="#" class="shop-icon bi-eye"></a>                                            
                                        </div>

                                        <p class="shop-pricing mb-0 mt-3">
                                            <span class="badge custom-badge">View</span>
                                        </p>
                                    </div>

                                    <div class="shop-btn-wrap">
                                        <a href="./shop-detail" class="shop-btn custom-btn btn d-flex align-items-center align-items-center">Learn more</a>
                                    </div>
                                </div>

                                <div class="shop-body">
                                    <h4>Dining</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="shop-thumb">
                                <div class="shop-image-wrap">
                                    <a href="./shop-detail">
                                        <img src="./images/shop/green-sofa-white-living-room-with-blank-table-mockup.jpg" class="shop-image img-fluid" alt="">
                                    </a>

                                    <div class="shop-icons-wrap">
                                        <div class="shop-icons d-flex flex-column align-items-center">
                                            <a href="#" class="shop-icon bi-eye"></a>                                            
                                        </div>

                                        <p class="shop-pricing mb-0 mt-3">
                                            <span class="badge custom-badge">View</span>
                                        </p>
                                    </div>

                                    <div class="shop-btn-wrap">
                                        <a href="./shop-detail" class="shop-btn custom-btn btn d-flex align-items-center align-items-center">Learn more</a>
                                    </div>
                                </div>

                                <div class="shop-body">
                                    <h4>Living Room</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="shop-thumb">
                                <div class="shop-image-wrap">
                                    <a href="./shop-detail">
                                        <img src="./images/shop/concept-home-cooking-with-female-chef.jpg" class="shop-image img-fluid" alt="">
                                    </a>

                                    <div class="shop-icons-wrap">
                                        <div class="shop-icons d-flex flex-column align-items-center">
                                            <a href="#" class="shop-icon bi-eye"></a>                                            
                                        </div>

                                        <p class="shop-pricing mb-0 mt-3">
                                            <span class="badge custom-badge">View</span>
                                        </p>
                                    </div>

                                    <div class="shop-btn-wrap">
                                        <a href="./shop-detail" class="shop-btn custom-btn btn d-flex align-items-center align-items-center">Learn more</a>
                                    </div>
                                </div>

                                <div class="shop-body">
                                    <h4>Chef Kitchen</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="shop-thumb">
                                <div class="shop-image-wrap">
                                    <a href="./shop-detail">
                                        <img src="./images/shop/childrens-bed-nursery-cot-velvet-childrens-room.jpg" class="shop-image img-fluid" alt="">
                                    </a>

                                    <div class="shop-icons-wrap">
                                        <div class="shop-icons d-flex flex-column align-items-center">
                                            <a href="#" class="shop-icon bi-eye"></a>                                            
                                        </div>

                                        <p class="shop-pricing mb-0 mt-3">
                                            <span class="badge custom-badge">View</span>
                                        </p>
                                    </div>

                                    <div class="shop-btn-wrap">
                                        <a href="./shop-detail" class="shop-btn custom-btn btn d-flex align-items-center align-items-center">Learn more</a>
                                    </div>
                                </div>

                                <div class="shop-body">
                                    <h4>Childrens Bedroom</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-12">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item">
                                        <a class="page-link" href="#" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    <li class="page-item active" aria-current="page">
                                        <a class="page-link" href="#">1</a>
                                    </li>
                                    
                                    <li class="page-item">
                                        <a class="page-link" href="#">2</a>
                                    </li>
                                    
                                    <li class="page-item">
                                        <a class="page-link" href="#">3</a>
                                    </li>

                                    <li class="page-item">
                                        <a class="page-link" href="#">4</a>
                                    </li>

                                    <li class="page-item">
                                        <a class="page-link" href="#">5</a>
                                    </li>
                                    
                                    <li class="page-item">
                                        <a class="page-link" href="#" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>

                    </div>
                </div>
            </section>

        <!-- Reviews Section -->

            <section class="reviews-section section-padding pb-0" id="section_4">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-12 col-12">
                            <small class="section-small-title">Happy customers.</small>

                            <h2 class="mt-2 mb-4">Reviews</h2>

                            <div class="owl-carousel reviews-carousel">

                                <div class="reviews-thumb">
                                    <div class="reviews-body">
                                        <p class="fw-bold">MK Interior is the most suitable website layout.</p>
                                    </div>

                                    <div class="reviews-bottom reviews-bottom-up d-flex align-items-center">
                                        <img src="./images/avatar/pretty-blonde-woman-wearing-white-t-shirt.jpg" class="avatar-image img-fluid" alt="">

                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100 ms-3">
                                            <p class="text-white mb-0">
                                                <strong>Sandy</strong>, <small>CEO</small>
                                            </p>

                                            <div class="reviews-icons">
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="reviews-thumb">
                                    <div class="reviews-body">
                                        <p class="fw-bold">Explore more HTML Templates to download for your website.</p>
                                    </div>

                                    <div class="reviews-bottom reviews-bottom-up d-flex align-items-center">
                                        <img src="./images/avatar/studio-portrait-emotional-happy-funny-smiling-boyfriend-man-with-heavy-beard-stands-with-arms-crossed-dressed-red-t-shirt-isolated-blue.jpg" class="avatar-image img-fluid" alt="">

                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100 ms-3">
                                            <p class="text-white mb-0">
                                                <strong>Jack</strong>, <small>Partner</small>
                                            </p>

                                            <div class="reviews-icons">
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star"></i>
                                                <i class="bi-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="reviews-thumb">
                                    <div class="reviews-body">
                                        <p class="fw-bold">Please recommend Tooplate website to your friends.</p>
                                    </div>

                                    <div class="reviews-bottom reviews-bottom-up d-flex align-items-center">
                                        <img src="./images/avatar/portrait-beautiful-young-woman-standing-grey-wall.jpg" class="avatar-image img-fluid" alt="">

                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100 ms-3">
                                            <p class="text-white mb-0">
                                                <strong>Helen</strong>, <small>Client</small>
                                            </p>

                                            <div class="reviews-icons">
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="reviews-thumb">
                                    <div class="reviews-body">
                                        <p class="fw-bold">This Bootstrap 5 layout is free to use for your website.</p>
                                    </div>

                                    <div class="reviews-bottom reviews-bottom-up d-flex align-items-center">
                                        <img src="./images/avatar/portrait-young-redhead-bearded-male-wears-white-t-shirt-keeps-his-eyes-closed-smiling-feels-happy-yellow.jpg" class="avatar-image img-fluid" alt="">

                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100 ms-3">
                                            <p class="text-white mb-0">
                                                <strong>Bill</strong>, <small>Designer</small>
                                            </p>

                                            <div class="reviews-icons">
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star"></i>
                                                <i class="bi-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="reviews-thumb">
                                    <div class="reviews-body">
                                        <p class="fw-bold">Thank you for visiting Tooplate to download free templates.</p>
                                    </div>

                                    <div class="reviews-bottom reviews-bottom-up d-flex align-items-center">
                                        <img src="./images/avatar/portrait-young-beautiful-woman-gesticulating.jpg" class="avatar-image img-fluid" alt="">

                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100 ms-3">
                                            <p class="text-white mb-0">
                                                <strong>Susan</strong>, <small>Boss</small>
                                            </p>

                                            <div class="reviews-icons">
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12">
                                <p class="d-flex justify-content-center align-items-center mt-lg-5">Write some reviews on <a href="#" class="custom-btn btn ms-3"><i class="bi-facebook me-2"></i>facebook</a></p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

        <!-- End Reviews Section -->


        <!-- Contact Section -->
        <section class="contact-section" id="section_5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#f9f9f9" fill-opacity="1" d="M0,96L40,117.3C80,139,160,181,240,186.7C320,192,400,160,480,149.3C560,139,640,149,720,176C800,203,880,245,960,250.7C1040,256,1120,224,1200,229.3C1280,235,1360,277,1400,298.7L1440,320L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z"></path></svg>
            <div class="container">
                <div class="row">

                    <div class="col-lg-6 col-12">
                        <div class="pe-lg-4">
                            <h2 class="mb-4">Let's Create Something Beautiful Together</h2>
                            <p class="lead mb-4">At MK Interior & Decor, we believe every space tells a story. Yours should be extraordinary.</p>
                            <p>Whether you're dreaming of a complete transformation or seeking the perfect finishing touches, we're here to bring your vision to life.</p>
                            
                            <div class="mt-5">
                                <a href="./contact" class="btn btn-success btn-lg">Get in Touch</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12 mt-5 mt-lg-0">
                        <div class="custom-block">
                            <h3 class="text-white mb-3">Find Us Here</h3>
                            <p class="text-white mb-4">
                                <i class="contact-icon bi-geo-alt me-1"></i>
                                Moi Avenue, Mombasa, Kenya
                            </p>
                            
                            <iframe class="google-map mt-2" src="https://www.google.com/maps/embed?pb=!1m24!1m12!1m3!1d4081156.056182239!2d35.78666322322672!3d-2.664761619041379!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m9!3e6!4m3!3m2!1d-1.2907412999999999!2d37.2684145!4m3!3m2!1d-4.0499467!2d39.6666512!5e0!3m2!1sen!2ske!4v1752113662671!5m2!1sen!2ske" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <!-- End Contact Section -->

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#36363e" fill-opacity="1" d="M0,96L40,117.3C80,139,160,181,240,186.7C320,192,400,160,480,149.3C560,139,640,149,720,176C800,203,880,245,960,250.7C1040,256,1120,224,1200,229.3C1280,235,1360,277,1400,298.7L1440,320L1440,320L1400,320C1360,320,1280,320,1200,320C1120,320,1040,320,960,320C880,320,800,320,720,320C640,320,560,320,480,320C400,320,320,320,240,320C160,320,80,320,40,320L0,320Z"></path></svg>            


        </main>

        
        <!-- Start Footer -->

        <?php include_once './includes/footer.php'; ?>

        <!-- End Footer -->

        <!-- JAVASCRIPT FILES -->
        <script src="./js/jquery.min.js"></script>
        <script src="./js/bootstrap.min.js"></script>
        <script src="./js/click-scroll.js"></script>
        <script src="./js/jquery.backstretch.min.js"></script>
        <script src="./js/owl.carousel.min.js"></script>
        <script src="./js/custom.js"></script> 
        
        
        <script>
            $(document).ready(function() {
                // Auto-dismiss success alert after 5 seconds
                setTimeout(function() {
                    $('.alert-success').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 3000);
                
                // Auto-dismiss error alert after 8 seconds
                setTimeout(function() {
                    $('.alert-danger').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 3000);
            });
        </script>

    </body>
</html>