<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db_config.php'; // JsonDB + TokenAuth

$pageTitle = 'Popular Picks';
$activePage = 'popular_picks';
$message = '';
$messageType = 'success';

// Handle POST updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $featuredIds = isset($_POST['featured']) && is_array($_POST['featured']) ? array_map('intval', $_POST['featured']) : [];

    // Fetch all menu items
    $items = $db->select('menu_items');
    foreach ($items as $item) {
        $id = $item['id'];
        $isFeatured = in_array($id, $featuredIds, true) ? 1 : 0;
        if ((int)($item['featured'] ?? 0) !== $isFeatured) {
            $db->update('menu_items', ['featured' => $isFeatured], ['id' => $id]);
        }
    }
    $message = 'Popular picks updated.';
}

// Reload items after potential update
$menuItems = $db->select('menu_items', ['availability' => 'Available'], ['updated_at' => 'DESC']);

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
