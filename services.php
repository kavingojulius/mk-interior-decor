<?php
require_once './config/config.php';

// Fetch services
$query = $conn->query("SELECT service_name, description FROM services");
$services = $query->fetchAll(PDO::FETCH_ASSOC);
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
        
            <section class="services-section section-padding" id="services">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-12 text-center mb-5">
                            <small class="section-small-title">Our Premium Services</small>
                            <h2 class="mt-2"><span class="tooplate-red">Interior Design</span> Services</h2>
                        </div>

                        <!-- Service List -->
                        <div class="col-12">
                            <div class="row">
                                <?php if (!empty($services)): ?>
                                    <?php foreach ($services as $index => $service): ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="service-item-container h-100">
                                            <div class="service-item d-flex justify-content-between align-items-center h-100">
                                                <div class="d-flex align-items-center">
                                                    <span class="service-number me-2"><?= $index + 1 ?>.</span>
                                                    <h5 class="mb-0"><?= htmlspecialchars($service['service_name']) ?></h5>
                                                </div>
                                                <button type="button" class="view-btn border-0" data-bs-toggle="modal" data-bs-target="#serviceModal" 
                                                    data-name="<?= htmlspecialchars($service['service_name']) ?>"
                                                    data-description="<?= htmlspecialchars($service['description']) ?>">
                                                    View <i class="bi bi-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-5">
                                        <div class="alert alert-info">
                                            Currently, there are no services available. Please check back later.
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <style>
                
            </style>

            <!-- Simple Modal -->
            <div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="serviceModalLabel">Service Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h6>Description:</h6>
                            <p id="modalServiceDescription"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Minimal JavaScript -->
            <script>
                document.getElementById('serviceModal').addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    this.querySelector('.modal-title').textContent = button.getAttribute('data-name');
                    this.querySelector('#modalServiceDescription').textContent = button.getAttribute('data-description');
                });
            </script>

            <style>
                /* Services Section */
                .services-section {
                    background-color: #f9f9f9;
                }

                .service-item-container {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                    position: relative;
                    overflow: hidden;
                    height: 100%;
                }

                .service-item-container::before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0;
                    height: 100%;
                    width: 4px;
                    background-color: #4CAF50; /* Green accent color */
                }

                .service-item {
                    padding: 15px 20px;
                    transition: all 0.3s ease;
                }

                .service-item:hover {
                    background-color: #f5f5f5;                                        
                }

                .service-item h5 {
                    font-family: 'Hepta Slab', serif;
                    font-weight: 500;
                    color: #333;
                    font-size: 1rem;
                    margin-bottom: 0;
                }

                .view-btn {
                    color: var(--tooplate-red);
                    text-decoration: none;
                    font-weight: 500;
                    padding: 3px 10px;
                    border-radius: 4px;
                    transition: all 0.3s ease;
                    font-size: 0.85rem;
                }

                .view-btn:hover {
                    background-color:#4CAF50;
                    color: white;
                }
                .service-number {
                    color: var(--tooplate-red);
                    font-weight: bold;
                    font-size: 1rem;
                }

                @media (max-width: 768px) {
                    .service-item {
                        padding: 12px 15px;
                    }
                    
                    .service-item h5 {
                        font-size: 0.95rem;
                    }
                    
                    .view-btn {
                        font-size: 0.8rem;
                        padding: 2px 8px;
                    }
                }
            </style>

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

    </body>
</html>