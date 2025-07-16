<?php
include('./config/config.php');

// Get all projects
$projects = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="container py-5">
        <h1 class="text-center mb-5">Our Projects</h1>
        
        <div class="row">
            <?php while ($project = $projects->fetch_assoc()): 
                // Get first image for thumbnail
                $stmt = $conn->prepare("SELECT file_path FROM project_media WHERE project_id = ? AND media_type = 'image' LIMIT 1");
                $stmt->bind_param("i", $project['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $image = $result->fetch_assoc();
                
                // If no image, check for video
                if (!$image) {
                    $stmt = $conn->prepare("SELECT file_path FROM project_media WHERE project_id = ? AND media_type = 'video' LIMIT 1");
                    $stmt->bind_param("i", $project['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $video = $result->fetch_assoc();
                }
            ?>
            <div class="col-md-4 mb-4">
                <div class="card project-card" onclick="window.location='project_detail.php?id=<?= $project['id'] ?>'">
                    <?php if (isset($image)): ?>
                        <img src="<?= $image['file_path'] ?>" class="card-img-top" alt="<?= htmlspecialchars($project['title']) ?>">
                    <?php elseif (isset($video)): ?>
                        <video class="card-img-top">
                            <source src="<?= $video['file_path'] ?>" type="video/mp4">
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
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>