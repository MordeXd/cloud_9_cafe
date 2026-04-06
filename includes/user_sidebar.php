<?php
// Reusable sidebar for logged-in user pages.
?>
<aside class="sidebar-card">
    <h4 class="h6">User Panel</h4>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>" href="profile.php">Profile</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'reservations' ? 'active' : '' ?>" href="reservations.php">Reservations</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>" href="orders.php">Orders</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'cart' ? 'active' : '' ?>" href="cart.php">Cart</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'feedback' ? 'active' : '' ?>" href="feedback.php">Feedback</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'change_password' ? 'active' : '' ?>" href="change_password.php">Change Password</a></li>
        <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/auth/logout.php">Logout</a></li>
    </ul>
</aside>
