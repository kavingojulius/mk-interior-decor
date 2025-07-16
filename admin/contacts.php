<?php
session_start();
require_once '../config/config.php';

// Check if admin is logged in
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit();
// }

// Fetch contact messages from database
$messages = [];
try {
    $stmt = $conn->query("SELECT * FROM contact_messages ORDER BY submission_date DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->bindParam(':id', $delete_id);
        $stmt->execute();
        $_SESSION['success_message'] = "Message deleted successfully!";
        header("Location: contacts.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error deleting message: " . $e->getMessage();
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $message_id = $_POST['message_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $conn->prepare("UPDATE contact_messages SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':id', $message_id);
        $stmt->execute();
        $_SESSION['success_message'] = "Status updated successfully!";
        header("Location: contacts.php");
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
            .status-unread {
                background-color: #fff3cd;
                color: #856404;
            }
            .status-read {
                background-color: #cce5ff;
                color: #004085;
            }
            .status-responded {
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
                <?php include './sidebar.php'; ?>

                <main class="main-wrapper col-md-9 ms-sm-auto py-4 col-lg-9 px-md-4 border-start">
                    <div class="title-group mb-3">
                        <span class="h4 mb-0">Contact Messages</span>
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
                                                <th scope="col">Name</th>
                                                <th scope="col">Phone</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($messages as $index => $message): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><?php echo htmlspecialchars($message['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($message['phone'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($message['email'] ?? 'N/A'); ?></td>
                                                <td><?php echo date('M j, Y g:i A', strtotime($message['submission_date'])); ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo $message['status']; ?>">
                                                        <?php echo ucfirst($message['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary action-btn view-btn" data-bs-toggle="modal" data-bs-target="#viewModal" 
                                                        data-id="<?php echo $message['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($message['full_name']); ?>"
                                                        data-phone="<?php echo htmlspecialchars($message['phone'] ?? 'N/A'); ?>"
                                                        data-email="<?php echo htmlspecialchars($message['email'] ?? 'N/A'); ?>"
                                                        data-date="<?php echo date('M j, Y g:i A', strtotime($message['submission_date'])); ?>"
                                                        data-message="<?php echo htmlspecialchars($message['message']); ?>"
                                                        data-status="<?php echo $message['status']; ?>">
                                                        <i class="bi-eye"></i> 
                                                    </button>
                                                    
                                                    <button class="btn btn-sm btn-warning action-btn update-btn" data-bs-toggle="modal" data-bs-target="#updateModal" 
                                                        data-id="<?php echo $message['id']; ?>"
                                                        data-status="<?php echo $message['status']; ?>">
                                                        <i class="bi-pencil"></i> 
                                                    </button>
                                                    
                                                    <a href="contacts.php?delete_id=<?php echo $message['id']; ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this message?');">
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
                                    <h5 class="modal-title" id="viewModalLabel">Message Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> <span id="view-name"></span></p>
                                            <p><strong>Phone:</strong> <span id="view-phone"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Email:</strong> <span id="view-email"></span></p>
                                            <p><strong>Date:</strong> <span id="view-date"></span></p>
                                            <p><strong>Status:</strong> <span id="view-status"></span></p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p><strong>Message:</strong></p>
                                        <div class="p-3 bg-light rounded" id="view-message"></div>
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
                                <form method="POST" action="contacts.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Update Message Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="message_id" id="update-message-id">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="unread">Unread</option>
                                                <option value="read">Read</option>
                                                <option value="responded">Responded</option>
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
                    document.getElementById('view-name').textContent = this.getAttribute('data-name');
                    document.getElementById('view-phone').textContent = this.getAttribute('data-phone');
                    document.getElementById('view-email').textContent = this.getAttribute('data-email');
                    document.getElementById('view-date').textContent = this.getAttribute('data-date');
                    document.getElementById('view-message').textContent = this.getAttribute('data-message');
                    
                    // Format status for display
                    let status = this.getAttribute('data-status');
                    status = status.charAt(0).toUpperCase() + status.slice(1);
                    document.getElementById('view-status').textContent = status;
                });
            });

            // Update Modal Script
            document.querySelectorAll('.update-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('update-message-id').value = this.getAttribute('data-id');
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