<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Orders - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['order_update_btn'])) {
    $orderId = (int) $_POST['order_id'];
    $status = cleanInput($_POST['order_status']);

    $updateQuery = "UPDATE orders SET order_status='$status' WHERE id=$orderId";
    if (mysqli_query($con, $updateQuery)) {
        $message = 'Order updated successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to update order.';
        $messageType = 'danger';
    }
}

$orderList = mysqli_query($con, "SELECT orders.*, users.full_name FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY orders.id DESC");
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
                <li class="nav-item"><a class="nav-link active" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/logout.php">Logout</a></li>
            </ul>
        </aside>
        <section class="content-card">
            <h1 class="h3 mb-4">Order Management</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" class="table-form-wrap mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order ID</label>
                        <input type="text" name="order_id" class="form-control" data-validation="required min max" data-min="1" data-max="20">
                        <span id="order_id_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order Status</label>
                        <select name="order_status" class="form-select" data-validation="required select">
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="preparing">Preparing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <span id="order_status_error"></span>
                    </div>
                </div>
                <button type="submit" name="order_update_btn" class="btn btn-cafe">Update Order</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Created At</th></tr></thead>
                    <tbody>
                        <?php if ($orderList && mysqli_num_rows($orderList) > 0): ?>
                            <?php while ($order = mysqli_fetch_assoc($orderList)): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['full_name'] ?? 'Unknown') ?></td>
                                    <td>₹<?= htmlspecialchars($order['total_amount']) ?></td>
                                    <td><?= htmlspecialchars($order['order_status']) ?></td>
                                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>