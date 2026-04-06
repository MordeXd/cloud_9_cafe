<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Admin Dashboard - Cloud 9 Cafe';
$activePage = 'dashboard';

$totalOrders = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM orders"))[0] ?? 0;
$totalReservations = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM reservations"))[0] ?? 0;
$totalUsers = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM users WHERE role='user'"))[0] ?? 0;
$totalMenuItems = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM menu_items"))[0] ?? 0;

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/admin_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-3">Admin Dashboard</h1>
            <div class="row g-3">
                <div class="col-md-3"><div class="stat-box"><h5>Total Orders</h5><p class="mb-1 fs-4 fw-bold"><?= $totalOrders ?></p><p class="mb-0">Monitor all customer orders.</p></div></div>
                <div class="col-md-3"><div class="stat-box"><h5>Reservations</h5><p class="mb-1 fs-4 fw-bold"><?= $totalReservations ?></p><p class="mb-0">Manage upcoming bookings.</p></div></div>
                <div class="col-md-3"><div class="stat-box"><h5>Users</h5><p class="mb-1 fs-4 fw-bold"><?= $totalUsers ?></p><p class="mb-0">Check customer accounts.</p></div></div>
                <div class="col-md-3"><div class="stat-box"><h5>Menu Items</h5><p class="mb-1 fs-4 fw-bold"><?= $totalMenuItems ?></p><p class="mb-0">Control products and pricing.</p></div></div>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>