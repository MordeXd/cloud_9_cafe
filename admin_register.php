<?php
// Public admin registration page (no login required).
require_once './config/db.php';
$pageTitle = 'Admin Accounts - Cloud 9 Cafe';
$activePage = 'admin_register';

$message = '';
$messageType = '';

if (isset($_POST['create_admin_btn'])) {
    $fullName = cleanInput($_POST['full_name']);
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirmPassword'] ?? '';

    if ($password !== $confirm) {
        $message = 'Passwords do not match.';
        $messageType = 'danger';
    } else {

        $exists = mysqli_query($con, "SELECT id FROM users WHERE email='$email' LIMIT 1");
        if ($exists && mysqli_num_rows($exists) > 0) {
            $message = 'Email already exists. Choose another.';
            $messageType = 'danger';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO users (full_name, email, phone, password, gender, role, status, is_verified, activation_token)
                       VALUES ('$fullName', '$email', '$phone', '$hashed', 'other', 'admin', 'active', 1, NULL)";
            if (mysqli_query($con, $insert)) {
                $message = 'Admin created successfully.';
                $messageType = 'success';
            } else {
                $message = 'Failed to create admin.';
                $messageType = 'danger';
            }
        }
    }
}

$admins = mysqli_query($con, "SELECT id, full_name, email, phone, status, created_at FROM users WHERE role='admin' ORDER BY id DESC");

include './includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include './includes/admin_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-4">Admin Accounts</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" class="mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" data-validation="required min alphabetic max" data-min="2" data-max="50">
                        <span id="full_name_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" data-validation="required email">
                        <span id="email_error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" data-validation="required number min max" data-min="10" data-max="10">
                        <span id="phone_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" id="confirmPassword_confirm" name="password" class="form-control" data-validation="required strongPassword">
                        <span id="password_error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" id="password" name="confirmPassword" class="form-control" data-validation="required confirmPassword">
                        <span id="confirmPassword_error"></span>
                    </div>
                </div>
                <button type="submit" name="create_admin_btn" class="btn btn-cafe">Create Admin</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Created</th></tr></thead>
                    <tbody>
                        <?php if ($admins && mysqli_num_rows($admins) > 0): ?>
                            <?php while ($admin = mysqli_fetch_assoc($admins)): ?>
                                <tr>
                                    <td><?= $admin['id'] ?></td>
                                    <td><?= htmlspecialchars($admin['full_name']) ?></td>
                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                    <td><?= htmlspecialchars($admin['phone']) ?></td>
                                    <td><?= htmlspecialchars($admin['status']) ?></td>
                                    <td><?= htmlspecialchars($admin['created_at']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No admin accounts found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include './includes/footer.php'; ?>
