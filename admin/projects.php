<?php 
session_start();
require_once '../config/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Handle project deletion
if (isset($_GET['delete_id'])) {
    try {
        $project_id = $_GET['delete_id'];
        
        // First delete related media
        $stmt = $conn->prepare("DELETE FROM project_media WHERE project_id = ?");
        $stmt->execute([$project_id]);
        
        // Then delete the project
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        
        $_SESSION['success'] = "Project deleted successfully!";
        header("Location: projects.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error deleting project: " . $e->getMessage();
        header("Location: projects.php");
        exit();
    }
}

// Get all projects
try {
    $stmt = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching projects: " . $e->getMessage());
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
            .project-card {
                transition: transform 0.3s;
                height: 100%;
            }
            .project-card:hover {
                transform: scale(1.02);
            }
            .card-img-top {
                height: 180px;
                object-fit: cover;
            }
            .action-buttons .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
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
                
            <!-- side bar -->
                <?php include_once './sidebar.php'; ?>
            <!-- side bar end -->

                <main class="main-wrapper col-md-9 ms-sm-auto py-4 col-lg-9 px-md-4 border-start">
                    <div class="title-group mb-3">
                        <h1 class="h2 mb-0">Projects Management</h1>
                        <small class="text-muted">Manage all projects</small>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Create New Project</h5>
                                    <a href="./create_project" class="btn btn-primary">Create Project</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">All Projects</h5>
                            
                            <?php if (empty($projects)): ?>
                                <div class="alert alert-info">No projects found. Create your first project!</div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($projects as $project): 
                                        // Get first image for thumbnail
                                        try {
                                            $stmt = $conn->prepare("SELECT file_path FROM project_media WHERE project_id = :project_id AND media_type = 'image' LIMIT 1");
                                            $stmt->bindParam(':project_id', $project['id'], PDO::PARAM_INT);
                                            $stmt->execute();
                                            $image = $stmt->fetch(PDO::FETCH_ASSOC);
                                        } catch(PDOException $e) {
                                            echo "<div class='alert alert-danger'>Error loading media: " . $e->getMessage() . "</div>";
                                            continue;
                                        }
                                    ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card project-card">
                                            <?php if (!empty($image)): ?>
                                                <img src="<?= htmlspecialchars($image['file_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($project['title']) ?>">
                                            <?php else: ?>
                                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 180px;">
                                                    <span class="text-white">No image available</span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($project['title']) ?></h5>
                                                <p class="card-text text-truncate"><?= htmlspecialchars($project['description']) ?></p>
                                                <div class="d-flex justify-content-between action-buttons">
                                                    <a href="edit_project.php?id=<?= $project['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi-pencil"></i> Edit
                                                    </a>
                                                    <a href="javascript:void(0)" onclick="confirmDelete(<?= $project['id'] ?>)" class="btn btn-sm btn-danger">
                                                        <i class="bi-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <footer class="site-footer">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <p class="copyright-text">Copyright © Mk Interior & Decor 2025. All rights reserved. 
                                    - Design: <a href="#" target="_blank">J & K</a></p>
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
            function confirmDelete(projectId) {
                if (confirm("Are you sure you want to delete this project? This action cannot be undone.")) {
                    window.location.href = 'projects.php?delete_id=' + projectId;
                }
            }
        </script>

    </body>
</html>