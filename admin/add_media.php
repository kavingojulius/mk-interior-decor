<?php
session_start();
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: admin_login.php');
//     exit;
// }

$project_id = $_GET['project_id'] ?? 0;
require_once '../config/config.php';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['media'])) {
    $upload_dir = 'uploads/projects/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES['media']['name'][$key]);
        $file_path = $upload_dir . uniqid() . '_' . $file_name;
        $file_type = strpos($_FILES['media']['type'][$key], 'video') !== false ? 'video' : 'image';
        
        if (move_uploaded_file($tmp_name, $file_path)) {
            try {
                $stmt = $conn->prepare("INSERT INTO project_media (project_id, file_path, media_type) VALUES (:project_id, :file_path, :media_type)");
                $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
                $stmt->bindParam(':file_path', $file_path);
                $stmt->bindParam(':media_type', $file_type);
                $stmt->execute();
            } catch(PDOException $e) {
                echo "Error uploading file: " . $e->getMessage();
            }
        }
    }
    header("Location: add_media.php?project_id=$project_id&success=1");
    exit;
}

// Get project info
try {
    $stmt = $conn->prepare("SELECT title FROM projects WHERE id = :project_id");
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error fetching project: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">

    <meta name="author" content="Mk Interior & Decor">

    <title>MK Interior & Decor - New project</title>

    <!-- CSS FILES -->      
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Hepta+Slab:wght@1..900&display=swap" rel="stylesheet">                

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">

    <link href="css/apexcharts.css" rel="stylesheet">

    <link href="css/styles.css" rel="stylesheet">

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

        <!-- <h4>Dashboard</h4> -->

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
                
                <div class="container mt-5">
                    <h2>Add Media to Project: <?= htmlspecialchars($project['title'] ?? 'Unknown Project') ?></h2>
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Media uploaded successfully!</div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="media" class="form-label">Upload Images/Videos</label>
                            <input type="file" class="form-control" id="media" name="media[]" multiple accept="image/*,video/*">
                            <div class="form-text">You can select multiple files</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Media</button>
                        <a href="./projects" class="btn btn-secondary">Finish</a>
                    </form>
                    
                    <div class="mt-4">
                        <h4>Uploaded Media</h4>
                        <div class="row">
                            <?php
                            try {
                                $stmt = $conn->prepare("SELECT * FROM project_media WHERE project_id = :project_id");
                                $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($media as $item):
                            ?>
                            <div class="col-md-3 mb-3">
                                <?php if ($item['media_type'] == 'image'): ?>
                                    <img src="<?= htmlspecialchars($item['file_path']) ?>" class="img-thumbnail" style="height: 200px; width: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <video width="100%" height="200" controls class="img-thumbnail">
                                        <source src="<?= htmlspecialchars($item['file_path']) ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php endif; ?>
                            </div>
                            <?php
                                endforeach;
                            } catch(PDOException $e) {
                                echo "<div class='alert alert-danger'>Error loading media: " . $e->getMessage() . "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <footer class="site-footer">
                    <div class="container">
                        <div class="row">
                            
                            <div class="col-lg-12 col-12">
                                <p class="copyright-text">Copyright © Mk Interior & Decor 2025. All rights reserved. 
                                - Design: <a  href="#" target="_blank">J & K</a></p>
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
        
    
</body>
</html>