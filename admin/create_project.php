<?php
session_start();

require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    try {
        $stmt = $conn->prepare("INSERT INTO projects (title, description) VALUES (:title, :description)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            $project_id = $conn->lastInsertId();
            header("Location: add_media.php?project_id=$project_id");
            exit;
        } else {
            echo "Error creating project";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
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
                    <h2>Create New Project</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Project Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Project</button>
                    </form>
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