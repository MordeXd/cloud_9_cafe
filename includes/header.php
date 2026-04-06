<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Cloud 9 Cafe';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin']);
$userName = $_SESSION['user_name'] ?? 'Account';
$dashboardLink = $isAdmin ? '/cloud_9_cafe/admin/dashboard.php' : '/cloud_9_cafe/user/dashboard.php';
$profileLink = '/cloud_9_cafe/user/profile.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/cloud_9_cafe/assets/css/global.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark cafe-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/cloud_9_cafe/index.php">Cloud 9 Cafe</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/contact.php">Contact</a></li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($userName) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= $dashboardLink ?>">Dashboard</a></li>
                            <li><a class="dropdown-item" href="<?= $profileLink ?>">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/cloud_9_cafe/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-cafe btn-sm mt-1 mt-lg-0" href="/cloud_9_cafe/auth/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="py-4">
