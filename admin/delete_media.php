<?php
session_start();
require_once '../config/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        // Get media info before deletion
        $stmt = $conn->prepare("SELECT file_path FROM project_media WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $media = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($media) {
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM project_media WHERE id = ?");
            $stmt->execute([$_POST['id']]);

            // Delete file from server
            $file_path = './' . $media['file_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Media not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}