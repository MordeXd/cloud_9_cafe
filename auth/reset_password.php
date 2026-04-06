<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$title = "Reset Password - Cloud 9 Cafe";
$error = null;
$status = null;

// Validate token (from GET or POST)
$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$resetRequest = null;

if ($token) {
    $stmt = mysqli_prepare($con, "SELECT * FROM password_resets WHERE token = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $resetRequest = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);

    if (!$resetRequest) {
        $error = "This reset link is invalid. Please request a new one.";
    } elseif (!empty($resetRequest['used'])) {
        $error = "This reset link has already been used. Please request a new one.";
    } elseif (isset($resetRequest['expires_at']) && strtotime($resetRequest['expires_at']) < time()) {
        $error = "This reset link has expired. Please request a new one.";
    }
} else {
    $error = "Missing reset link. Please use the link sent to your email.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($newPassword === '' || $confirmPassword === '') {
        $error = "Please fill out both password fields.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Update user/admin password
        $email = $resetRequest['email'] ?? '';
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmtU = mysqli_prepare($con, "UPDATE users SET password=? WHERE email=? LIMIT 1");
        mysqli_stmt_bind_param($stmtU, "ss", $hash, $email);
        mysqli_stmt_execute($stmtU);
        $updated = mysqli_stmt_affected_rows($stmtU);
        mysqli_stmt_close($stmtU);

        if ($updated > 0) {
            $stmtTok = mysqli_prepare($con, "UPDATE password_resets SET used = 1, used_at = NOW() WHERE token = ?");
            mysqli_stmt_bind_param($stmtTok, "s", $token);
            mysqli_stmt_execute($stmtTok);
            mysqli_stmt_close($stmtTok);
            $status = "Your password has been updated. You can now log in.";
        } else {
            $error = "We couldn't update your password. Please request a new reset link.";
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
                            Reset Password
                        </h2>
                        <p class="text-muted">Set a new password for your account.</p>
                    </div>

                    <?php if ($status): ?>
                        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($status); ?></div>
                        <div class="text-center">
                            <a class="btn btn-primary" href="login.php">Go to Login</a>
                        </div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                        <div class="text-center">
                            <a href="forgot_password.php" class="btn btn-outline-primary">Request New Link</a>
                        </div>
                    <?php else: ?>
                        <form action="reset_password.php" method="POST">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <div class="mb-4">
                                <label for="new_password" class="form-label fw-semibold">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required data-validation="required strongPassword">
                                <span id="new_password_error" class="text-danger small"></span>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required data-validation="required confirmPassword">
                                <span id="confirm_password_error" class="text-danger small"></span>
                            </div>

                            <button type="submit" class="btn btn-gradient w-100 btn-lg mb-3">Reset Password</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
