<?php
// Common authentication helpers for Cloud 9 Cafe.
require_once __DIR__ . '/../config/TokenAuth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function syncAuthSession()
{
    global $auth;
    if (!isset($auth) || !($auth instanceof TokenAuth)) {
        $auth = new TokenAuth();
    }

    $tokenUser = $auth->getCurrentUser();
    if ($tokenUser && (($tokenUser['type'] ?? '') === 'admin')) {
        $_SESSION['admin'] = true;
        $_SESSION['user'] = null;
        $_SESSION['user_id'] = $tokenUser['id'] ?? null;
        $_SESSION['user_name'] = $tokenUser['name'] ?? 'Admin';
    } elseif ($tokenUser && (($tokenUser['type'] ?? '') === 'user')) {
        $_SESSION['user'] = true;
        $_SESSION['admin'] = null;
        $_SESSION['user_id'] = $tokenUser['id'] ?? null;
        $_SESSION['user_name'] = $tokenUser['name'] ?? 'User';
    }
}

function renderAccessDenied()
{
    http_response_code(403);
    $pageTitle = 'Access Denied - Cloud 9 Cafe';
    $activePage = '';
    $isAdmin = !empty($_SESSION['admin']);
    $dashboardLink = $isAdmin ? '/cloud_9_cafe/admin/dashboard.php' : '/cloud_9_cafe/user/dashboard.php';

    include __DIR__ . '/header.php';
    echo '<div class="container">';
    echo '  <div class="dashboard-shell">';
    if ($isAdmin) {
        include __DIR__ . '/admin_sidebar.php';
    } else {
        include __DIR__ . '/user_sidebar.php';
    }
    echo '      <section class="content-card text-center">';
    echo '          <h1 class="h3 mb-2">Access Denied</h1>';
    echo '          <p class="text-muted mb-4">You don’t have permission to access this page</p>';
    echo '          <a class="btn btn-primary" href="' . $dashboardLink . '">Go to Dashboard</a>';
    echo '      </section>';
    echo '  </div>';
    echo '</div>';
    include __DIR__ . '/footer.php';
    exit;
}

function requireUser()
{
    syncAuthSession();
    if (empty($_SESSION['user'])) {
        if (!empty($_SESSION['admin'])) {
            renderAccessDenied();
        }
        header('Location: /cloud_9_cafe/auth/login.php');
        exit;
    }
}

function requireAdmin()
{
    syncAuthSession();
    if (empty($_SESSION['admin'])) {
        if (!empty($_SESSION['user'])) {
            renderAccessDenied();
        }
        header('Location: /cloud_9_cafe/auth/login.php');
        exit;
    }
}

function currentUserId()
{
    syncAuthSession();
    return $_SESSION['user_id'] ?? null;
}
?>
