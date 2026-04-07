<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Orders - Cloud 9 Cafe';
$activePage = 'orders';
$dashboardSearchEnabled = true;

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

$statusFilter = strtolower(trim($_GET['status'] ?? ''));
$allowedStatuses = ['pending', 'preparing', 'completed', 'cancelled'];
$statusSql = '';
if (in_array($statusFilter, $allowedStatuses, true)) {
    $statusSql = "WHERE orders.order_status = '$statusFilter'";
}
$orderList = mysqli_query($con, "SELECT orders.*, users.full_name FROM orders LEFT JOIN users ON orders.user_id = users.id $statusSql ORDER BY orders.id DESC");
include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/admin_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-4">Order Management</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="get" class="d-flex flex-wrap gap-2 align-items-center mb-4">
                <select name="status" class="form-select" style="max-width: 220px;">
                    <option value="">All Status</option>
                    <?php foreach ($allowedStatuses as $statusOption): ?>
                        <option value="<?= $statusOption ?>" <?= $statusFilter === $statusOption ? 'selected' : '' ?>>
                            <?= ucfirst($statusOption) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-outline-secondary">Filter</button>
                <?php if ($statusFilter !== ''): ?>
                    <a href="orders.php" class="btn btn-light">Clear</a>
                <?php endif; ?>
            </form>
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
                                    <?php
                                        $status = strtolower($order['order_status'] ?? '');
                                        $badgeClass = 'bg-warning';
                                        if ($status === 'completed') $badgeClass = 'bg-success';
                                        if ($status === 'preparing') $badgeClass = 'bg-info';
                                        if ($status === 'cancelled') $badgeClass = 'bg-danger';
                                    ?>
                                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars(ucfirst($order['order_status'])) ?></span></td>
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
