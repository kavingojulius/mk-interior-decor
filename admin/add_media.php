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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Media to Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
            <a href="./index.php" class="btn btn-secondary">Finish</a>
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
</body>
</html>