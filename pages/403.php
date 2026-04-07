<?php
require_once '../config/TokenAuth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new TokenAuth();
$tokenUser = $auth->getCurrentUser();
$isAdmin = ($tokenUser && (($tokenUser['type'] ?? '') === 'admin')) || !empty($_SESSION['admin']);
$isUser = ($tokenUser && (($tokenUser['type'] ?? '') === 'user')) || !empty($_SESSION['user']);

$pageTitle = 'Access Denied - Cloud 9 Cafe';
$dashboardLink = $isAdmin ? '/cloud_9_cafe/admin/dashboard.php' : '/cloud_9_cafe/user/dashboard.php';
$buttonLink = ($isAdmin || $isUser) ? $dashboardLink : '/cloud_9_cafe/auth/login.php';

http_response_code(403);

include '../includes/header.php';
?>
<div class="container">
    <section class="content-card text-center">
        <h1 class="h3 mb-2">Access Denied</h1>
        <p class="text-muted mb-4">You don’t have permission to access this page</p>
        <a class="btn btn-primary" href="<?= $buttonLink ?>">Go to Dashboard</a>
    </section>
</div>
<?php include '../includes/footer.php'; ?>
