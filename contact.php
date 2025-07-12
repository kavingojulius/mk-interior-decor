<?php
session_start();
require_once './config/config.php';

// Initialize variables
$fullName = $phone = $email = $message = '';
$successMessage = $errorMessage = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact_submit'])) {
    // Sanitize and validate input
    $fullName = filter_input(INPUT_POST, 'full-name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Validate inputs
    if (empty($fullName)) {
        $errorMessage = 'Please enter your full name.';
    } elseif (empty($phone) && empty($email)) {
        $errorMessage = 'Please provide either a phone number or email address.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } elseif (empty($message)) {
        $errorMessage = 'Please enter your message.';
    } else {
        try {
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO contact_messages 
                                   (full_name, phone, email, message) 
                                   VALUES (:full_name, :phone, :email, :message)");
            
            // Bind parameters
            $stmt->bindParam(':full_name', $fullName);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);
            
            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Your message has been sent successfully! We will get back to you soon.';
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                $errorMessage = 'There was an error sending your message. Please try again.';
            }
        } catch(PDOException $e) {
            $errorMessage = 'Database error: ' . $e->getMessage();
            // For debugging only - remove in production
            error_log($e->getMessage());
        }
    }
}

// Check for success message in session
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="description" content="">
        <meta name="author" content="MK interior">

        <title>MK Interior & Decor - contact</title>

        <!-- CSS FILES -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Hepta+Slab:wght@1..900&display=swap" rel="stylesheet">                
        
        <link href="./css/bootstrap.min.css" rel="stylesheet">
        <link href="./css/bootstrap-icons.css" rel="stylesheet">
        <link href="./css/owl.carousel.min.css" rel="stylesheet">
        <link href="./css/styles.css" rel="stylesheet">
        
        <style>
            .alert {
                transition: opacity 0.5s ease-out;
            }
        </style>
    </head>
    
    <body>
        <!-- Start Nav bar -->
        <?php include_once './includes/navbar.php' ?>
        <!-- End Nav bar -->

        <main>
            <!-- Contact Section -->
            <section class="contact-section" id="">
                
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <small class="section-small-title">Ask anything.</small>
                            <h2 class="mb-4">Say Hello</h2>
                        </div>

                        <div class="col-lg-6 col-12">
                            <?php if (!empty($successMessage)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $successMessage; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php elseif (!empty($errorMessage)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $errorMessage; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form class="custom-form contact-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" role="form">
                                <div class="input-group align-items-center">
                                    <!-- <label for="full-name"></label> -->
                                    <input type="text" name="full-name" id="full-name" class="form-control" 
                                           placeholder="Full Name" required
                                           value="<?php echo htmlspecialchars($fullName); ?>">
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="input-group align-items-center">
                                            <!-- <label for="phone"></label> -->
                                            <input type="tel" name="phone" id="phone" class="form-control" 
                                                   placeholder="Phone Number , ie ,0718 473019"
                                                   value="<?php echo htmlspecialchars($phone); ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="input-group align-items-center">
                                            <!-- <label for="email"></label> -->
                                            <input type="email" name="email" id="email" class="form-control"
                                                    pattern="[^ @]*@[^ @]*" 
                                                   placeholder="email address"
                                                   value="<?php echo htmlspecialchars($email); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group textarea-group">
                                    <label for="message">Message</label>
                                    <textarea name="message" rows="6" class="form-control" id="message" 
                                              placeholder="What can we help you?" required><?php echo htmlspecialchars($message); ?></textarea>
                                </div>

                                <div class="col-lg-3 col-md-4 col-6">
                                    <button type="submit" name="contact_submit" class="form-control">Send</button>
                                </div>
                            </form>
                            
                        </div>

                        <div class="col-lg-6 col-12 mt-5 mt-lg-0">
                            <div class="custom-block">
                                <h3 class="text-white mb-2">Location</h3>
                                <p class="text-white mb-2">
                                    <i class="contact-icon bi-geo-alt me-1"></i>
                                    Moi Avenue, Mombasa, Kenya
                                </p>

                                <h3 class="text-white mt-3 mb-2">Contact Info</h3>
                                <div class="d-flex flex-wrap">
                                    <p class="text-white mb-2 me-4">
                                        <i class="contact-icon bi-telephone me-1"></i>
                                        <a href="tel: +254 718 473019" class="text-white">
                                            0718 473019
                                        </a>
                                    </p>

                                    <p class="text-white">
                                        <i class="contact-icon bi-envelope me-1"></i>
                                        <a href="mailto:michaelkimanthi02@gmail.com" class="text-white">
                                            michaelkimanthi02@gmail.com
                                        </a>
                                    </p>
                                </div>
                                
                                <!-- Map -->                                 
                                <iframe class="google-map mt-2" src="https://www.google.com/maps/embed?pb=!1m24!1m12!1m3!1d4081156.056182239!2d35.78666322322672!3d-2.664761619041379!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m9!3e6!4m3!3m2!1d-1.2907412999999999!2d37.2684145!4m3!3m2!1d-4.0499467!2d39.6666512!5e0!3m2!1sen!2ske!4v1752113662671!5m2!1sen!2ske" width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End Contact Section -->
            
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
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        </script>
    </body>
</html>