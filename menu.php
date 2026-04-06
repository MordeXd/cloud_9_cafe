<?php
include 'config/db.php';
$pageTitle = 'Menu - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['add_to_cart_btn'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /cloud_9_cafe/auth/login.php');
        exit;
    }

    $userId = (int) $_SESSION['user_id'];
    $menuItemId = (int) $_POST['menu_item_id'];
    $quantity = max(1, (int) $_POST['quantity']);

    $checkCart = mysqli_query($con, "SELECT id, quantity FROM cart WHERE user_id = $userId AND menu_item_id = $menuItemId LIMIT 1");
    if ($checkCart && mysqli_num_rows($checkCart) > 0) {
        $cartRow = mysqli_fetch_assoc($checkCart);
        $newQty = $cartRow['quantity'] + $quantity;
        $result = mysqli_query($con, "UPDATE cart SET quantity = $newQty WHERE id = {$cartRow['id']}");
    } else {
        $result = mysqli_query($con, "INSERT INTO cart (user_id, menu_item_id, quantity) VALUES ($userId, $menuItemId, $quantity)");
    }

    if ($result) {
        $message = 'Item added to cart successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to add item to cart.';
        $messageType = 'danger';
    }
}

$menuItems = mysqli_query($con, "SELECT menu_items.*, categories.category_name FROM menu_items LEFT JOIN categories ON menu_items.category_id = categories.id WHERE menu_items.status='active' ORDER BY menu_items.id DESC");
include 'includes/header.php';
?>
<div class="container">
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h2 mb-1">Our Menu</h1>
                <p class="text-muted mb-0">Browse menu items and add them to your cart.</p>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/cloud_9_cafe/user/cart.php" class="btn btn-cafe">View Cart</a>
            <?php endif; ?>
        </div>

        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
        <?php endif; ?>

        <div class="row g-4 mt-2">
            <?php if ($menuItems && mysqli_num_rows($menuItems) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($menuItems)): ?>
                    <div class="col-md-4">
                        <div class="card section-card h-100">
                            <div class="card-body d-flex flex-column">
                                <?php if (!empty($item['item_image'])): ?>
                                    <img src="/cloud_9_cafe/uploads/<?= htmlspecialchars($item['item_image']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>" class="menu-card-image">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80" alt="Menu item" class="menu-card-image">
                                <?php endif; ?>
                                <h5><?= htmlspecialchars($item['item_name']) ?></h5>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($item['category_name'] ?? 'General') ?></p>
                                <p class="mb-3"><?= htmlspecialchars($item['description']) ?></p>
                                <div class="mt-auto">
                                    <p class="fw-bold mb-3">₹<?= htmlspecialchars($item['price']) ?></p>
                                    <form method="post" action="">
                                        <input type="hidden" name="menu_item_id" value="<?= $item['id'] ?>">
                                        <div class="input-group mb-2">
                                            <input type="number" name="quantity" class="form-control" value="1" min="1">
                                            <button type="submit" name="add_to_cart_btn" class="btn btn-cafe">Add to Cart</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12"><div class="alert alert-info">No menu items available yet.</div></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>