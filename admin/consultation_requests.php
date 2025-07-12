<?php
session_start();
require_once '../config/config.php'; // Adjust path as needed

// Check if admin is logged in
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit();
// }

// Fetch consultation requests from database
$requests = [];
try {
    $stmt = $conn->query("SELECT * FROM consultation_requests ORDER BY submission_date DESC");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM consultation_requests WHERE id = :id");
        $stmt->bindParam(':id', $delete_id);
        $stmt->execute();
        $_SESSION['success_message'] = "Request deleted successfully!";
        header("Location: consultation_requests.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error deleting request: " . $e->getMessage();
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $conn->prepare("UPDATE consultation_requests SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $request_id);
        $stmt->execute();
        $_SESSION['success_message'] = "Status updated successfully!";
        header("Location: consultation_requests.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="description" content="">
        <meta name="author" content="MK Interior">

        <title>MK Interior Dashboard</title>

        <!-- CSS FILES -->      
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Hepta+Slab:wght@1..900&display=swap" rel="stylesheet">                

        <link href="css/bootstrap.min.css" rel="stylesheet">

        <link href="css/bootstrap-icons.css" rel="stylesheet">

        <link href="css/apexcharts.css" rel="stylesheet">

        <link href="css/styles.css" rel="stylesheet">

        <style>
            .status-badge {
                padding: 5px 10px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 500;
            }
            .status-pending {
                background-color: #fff3cd;
                color: #856404;
            }
            .status-in_progress {
                background-color: #cce5ff;
                color: #004085;
            }
            .status-completed {
                background-color: #d4edda;
                color: #155724;
            }
            .action-btn {
                padding: 5px 10px;
                margin: 0 2px;
                font-size: 0.8rem;
            }
        </style>
    </head>
    
    <body>
        
        <header class="navbar sticky-top flex-md-nowrap">
            <div class="col-md-3 col-lg-3 me-0 px-3 fs-6">
                <a class="navbar-brand" href="./">
                    <i class="bi-box"></i>
                    MK Interior
                </a>
            </div>

            <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- <h3>Dashboard</h3> -->
            
            <div class="navbar-nav me-lg-2">
                <div class="nav-item text-nowrap d-flex align-items-center">
                    

                    
                </div>
            </div>            
        </header>
    
        <div class="container-fluid">
            <div class="row">

            <!-- Side bar -->
                <?php include './sidebar.php'; ?>
            <!-- Side bar -->

                <main class="main-wrapper col-md-9 ms-sm-auto py-4 col-lg-9 px-md-4 border-start">
                    <div class="title-group mb-3">
                        <span class="h4 mb-0">Consultation requests</span>
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
                                <h5 class="mb-4">Consultation Requests</h5>

                                <div class="table-responsive">
                                    <table class="account-table table">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Submission Date</th>
                                                <!-- <th scope="col">Request</th> -->
                                                <th scope="col">Status</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($requests as $request): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($request['id']); ?></td>
                                                <td><?php echo htmlspecialchars($request['email']); ?></td>
                                                <td><?php echo date('M j, Y g:i A', strtotime($request['submission_date'])); ?></td>
                                                
                                                <td>
                                                    <span class="status-badge status-<?php echo str_replace('_', '-', $request['status']); ?>">
                                                        <?php 
                                                        $status = $request['status'];
                                                        if ($status == 'in_progress') {
                                                            echo 'In Progress';
                                                        } else {
                                                            echo ucfirst($status);
                                                        }
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary action-btn view-btn" data-bs-toggle="modal" data-bs-target="#viewModal" 
                                                        data-id="<?php echo $request['id']; ?>"
                                                        data-email="<?php echo htmlspecialchars($request['email']); ?>"
                                                        data-date="<?php echo date('M j, Y g:i A', strtotime($request['submission_date'])); ?>"
                                                        data-request="<?php echo htmlspecialchars($request['request_text']); ?>"
                                                        data-status="<?php echo $request['status']; ?>">
                                                        <i class="bi-eye"></i> 
                                                    </button>
                                                    
                                                    <button class="btn btn-sm btn-warning action-btn update-btn" data-bs-toggle="modal" data-bs-target="#updateModal" 
                                                        data-id="<?php echo $request['id']; ?>"
                                                        data-status="<?php echo $request['status']; ?>">
                                                        <i class="bi-pencil"></i> 
                                                    </button>
                                                    
                                                    <a href="consultation_requests.php?delete_id=<?php echo $request['id']; ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this request?');">
                                                        <i class="bi-trash"></i> 
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center mb-0">
                                        <li class="page-item">
                                            <a class="page-link" href="#" aria-label="Previous">
                                                <span aria-hidden="true">Prev</span>
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
                                            <a class="page-link" href="#" aria-label="Next">
                                                <span aria-hidden="true">Next</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- View Modal -->
                    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewModalLabel">Consultation Request Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>ID:</strong> <span id="view-id"></span></p>
                                            <p><strong>Email:</strong> <span id="view-email"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Submission Date:</strong> <span id="view-date"></span></p>
                                            <p><strong>Status:</strong> <span id="view-status"></span></p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p><strong>Request:</strong></p>
                                        <div class="p-3 bg-light rounded" id="view-request"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Status Modal -->
                    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="consultation_requests.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Update Request Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="request_id" id="update-request-id">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending">Pending</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <footer class="site-footer">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <p class="copyright-text">Copyright © MK Interior <?php echo date('Y'); ?></p>
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
                    document.getElementById('view-id').textContent = this.getAttribute('data-id');
                    document.getElementById('view-email').textContent = this.getAttribute('data-email');
                    document.getElementById('view-date').textContent = this.getAttribute('data-date');
                    document.getElementById('view-request').textContent = this.getAttribute('data-request');
                    
                    // Format status for display
                    let status = this.getAttribute('data-status');
                    if (status === 'in_progress') {
                        status = 'In Progress';
                    } else {
                        status = status.charAt(0).toUpperCase() + status.slice(1);
                    }
                    document.getElementById('view-status').textContent = status;
                });
            });

            // Update Modal Script
            document.querySelectorAll('.update-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('update-request-id').value = this.getAttribute('data-id');
                    document.getElementById('status').value = this.getAttribute('data-status');
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