<?php
// User orders page
require_once '../includes/auth.php';
requireUser();
require_once '../config/db.php';
$pageTitle = 'My Orders - Cloud 9 Cafe';
$activePage = 'orders';

$userId = currentUserId();
$orderList = mysqli_query($con, "SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC");

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/user_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-4">My Orders</h1>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orderList && mysqli_num_rows($orderList) > 0): ?>
                            <?php while ($order = mysqli_fetch_assoc($orderList)): ?>
                                <?php
                                    $status = strtolower($order['order_status'] ?? '');
                                    $badgeClass = 'bg-warning';
                                    if ($status === 'completed') $badgeClass = 'bg-success';
                                    if ($status === 'preparing') $badgeClass = 'bg-info';
                                    if ($status === 'cancelled') $badgeClass = 'bg-danger';
                                ?>
                                <tr>
                                    <td>#ORD<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                                    <td>₹<?= htmlspecialchars($order['total_amount']) ?></td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars(ucfirst($order['order_status'])) ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
