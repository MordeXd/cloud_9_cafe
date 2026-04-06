<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Users - Cloud 9 Cafe';

$message = '';
$messageType = '';

// Delete admin account (only admins, prevent self-delete)
if (isset($_POST['delete_admin_btn'])) {
    $deleteId = (int) ($_POST['delete_admin_id'] ?? 0);
    $currentId = (int) ($_SESSION['user_id'] ?? 0);

    if ($deleteId === 0) {
        $message = 'Invalid admin selected.';
        $messageType = 'danger';
    } elseif ($deleteId === $currentId) {
        $message = 'You cannot delete your own admin account.';
        $messageType = 'warning';
    } else {
        $deleteSql = "DELETE FROM users WHERE id = $deleteId AND role = 'admin'";
        if (mysqli_query($con, $deleteSql) && mysqli_affected_rows($con) > 0) {
            $message = 'Admin account deleted.';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete admin account.';
            $messageType = 'danger';
        }
    }
}

if (isset($_POST['user_btn'])) {
    $userName = cleanInput($_POST['user_name']);
    $email = cleanInput($_POST['email']);
    $role = cleanInput($_POST['role']);
    $status = cleanInput($_POST['status']);

    $updateQuery = "UPDATE users SET full_name='$userName', role='$role', status='$status' WHERE email='$email'";
    if (mysqli_query($con, $updateQuery)) {
        $message = 'User updated successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to update user.';
        $messageType = 'danger';
    }
}

$userList = mysqli_query($con, "SELECT * FROM users ORDER BY id DESC");
include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <aside class="sidebar-card">
            <h4 class="h6">Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="menu_items.php">Menu Items</a></li>
                <li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="reservations.php">Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link active" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/logout.php">Logout</a></li>
            </ul>
        </aside>
        <section class="content-card">
            <h1 class="h3 mb-4">User Management</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" class="table-form-wrap mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">User Name</label>
                        <input type="text" name="user_name" class="form-control" data-validation="required min alphabetic max" data-min="2" data-max="50">
                        <span id="user_name_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" data-validation="required email">
                        <span id="email_error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" data-validation="required select">
                            <option value="">Select Role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <span id="role_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" data-validation="required select">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <span id="status_error"></span>
                    </div>
                </div>
                <button type="submit" name="user_btn" class="btn btn-cafe">Save User</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($userList && mysqli_num_rows($userList) > 0): ?>
                            <?php while ($user = mysqli_fetch_assoc($userList)): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                    <td><?= htmlspecialchars($user['status']) ?></td>
                                    <td>
                                        <?php if (strtolower($user['role']) === 'admin'): ?>
                                            <form method="post" action="" onsubmit="return confirm('Delete this admin account?');" class="d-inline">
                                                <input type="hidden" name="delete_admin_id" value="<?= (int)$user['id']; ?>">
                                                <button type="submit" name="delete_admin_btn" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
