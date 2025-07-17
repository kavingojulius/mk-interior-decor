<?php
session_start();
require_once '../config/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Check if project ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid project ID";
    header("Location: projects.php");
    exit();
}

$project_id = $_GET['id'];

// Fetch project details
try {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        $_SESSION['error'] = "Project not found";
        header("Location: projects.php");
        exit();
    }

    // Fetch project media
    $stmt = $conn->prepare("SELECT * FROM project_media WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching project: " . $e->getMessage();
    header("Location: projects.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Validate inputs
    if (empty($title) || empty($description)) {
        $_SESSION['error'] = "Title and description are required";
    } else {
        try {
            // Update project
            $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $description, $project_id]);

            // Handle file uploads if any
            if (!empty($_FILES['media']['name'][0])) {
                // Directory where files will be stored
                $uploadDir = 'uploads/projects/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Loop through each file
                foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['media']['name'][$key];
                    $file_tmp = $_FILES['media']['tmp_name'][$key];
                    $file_size = $_FILES['media']['size'][$key];
                    $file_error = $_FILES['media']['error'][$key];

                    // Check for errors
                    if ($file_error === UPLOAD_ERR_OK) {
                        // Generate unique filename
                        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $unique_name = uniqid('media_', true) . '.' . $file_ext;
                        $destination = $uploadDir . $unique_name;

                        // Move file to permanent location
                        if (move_uploaded_file($file_tmp, $destination)) {
                            // Determine media type
                            $media_type = strpos(mime_content_type($destination), 'image') !== false ? 'image' : 'video';

                            // Save to database
                            $stmt = $conn->prepare("INSERT INTO project_media (project_id, file_path, media_type) VALUES (?, ?, ?)");
                            $stmt->execute([$project_id, 'uploads/projects/' . $unique_name, $media_type]);
                        }
                    }
                }
            }

            $_SESSION['success'] = "Project updated successfully!";
            header("Location: ./projects");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error updating project: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="description" content="">
        <meta name="author" content="Mk Interior & Decor">

        <title>Edit Project - MK Interior & Decor</title>

        <!-- CSS FILES -->      
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Hepta+Slab:wght@1..900&display=swap" rel="stylesheet">                

        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-icons.css" rel="stylesheet">
        <link href="css/apexcharts.css" rel="stylesheet">
        <link href="css/styles.css" rel="stylesheet">

        <style>
            .media-preview {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 15px;
            }
            .media-item {
                position: relative;
                width: 150px;
                height: 150px;
                border: 1px solid #ddd;
                border-radius: 4px;
                overflow: hidden;
            }
            .media-item img, .media-item video {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .delete-media {
                position: absolute;
                top: 5px;
                right: 5px;
                background: rgba(0,0,0,0.7);
                color: white;
                border: none;
                border-radius: 50%;
                width: 25px;
                height: 25px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }
            .file-input-label {
                display: block;
                padding: 10px;
                border: 2px dashed #ddd;
                text-align: center;
                cursor: pointer;
                margin-bottom: 15px;
            }
            .file-input-label:hover {
                border-color: #aaa;
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
                        <h1 class="h2 mb-0">Edit Project</h1>
                        <small class="text-muted">Update project details</small>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Project Title</label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($project['description']) ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Project Media</label>
                                            <label for="media" class="file-input-label">
                                                <i class="bi-cloud-arrow-up fs-1"></i>
                                                <p>Click to upload images/videos</p>
                                                <input type="file" id="media" name="media[]" multiple accept="image/*,video/*" class="d-none">
                                            </label>
                                            <small class="text-muted">Upload multiple images or videos</small>
                                        </div>

                                        <?php if (!empty($media)): ?>
                                            <div class="media-preview">
                                                <?php foreach ($media as $item): ?>
                                                    <div class="media-item">
                                                        <?php if ($item['media_type'] === 'image'): ?>
                                                            <img src="<?= htmlspecialchars($item['file_path']) ?>" alt="Project media">
                                                        <?php else: ?>
                                                            <video controls>
                                                                <source src="<?= htmlspecialchars($item['file_path']) ?>" type="video/mp4">
                                                            </video>
                                                        <?php endif; ?>
                                                        <button type="button" class="delete-media" onclick="deleteMedia(<?= $item['id'] ?>)">
                                                            <i class="bi-x"></i>
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="./projects" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Project</button>
                                </div>
                            </form>
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
            function deleteMedia(mediaId) {
                if (confirm('Are you sure you want to delete this media item?')) {
                    $.ajax({
                        url: 'delete_media.php',
                        type: 'POST',
                        dataType: 'json', // Add this to expect JSON response
                        data: { id: mediaId },
                        success: function(response) {
                            if (response && response.success) {
                                location.reload();
                            } else {
                                alert('Error deleting media: ' + (response.error || 'Unknown error'));
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error deleting media: ' + error);
                        }
                    });
                }
            }

            // Preview selected files before upload
            document.getElementById('media').addEventListener('change', function(event) {
                const files = event.target.files;
                const previewContainer = document.querySelector('.media-preview') || document.createElement('div');
                
                if (!document.querySelector('.media-preview')) {
                    previewContainer.className = 'media-preview';
                    document.querySelector('.file-input-label').insertAdjacentElement('afterend', previewContainer);
                } else {
                    previewContainer.innerHTML = '';
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileType = file.type.split('/')[0];
                    const previewElement = document.createElement('div');
                    previewElement.className = 'media-item';
                    
                    if (fileType === 'image') {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        previewElement.appendChild(img);
                    } else if (fileType === 'video') {
                        const video = document.createElement('video');
                        video.controls = true;
                        const source = document.createElement('source');
                        source.src = URL.createObjectURL(file);
                        source.type = file.type;
                        video.appendChild(source);
                        previewElement.appendChild(video);
                    }
                    
                    previewContainer.appendChild(previewElement);
                }
            });
        </script>
    </body>
</html>