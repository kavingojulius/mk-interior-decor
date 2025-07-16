<?php
include('./config/config.php');

$project_id = $_GET['id'] ?? 0;

// Get project details
try {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        header("Location: projects.php");
        exit;
    }

    // Get project media
    $stmt = $conn->prepare("SELECT * FROM project_media WHERE project_id = :project_id ORDER BY media_type");
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error loading project: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">        
    <meta name="author" content="Mk Interior & Decor">

    <title><?= htmlspecialchars($project['title']) ?> - Project Details</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Hepta+Slab:wght@1..900&display=swap" rel="stylesheet">                
    
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/bootstrap-icons.css" rel="stylesheet">
    <link href="./css/owl.carousel.min.css" rel="stylesheet">
    <link href="./css/styles.css" rel="stylesheet">

    <style>
        .project-header {
            margin-bottom: 30px;
        }
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .media-item {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .media-item:hover {
            transform: translateY(-5px);
        }
        .media-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        .media-item video {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        .project-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
        }
        .project-info h3 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .project-info p {
            color: #555;
            line-height: 1.6;
        }
        .back-btn {
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            transform: translateX(-5px);
        }
        @media (max-width: 768px) {
            .media-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Start Nav bar -->
    <?php include_once './includes/navbar.php'; ?>
    <!-- End Nav bar -->

    <div class="container py-5">
        <a href="projects.php" class="btn btn-outline-secondary back-btn">
            <i class="bi bi-arrow-left"></i> Back to Projects
        </a>
        
        <div class="project-header">
            <h1 class="display-5"><?= htmlspecialchars($project['title']) ?></h1>
        </div>
        
        <div class="row">
            <div class="col-lg-8 order-lg-1 order-2">
                <div class="media-grid">
                    <?php foreach ($media as $item): ?>
                        <div class="media-item">
                            <?php if ($item['media_type'] == 'image'): ?>
                                <img src="admin/<?= htmlspecialchars($item['file_path']) ?>" alt="Project Image" class="img-fluid">
                            <?php else: ?>
                                <video controls class="w-100">
                                    <source src="admin/<?= htmlspecialchars($item['file_path']) ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="col-lg-4 order-lg-2 order-1 mb-4 mb-lg-0">
                <div class="project-info">
                    <h3>Project Details</h3>
                    <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>

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