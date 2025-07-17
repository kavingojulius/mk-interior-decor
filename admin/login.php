<?php
session_start();
require_once '../config/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Verify the password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;  // Changed from admin_id
            $_SESSION['admin_id'] = $admin['id']; // Keep this if you need the ID elsewhere
            header("Location: ./");        // Explicit path is better than ./
            exit();
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Wrong password! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Admin not found! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex vh-100 align-items-center justify-content-center">
        <div class="card p-4 shadow" style="min-width: 300px;">
            <h3 class="text-center mb-3">Admin Login</h3>
            <?php if ($message): ?>
                <?php echo $message; ?>
            <?php endif; ?>
            <form method="post">
                <input type="text" name="username" placeholder="Username" class="form-control mb-2" required>
                <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
