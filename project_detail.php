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
    <title><?= htmlspecialchars($project['title']) ?> - Project Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .media-container {
            margin-bottom: 30px;
        }
        .media-item {
            margin-bottom: 20px;
        }
        .media-item img, .media-item video {
            max-width: 100%;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <a href="projects.php" class="btn btn-outline-secondary mb-4">← Back to Projects</a>
        
        <h1 class="mb-4"><?= htmlspecialchars($project['title']) ?></h1>
        
        <div class="row">
            <div class="col-md-8">
                <div class="media-container">
                    <?php foreach ($media as $item): ?>
                        <div class="media-item">
                            <?php if ($item['media_type'] == 'image'): ?>
                                <img src="admin/<?= htmlspecialchars($item['file_path']) ?>" class="img-fluid" alt="Project Image">
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
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Project Details</h3>
                        <p class="card-text"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>