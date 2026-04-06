<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php'; // MySQL
require_once '../config/TokenAuth.php';
$auth = new TokenAuth();

// Helper to add column if missing
function ensureColumn($con, $table, $col, $definition) {
    $safeTable = mysqli_real_escape_string($con, $table);
    $safeCol   = mysqli_real_escape_string($con, $col);
    $checkSql = "
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$safeTable' AND COLUMN_NAME = '$safeCol'
        LIMIT 1";
    $exists = mysqli_query($con, $checkSql);
    if ($exists && mysqli_num_rows($exists) > 0) {
        mysqli_free_result($exists);
        return;
    }
    mysqli_query($con, "ALTER TABLE `$table` ADD COLUMN $definition");
}

$pageTitle = 'Popular Picks';
$activePage = 'popular_picks';
$message = '';
$messageType = 'success';

// Handle POST updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $featuredIds = isset($_POST['featured']) && is_array($_POST['featured']) ? array_map('intval', $_POST['featured']) : [];
    // Ensure columns exist
    ensureColumn($con, 'menu_items', 'featured', "featured TINYINT(1) NOT NULL DEFAULT 0");
    foreach ($featuredIds as $fid) {
        mysqli_query($con, "UPDATE menu_items SET featured = 1 WHERE id = $fid");
    }
    // Clear others
    $idsStr = $featuredIds ? implode(',', $featuredIds) : '0';
    mysqli_query($con, "UPDATE menu_items SET featured = 0 WHERE id NOT IN ($idsStr)");
    $message = 'Popular picks updated.';
}

// Ensure columns exist
ensureColumn($con, 'menu_items', 'availability', "availability VARCHAR(20) NOT NULL DEFAULT 'Available'");
ensureColumn($con, 'menu_items', 'updated_at', "updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

// Reload items after potential update
$menuItems = [];
$res = mysqli_query($con, "SELECT mi.*, c.category_name FROM menu_items mi LEFT JOIN categories c ON mi.category_id = c.id ORDER BY mi.updated_at DESC, mi.id DESC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $menuItems[] = $row;
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/admin_sidebar.php'; ?>
        <section class="content-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h1 class="h4 mb-0">Popular Picks (homepage)</h1>
                <small class="text-muted">Select up to 4 items to feature</small>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width:60px;">Feature</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Availability</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($menuItems)): ?>
                                <?php foreach ($menuItems as $item): ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="featured[]" value="<?= $item['id']; ?>" <?= !empty($item['featured']) ? 'checked' : ''; ?>>
                                        </td>
                                        <td><?= htmlspecialchars($item['name'] ?? ($item['item_name'] ?? '')); ?></td>
                                        <td><?= htmlspecialchars($item['category'] ?? ($item['category_name'] ?? '')); ?></td>
                                        <td>₹<?= number_format($item['price'] ?? 0, 0); ?></td>
                                        <td><?= htmlspecialchars($item['availability'] ?? $item['status'] ?? ''); ?></td>
                                        <td><?= htmlspecialchars($item['updated_at'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No menu items found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mb-2">Tip: Popular Picks on the homepage show Featured items first (up to 4). If fewer than 4 are featured, top sellers and available items fill the remaining slots automatically.</p>
                <button type="submit" class="btn btn-primary">Save Popular Picks</button>
            </form>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
