<?php
include '../config/db.php';
$pageTitle = 'Reset Password - Cloud 9 Cafe';

$token = $_GET['token'] ?? '';
$message = '';
$messageType = '';
$validToken = false;

if ($token) {
    $tokenSafe = cleanInput($token);
    $userQuery = mysqli_query($con, "SELECT id FROM users WHERE reset_token='$tokenSafe' AND reset_expires > NOW() LIMIT 1");
    if ($userQuery && mysqli_num_rows($userQuery) === 1) {
        $validToken = true;
        $user = mysqli_fetch_assoc($userQuery);
        $userId = (int)$user['id'];

        if (isset($_POST['reset_btn'])) {
            $password = $_POST['password'] ?? '';
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($con, "UPDATE users SET password='$hashed', reset_token=NULL, reset_expires=NULL WHERE id=$userId");
            $message = 'Password updated. You may now login.';
            $messageType = 'success';
            $validToken = false;
            header('Location: /cloud_9_cafe/auth/login.php?reset=success');
            exit;
        }
    } else {
        $message = 'This reset link is invalid or has expired.';
        $messageType = 'danger';
    }
} else {
    $message = 'Missing reset token.';
    $messageType = 'danger';
}

include '../includes/header.php';
?>
<div class="container">
    <div class="content-card form-shell">
        <h1 class="h3 mb-3">Reset Password</h1>
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
        <?php endif; ?>

        <?php if ($validToken): ?>
            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" id="confirmPassword_confirm" name="password" class="form-control" data-validation="required strongPassword">
                    <span id="password_error"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" id="password" name="confirmPassword" class="form-control" data-validation="required confirmPassword">
                    <span id="confirmPassword_error"></span>
                </div>
                <button type="submit" name="reset_btn" class="btn btn-cafe w-100">Update Password</button>
            </form>
        <?php else: ?>
            <a class="btn btn-cafe" href="/cloud_9_cafe/auth/forgot_password.php">Request a new link</a>
            <a class="btn btn-outline-secondary ms-2" href="/cloud_9_cafe/auth/login.php">Go to Login</a>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
