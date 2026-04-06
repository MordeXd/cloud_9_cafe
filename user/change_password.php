<?php
// User change password page
require_once '../includes/auth.php';
requireUser();
require_once '../config/db.php';
$pageTitle = 'Change Password - Cloud 9 Cafe';

$message = '';
$messageType = '';
$userId = currentUserId();

if (isset($_POST['password_btn'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['password'];

    $query = mysqli_query($con, "SELECT password FROM users WHERE id = $userId LIMIT 1");
    $user = $query ? mysqli_fetch_assoc($query) : null;

    if ($user && password_verify($currentPassword, $user['password'])) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET password='$hashedPassword' WHERE id=$userId";

        if (mysqli_query($con, $updateQuery)) {
            $message = 'Password updated successfully.';
            $messageType = 'success';
        } else {
            $message = 'Password update failed.';
            $messageType = 'danger';
        }
    } else {
        $message = 'Current password is incorrect.';
        $messageType = 'danger';
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <aside class="sidebar-card">
            <h4 class="h6">User Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="reservations.php">Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link active" href="change_password.php">Change Password</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/logout.php">Logout</a></li>
            </ul>
        </aside>
        <section class="content-card">
            <h1 class="h3 mb-4">Change Password</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" data-validation="required">
                    <span id="current_password_error"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" id="confirmPassword_confirm" name="password" class="form-control" data-validation="required strongPassword">
                    <span id="password_error"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" id="password" name="confirmPassword" class="form-control" data-validation="required confirmPassword">
                    <span id="confirmPassword_error"></span>
                </div>
                <button type="submit" name="password_btn" class="btn btn-cafe">Update Password</button>
            </form>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>