<?php
require_once '../config/db_config.php';
require_once '../includes/mailer.php';

$title = "Forgot Password - Cloud 9 Cafe";
$status = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Look up user by email (users or admins)
        $user = $db->selectOne('cafe_users', ['email' => $email]) ?: $db->selectOne('cafe_admins', ['email' => $email]);

        if (!$user) {
            $error = "We couldn't find an account with that email.";
        } else {
            // Create password reset token valid for 60 minutes
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600);

            $db->insert('password_resets', [
                'email'      => $email,
                'token'      => $token,
                'expires_at' => $expiresAt,
                'used'       => 0
            ]);

            // Build reset link
            $env = loadEnv(__DIR__ . '/../.env');
            $baseUrl = rtrim($env['APP_URL'] ?? ('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/cloud_9_cafe'), '/');
            $resetLink = $baseUrl . '/auth/reset_password.php?token=' . urlencode($token);

            $subject = 'Reset your Cloud 9 Cafe password';
            $body = "
                <p>Hi " . htmlspecialchars($user['fullname'] ?? 'there') . ",</p>
                <p>We received a request to reset your Cloud 9 Cafe password. Click the button below to set a new one:</p>
                <p style='text-align:center; margin: 24px 0;'>
                    <a href='{$resetLink}' style='background:#667eea;color:#fff;text-decoration:none;padding:12px 20px;border-radius:6px;display:inline-block;'>Reset Password</a>
                </p>
                <p>This link will expire in 60 minutes. If you didn't request a password reset, you can ignore this email.</p>
                <p>Thanks,<br>Cloud 9 Cafe Team</p>
            ";

            if (sendAppMail($email, $subject, $body)) {
                $status = "We sent a password reset link to {$email}. Please check your inbox.";
            } else {
                $error = "We couldn't send the reset email right now. Please try again in a moment.";
            }
        }
    }
}

ob_start();
?>
<div class="container">
    <div class="row justify-content-center fade-in-up">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold" style="color: #667eea;">
                            Forgot Password?
                        </h2>
                        <p class="text-muted">Enter your email and we'll send you a reset link.</p>
                    </div>

                    <?php if ($status): ?>
                        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($status); ?></div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form action="forgot_password.php" method="POST">
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required data-validation="required email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <span id="email_error" class="text-danger small"></span>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 btn-lg mb-3">Send Reset Link</button>

                        <div class="text-center">
                            <p class="text-muted mb-0">Remember your password? <a href="login.php" class="text-decoration-none fw-semibold" style="color: #667eea;">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
