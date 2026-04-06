<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Reservations - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['reservation_update_btn'])) {
    $reservationId = (int) $_POST['reservation_id'];
    $status = cleanInput($_POST['reservation_status']);

    $updateQuery = "UPDATE reservations SET status='$status' WHERE id=$reservationId";
    if (mysqli_query($con, $updateQuery)) {
        $message = 'Reservation updated successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to update reservation.';
        $messageType = 'danger';
    }
}

$reservationList = mysqli_query($con, "SELECT reservations.*, users.full_name FROM reservations LEFT JOIN users ON reservations.user_id = users.id ORDER BY reservations.id DESC");
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
                <li class="nav-item"><a class="nav-link active" href="reservations.php">Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/logout.php">Logout</a></li>
            </ul>
        </aside>
        <section class="content-card">
            <h1 class="h3 mb-4">Reservation Management</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" class="table-form-wrap mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reservation ID</label>
                        <input type="text" name="reservation_id" class="form-control" data-validation="required min max" data-min="1" data-max="20">
                        <span id="reservation_id_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="reservation_status" class="form-select" data-validation="required select">
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <span id="reservation_status_error"></span>
                    </div>
                </div>
                <button type="submit" name="reservation_update_btn" class="btn btn-cafe">Update Reservation</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>User</th><th>Date</th><th>Time</th><th>Guests</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if ($reservationList && mysqli_num_rows($reservationList) > 0): ?>
                            <?php while ($reservation = mysqli_fetch_assoc($reservationList)): ?>
                                <tr>
                                    <td><?= $reservation['id'] ?></td>
                                    <td><?= htmlspecialchars($reservation['full_name'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($reservation['reservation_date']) ?></td>
                                    <td><?= htmlspecialchars($reservation['reservation_time']) ?></td>
                                    <td><?= htmlspecialchars($reservation['guest_count']) ?></td>
                                    <td><?= htmlspecialchars($reservation['status']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No reservations found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>