<?php
include '../config/db.php';
$env = loadEnv(__DIR__ . '/../.env');
$pageTitle = 'Account Activation - Cloud 9 Cafe';

$message = 'Invalid or missing activation token.';
$messageType = 'danger';

if (isset($_GET['token'])) {
    $token = cleanInput($_GET['token']);
    $userQuery = mysqli_query($con, "SELECT id, status, is_verified FROM users WHERE activation_token='$token' LIMIT 1");
    if ($userQuery && mysqli_num_rows($userQuery) === 1) {
        $user = mysqli_fetch_assoc($userQuery);
        if ($user['is_verified'] == 1 && $user['status'] === 'active') {
            $message = 'Your account is already activated. You can login.';
            $messageType = 'info';
        } else {
            mysqli_query($con, "UPDATE users SET is_verified=1, status='active', activation_token=NULL WHERE id=" . (int)$user['id']);
            $message = 'Account activated successfully. You may now login.';
            $messageType = 'success';
        }
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="content-card form-shell">
        <h1 class="h4 mb-3">Account Activation</h1>
        <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
        <a class="btn btn-cafe" href="/cloud_9_cafe/auth/login.php">Go to Login</a>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
