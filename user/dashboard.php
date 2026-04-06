<?php
require_once '../includes/auth.php';
requireUser();
require_once '../config/db.php';
$pageTitle = 'User Dashboard - Cloud 9 Cafe';
$activePage = 'dashboard';

$userId = currentUserId();
$reservationCount = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM reservations WHERE user_id = $userId"))[0] ?? 0;
$orderCount = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM orders WHERE user_id = $userId"))[0] ?? 0;
$cartCount = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM cart WHERE user_id = $userId"))[0] ?? 0;

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/user_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-3">Welcome to your dashboard</h1>
            <div class="row g-3">
                <div class="col-md-4"><div class="stat-box"><h5>Reservations</h5><p class="mb-1 fs-4 fw-bold"><?= $reservationCount ?></p><p class="mb-0">Manage your table bookings.</p></div></div>
                <div class="col-md-4"><div class="stat-box"><h5>Orders</h5><p class="mb-1 fs-4 fw-bold"><?= $orderCount ?></p><p class="mb-0">Track your cafe orders.</p></div></div>
                <div class="col-md-4"><div class="stat-box"><h5>Cart Items</h5><p class="mb-1 fs-4 fw-bold"><?= $cartCount ?></p><p class="mb-0">Review items ready for checkout.</p></div></div>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>