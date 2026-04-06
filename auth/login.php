<?php
include '../config/db.php';
$pageTitle = 'Login - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['login_btn'])) {
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' LIMIT 1");

    if ($query && mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        if (!password_verify($password, $user['password'])) {
            $message = 'Invalid email or password.';
            $messageType = 'danger';
        } elseif ($user['status'] !== 'active' || (int)$user['is_verified'] !== 1) {
            $message = 'Please activate your account from the email link before logging in.';
            $messageType = 'warning';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];

            if ($user['role'] === 'admin') {
                $_SESSION['admin'] = $user['email'];
                header('Location: /cloud_9_cafe/admin/dashboard.php');
                exit;
            }

            header('Location: /cloud_9_cafe/user/dashboard.php');
            exit;
        }
    } else {
        $message = 'Invalid email or password.';
        $messageType = 'danger';
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="content-card form-shell">
        <h1 class="h3 mb-3">Login</h1>
        <p class="text-muted">Use the same simple validation method as the reference project.</p>
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="text" name="email" class="form-control" data-validation="required email">
                <span id="email_error"></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" data-validation="required">
                <span id="password_error"></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="forgot_password.php">Forgot password?</a>
            </div>
            <button type="submit" name="login_btn" class="btn btn-cafe w-100">Login</button>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
