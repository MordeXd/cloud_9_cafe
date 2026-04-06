<?php
include '../config/db.php';
include '../includes/mailer.php';
$env = loadEnv(__DIR__ . '/../.env');
$pageTitle = 'Forgot Password - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['reset_btn'])) {
    $email = cleanInput($_POST['email']);
    $userQuery = mysqli_query($con, "SELECT id, full_name FROM users WHERE email='$email' LIMIT 1");
    if ($userQuery && mysqli_num_rows($userQuery) === 1) {
        $user = mysqli_fetch_assoc($userQuery);
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        mysqli_query($con, "UPDATE users SET reset_token='$token', reset_expires='$expires' WHERE id=" . (int)$user['id']);

        $resetLink = ($env['APP_URL'] ?? 'http://localhost/cloud_9_cafe') . '/auth/reset_password.php?token=' . $token;
        $body = '<p>Hello ' . htmlspecialchars($user['full_name']) . ',</p><p>We received a request to reset your password. Click the link below to set a new password:</p><p><a href="' . $resetLink . '">' . $resetLink . '</a></p><p>If you did not request this, please ignore this email.</p>';
        sendAppMail($email, 'Reset your Cloud 9 Cafe password', $body);
        $message = 'Reset link sent to your email address.';
        $messageType = 'success';
    } else {
        $message = 'If the email exists, a reset link will be sent.';
        $messageType = 'info';
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="content-card form-shell">
        <h1 class="h3 mb-3">Forgot Password</h1>
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">Registered Email</label>
                <input type="text" name="email" class="form-control" data-validation="required email">
                <span id="email_error"></span>
            </div>
            <button type="submit" name="reset_btn" class="btn btn-cafe w-100">Send Reset Link</button>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
