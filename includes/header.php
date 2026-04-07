<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Cloud 9 Cafe';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/TokenAuth.php';
if (!isset($auth) || !($auth instanceof TokenAuth)) {
    $auth = new TokenAuth();
}

$tokenUser = $auth->getCurrentUser();
$isTokenAdmin = $tokenUser && (($tokenUser['type'] ?? '') === 'admin');
$isTokenUser = $tokenUser && (($tokenUser['type'] ?? '') === 'user');

if ($isTokenAdmin) {
    $_SESSION['admin'] = true;
    $_SESSION['user'] = null;
    $_SESSION['user_id'] = $tokenUser['id'] ?? null;
    $_SESSION['user_name'] = $tokenUser['name'] ?? 'Admin';
} elseif ($isTokenUser) {
    $_SESSION['user'] = true;
    $_SESSION['admin'] = null;
    $_SESSION['user_id'] = $tokenUser['id'] ?? null;
    $_SESSION['user_name'] = $tokenUser['name'] ?? 'User';
}

$isAdmin = $isTokenAdmin || !empty($_SESSION['admin']);
if ($isAdmin && $isTokenUser) {
    $isTokenUser = false;
}
$isLoggedIn = $isAdmin || $isTokenUser || !empty($_SESSION['user']) || !empty($_SESSION['user_id']);

$currentPath = $_SERVER['PHP_SELF'] ?? '';
$isDashboardLayout = $isLoggedIn && (strpos($currentPath, '/admin/') !== false || strpos($currentPath, '/user/') !== false);

$displayName = $tokenUser['name'] ?? ($_SESSION['user_name'] ?? ($isAdmin ? 'Admin' : 'User'));
$dashboardLink = $isAdmin ? '/cloud_9_cafe/admin/dashboard.php' : '/cloud_9_cafe/user/dashboard.php';
$profileLink = '/cloud_9_cafe/user/profile.php';
$cartCount = $isAdmin ? 0 : ($_SESSION['cart_count'] ?? 0);
$rawTitle = $pageTitle ?? 'Dashboard';
$dashboardTitle = $rawTitle;
if (strpos($rawTitle, ' - ') !== false) {
    $dashboardTitle = trim(explode(' - ', $rawTitle)[0]);
}
$dashboardSubtitle = $isAdmin ? 'Admin Panel' : 'User Panel';
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
<body data-role="<?= $isAdmin ? 'admin' : ($isLoggedIn ? 'user' : 'guest') ?>" class="<?= $isDashboardLayout ? 'dashboard-layout' : '' ?>">
<?php if (!$isDashboardLayout): ?>
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
                    <?php if (!$isAdmin): ?>
                        <li class="nav-item me-3">
                            <a class="nav-link position-relative" href="/cloud_9_cafe/user/cart.php">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-badge" id="navbarCartCount"><?= (int)$cartCount ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <span class="nav-avatar d-inline-flex align-items-center justify-content-center bg-primary text-white">
                                <i class="fas fa-user"></i>
                            </span>
                            <span class="fw-medium"><?= htmlspecialchars($displayName) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= $dashboardLink ?>"><i class="fas fa-th-large me-2 text-primary"></i>Dashboard</a></li>
                            <?php if (!$isAdmin): ?>
                                <li><a class="dropdown-item" href="<?= $profileLink ?>"><i class="fas fa-user me-2 text-primary"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="/cloud_9_cafe/user/orders.php"><i class="fas fa-shopping-bag me-2 text-primary"></i>My Orders</a></li>
                            <?php endif; ?>
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
<?php else: ?>
<main class="dashboard-main">
    <div class="dashboard-topbar">
        <div class="container">
            <div class="dashboard-topbar-inner row align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="dashboard-title d-flex flex-column">
                        <h1 class="h4 mb-0"><?= htmlspecialchars($dashboardTitle) ?></h1>
                        <small class="text-muted"><?= htmlspecialchars($dashboardSubtitle) ?></small>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="dashboard-actions d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                        <?php if (!empty($dashboardSearchEnabled)): ?>
                            <div class="dashboard-search d-none d-md-flex">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" placeholder="Search...">
                            </div>
                        <?php endif; ?>
                        <button class="btn btn-light dashboard-icon-btn" type="button" aria-label="Notifications">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="dashboard-profile d-flex align-items-center gap-2">
                            <span class="nav-avatar d-inline-flex align-items-center justify-content-center bg-primary text-white">
                                <i class="fas fa-user"></i>
                            </span>
                            <div class="d-none d-sm-block">
                                <div class="fw-semibold"><?= htmlspecialchars($displayName) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($dashboardSubtitle) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="dashboard-content">
<?php endif; ?>
