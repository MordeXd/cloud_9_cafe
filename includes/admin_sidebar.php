<?php
// Reusable sidebar for admin pages.
?>
<aside class="sidebar-card">
    <h4 class="h6">Admin Panel</h4>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'menu_items' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/menu_items.php">Menu Items</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/categories.php">Categories</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'reservations' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/reservations.php">Reservations</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/orders.php">Orders</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/users.php">Users</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'admin_register' ? 'active' : '' ?>" href="/cloud_9_cafe/admin_register.php">Admin Accounts</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'feedback' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/feedback.php">Feedback</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'popular_picks' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/popular_picks.php">Popular Picks</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'settings' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/settings.php">Settings</a></li>
        <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/logout.php">Logout</a></li>
    </ul>
</aside>
