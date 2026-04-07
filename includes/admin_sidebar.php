<?php
// Reusable sidebar for admin pages.
?>
<aside class="sidebar-card">
    <h4 class="h6">Admin Panel</h4>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/dashboard.php"><i class="fas fa-gauge-high"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'menu_items' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/menu_items.php"><i class="fas fa-mug-hot"></i>Menu Items</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/categories.php"><i class="fas fa-tags"></i>Categories</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'reservations' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/reservations.php"><i class="fas fa-calendar-check"></i>Reservations</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/orders.php"><i class="fas fa-receipt"></i>Orders</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/users.php"><i class="fas fa-users"></i>Users</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'admin_register' ? 'active' : '' ?>" href="/cloud_9_cafe/admin_register.php"><i class="fas fa-user-shield"></i>Admin Accounts</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'feedback' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/feedback.php"><i class="fas fa-comment-dots"></i>Feedback</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'popular_picks' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/popular_picks.php"><i class="fas fa-star"></i>Popular Picks</a></li>
        <li class="nav-item"><a class="nav-link <?= ($activePage ?? '') === 'settings' ? 'active' : '' ?>" href="/cloud_9_cafe/admin/settings.php"><i class="fas fa-gear"></i>Settings</a></li>
        <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/auth/logout.php"><i class="fas fa-right-from-bracket"></i>Logout</a></li>
    </ul>
</aside>

