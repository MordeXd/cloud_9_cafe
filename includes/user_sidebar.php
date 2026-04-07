<?php
// Reusable sidebar for logged-in user pages.
?>
<aside class="sidebar-card">
    <h4 class="h6">User Panel</h4>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="dashboard.php"><i class="fas fa-gauge-high"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'menu' ? 'active' : '' ?>" href="menu.php"><i class="fas fa-mug-hot"></i>Menu</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>" href="profile.php"><i class="fas fa-user"></i>Profile</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'reservations' ? 'active' : '' ?>" href="reservations.php"><i class="fas fa-calendar-check"></i>Reservations</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>" href="orders.php"><i class="fas fa-receipt"></i>Orders</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'cart' ? 'active' : '' ?>" href="cart.php"><i class="fas fa-shopping-cart"></i>Cart</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'feedback' ? 'active' : '' ?>" href="feedback.php"><i class="fas fa-comment-dots"></i>Feedback</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'change_password' ? 'active' : '' ?>" href="change_password.php"><i class="fas fa-key"></i>Change Password</a></li>
        <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/auth/logout.php"><i class="fas fa-right-from-bracket"></i>Logout</a></li>
    </ul>
</aside>
