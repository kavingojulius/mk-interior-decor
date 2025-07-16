<?php
session_start();
require_once '../config/config.php';

// Check if admin is logged in
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit();
// }

// Fetch services from database
$services = [];
try {
    $stmt = $conn->query("SELECT * FROM services ORDER BY id DESC");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM services WHERE id = :id");
        $stmt->bindParam(':id', $delete_id);
        $stmt->execute();
        $_SESSION['success_message'] = "Service deleted successfully!";
        header("Location: services.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error deleting service: " . $e->getMessage();
    }
}

// Handle create/update service
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_service'])) {
        // Create new service
        $service_name = $_POST['service_name'];
        $description = $_POST['description'];
        
        try {
            $stmt = $conn->prepare("INSERT INTO services (service_name, description) VALUES (:service_name, :description)");
            $stmt->bindParam(':service_name', $service_name);
            $stmt->bindParam(':description', $description);
            $stmt->execute();
            $_SESSION['success_message'] = "Service created successfully!";
            header("Location: services.php");
            exit();
        } catch(PDOException $e) {
            $error = "Error creating service: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_service'])) {
        // Update existing service
        $service_id = $_POST['service_id'];
        $service_name = $_POST['service_name'];
        $description = $_POST['description'];
        
        try {
            $stmt = $conn->prepare("UPDATE services SET service_name = :service_name, description = :description WHERE id = :id");
            $stmt->bindParam(':service_name', $service_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $service_id);
            $stmt->execute();
            $_SESSION['success_message'] = "Service updated successfully!";
            header("Location: services.php");
            exit();
        } catch(PDOException $e) {
            $error = "Error updating service: " . $e->getMessage();
        }
    }
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

        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-icons.css" rel="stylesheet">
        <link href="css/apexcharts.css" rel="stylesheet">
        <link href="css/styles.css" rel="stylesheet">

        <style>
            .action-btn {
                padding: 5px 10px;
                margin: 0 2px;
                font-size: 0.8rem;
            }
            .description-cell {
                max-width: 300px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>
    </head>
    
    <body>

        <header class="navbar sticky-top flex-md-nowrap">
            <div class="col-md-3 col-lg-3 me-0 px-3 fs-6">
                <a class="navbar-brand" href="index.html">
                    <i class="bi-box"></i>
                    Mk Interior & Decor
                </a>
            </div>

            <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-nav me-lg-2">
                <div class="nav-item text-nowrap d-flex align-items-center">
                    
                </div>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row">
                
                <?php include_once './sidebar.php'; ?>

                <main class="main-wrapper col-md-9 ms-sm-auto py-4 col-lg-9 px-md-4 border-start">
                    <div class="title-group mb-3">
                        <span class="h4 mb-0">Services Management</span>
                        <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="bi-plus"></i> Add New Service
                        </button>
                    </div>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row my-4">
                        <div class="col-lg-12 col-12">
                            <div class="custom-block bg-white">
                                <div class="table-responsive">
                                    <table class="account-table table">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Service Name</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($services as $index => $service): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                                <td class="description-cell" title="<?php echo htmlspecialchars($service['description']); ?>">
                                                    <?php echo htmlspecialchars($service['description']); ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary action-btn view-btn" data-bs-toggle="modal" data-bs-target="#viewModal" 
                                                        data-id="<?php echo $service['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($service['service_name']); ?>"
                                                        data-description="<?php echo htmlspecialchars($service['description']); ?>">
                                                        <i class="bi-eye"></i> 
                                                    </button>
                                                    
                                                    <button class="btn btn-sm btn-warning action-btn edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" 
                                                        data-id="<?php echo $service['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($service['service_name']); ?>"
                                                        data-description="<?php echo htmlspecialchars($service['description']); ?>">
                                                        <i class="bi-pencil"></i> 
                                                    </button>
                                                    
                                                    <a href="services.php?delete_id=<?php echo $service['id']; ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this service?');">
                                                        <i class="bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- View Modal -->
                    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewModalLabel">Service Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <p><strong>Service Name:</strong> <span id="view-name"></span></p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p><strong>Description:</strong></p>
                                        <div class="p-3 bg-light rounded" id="view-description"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Create Modal -->
                    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="services.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Create New Service</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="service_name" class="form-label">Service Name</label>
                                            <input type="text" class="form-control" id="service_name" name="service_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="create_service" class="btn btn-primary">Create Service</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="services.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Service</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="service_id" id="edit-service-id">
                                        <div class="mb-3">
                                            <label for="edit-service_name" class="form-label">Service Name</label>
                                            <input type="text" class="form-control" id="edit-service_name" name="service_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit-description" class="form-label">Description</label>
                                            <textarea class="form-control" id="edit-description" name="description" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_service" class="btn btn-primary">Update Service</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <footer class="site-footer">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <p class="copyright-text">Copyright © Mk Interior & Decor <?php echo date('Y'); ?>. All rights reserved.</p>
                                </div>
                            </div>
                        </div>
                    </footer>
                </main>
            </div>
        </div>

        <!-- JAVASCRIPT FILES -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/apexcharts.min.js"></script>
        <script src="js/custom.js"></script>

        <script>
            // View Modal Script
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('view-name').textContent = this.getAttribute('data-name');
                    document.getElementById('view-description').textContent = this.getAttribute('data-description');
                });
            });

            // Edit Modal Script
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('edit-service-id').value = this.getAttribute('data-id');
                    document.getElementById('edit-service_name').value = this.getAttribute('data-name');
                    document.getElementById('edit-description').value = this.getAttribute('data-description');
                });
            });

            // Auto-dismiss alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        </script>
    </body>
</html>