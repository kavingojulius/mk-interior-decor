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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
</body>
</html>