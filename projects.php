<?php
include('./config/config.php');

// Get all projects
try {
    $stmt = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching projects: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">        
    <meta name="author" content="Mk Interior & Decor">
    
    <title>Our Projects</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Hepta+Slab:wght@1..900&display=swap" rel="stylesheet">                
    
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <link href="./css/bootstrap-icons.css" rel="stylesheet">

    <link href="./css/owl.carousel.min.css" rel="stylesheet">

    <link href="./css/styles.css" rel="stylesheet">


    <style>
        .project-card {
            transition: transform 0.3s;
            cursor: pointer;
            height: 100%;
        }
        .project-card:hover {
            transform: scale(1.03);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>

</head>
<body>

    <?php include_once './includes/navbar.php'; ?>


    <div class="container py-5">
        <h1 class="text-center mb-5">Our Projects</h1>
        
        <div class="row">
            <?php foreach ($projects as $project): 
                // Get first image for thumbnail
                try {
                    $stmt = $conn->prepare("SELECT file_path FROM project_media WHERE project_id = :project_id AND media_type = 'image' LIMIT 1");
                    $stmt->bindParam(':project_id', $project['id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $image = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // If no image, check for video
                    if (!$image) {
                        $stmt = $conn->prepare("SELECT file_path FROM project_media WHERE project_id = :project_id AND media_type = 'video' LIMIT 1");
                        $stmt->bindParam(':project_id', $project['id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $video = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                } catch(PDOException $e) {
                    echo "<div class='alert alert-danger'>Error loading media: " . $e->getMessage() . "</div>";
                    continue;
                }
            ?>
            <div class="col-md-4 mb-4">
                <div class="card project-card" onclick="window.location='project_detail.php?id=<?= $project['id'] ?>'">
                    <?php if (!empty($image)): ?>
                        <img src="admin/<?= htmlspecialchars($image['file_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($project['title']) ?>">
                    <?php elseif (!empty($video)): ?>
                        <video class="card-img-top">
                            <source src="admin/<?= htmlspecialchars($video['file_path']) ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center">
                            <span class="text-white">No media available</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($project['title']) ?></h5>
                        <p class="card-text text-truncate"><?= htmlspecialchars($project['description']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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