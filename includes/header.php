<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Cloud 9 Cafe';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user']) || isset($_SESSION['user_id']) || isset($_SESSION['admin']);
$isAdmin = isset($_SESSION['admin']);
$displayName = $_SESSION['user_name'] ?? 'User';
$dashboardLink = $isAdmin ? '/cloud_9_cafe/admin/dashboard.php' : '/cloud_9_cafe/user/dashboard.php';
$profileLink = '/cloud_9_cafe/user/profile.php';
$cartCount = $_SESSION['cart_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap / Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Theme styles to match public navigation -->
    <link rel="stylesheet" href="/cloud_9_cafe/assets/css/theme.css">
    <link rel="stylesheet" href="/cloud_9_cafe/assets/css/layout/layout.css">
    <link rel="stylesheet" href="/cloud_9_cafe/assets/css/global.css">
</head>
<body>
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/cloud_9_cafe/pages/index.php">
            <i class="fas fa-mug-hot"></i>
            Cloud 9 Cafe
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <i class="fas fa-bars text-primary"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/pages/index.php"><i class="fas fa-home me-1 d-lg-none"></i>Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/menu.php"><i class="fas fa-coffee me-1 d-lg-none"></i>Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/pages/about.php"><i class="fas fa-info-circle me-1 d-lg-none"></i>About</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/pages/contact.php"><i class="fas fa-envelope me-1 d-lg-none"></i>Contact</a></li>
            </ul>
            <ul class="navbar-nav align-items-center">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item me-3">
                        <a class="nav-link position-relative" href="/cloud_9_cafe/user/cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge" id="navbarCartCount"><?= (int)$cartCount ?></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <span class="nav-avatar d-inline-flex align-items-center justify-content-center bg-primary text-white">
                                <i class="fas fa-user"></i>
                            </span>
                            <span class="fw-medium"><?= htmlspecialchars($displayName) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= $dashboardLink ?>"><i class="fas fa-th-large me-2 text-primary"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="<?= $profileLink ?>"><i class="fas fa-user me-2 text-primary"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="/cloud_9_cafe/user/orders.php"><i class="fas fa-shopping-bag me-2 text-primary"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/cloud_9_cafe/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="/cloud_9_cafe/auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="/cloud_9_cafe/auth/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div style="height: var(--navbar-height);"></div>
<main class="py-4">
