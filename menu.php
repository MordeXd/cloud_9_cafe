<?php
// Menu page (frontend refreshed to match rebuild design; backend logic unchanged)
include 'config/db.php';
$pageTitle = 'Menu - Cloud 9 Cafe';

$message = '';
$messageType = '';

// Handle add to cart
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

// Filters
$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$search = trim($_GET['search'] ?? '');

// Categories list
$categories = [];
$catResult = mysqli_query($con, "SELECT id, category_name FROM categories ORDER BY category_name ASC");
if ($catResult) {
    while ($row = mysqli_fetch_assoc($catResult)) {
        $categories[] = $row;
    }
}

// Menu items query with filters (frontend only; same DB backend)
$where = ["mi.status='active'"];
if ($categoryId > 0) {
    $where[] = "mi.category_id = $categoryId";
}
if ($search !== '') {
    $safe = mysqli_real_escape_string($con, $search);
    $where[] = "(mi.item_name LIKE '%$safe%' OR mi.description LIKE '%$safe%')";
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$menuQuery = "
    SELECT mi.*, c.category_name 
    FROM menu_items mi 
    LEFT JOIN categories c ON mi.category_id = c.id
    $whereSql
    ORDER BY c.category_name ASC, mi.item_name ASC
";
$menuItems = mysqli_query($con, $menuQuery);

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-dark) 100%);">
    <div class="container">
        <div class="row justify-content-center text-center text-white">
            <div class="col-lg-8 animate-fade-in-up">
                <h1 class="fw-bold text-white mb-3">Our Menu</h1>
                <p class="lead opacity-75 text-white mb-0">Discover our handcrafted selection of premium coffees, delicious snacks, and delightful desserts.</p>
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section class="py-5" style="background: var(--bg-cream);">
    <div class="container">
        <!-- Filters -->
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <form method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search menu..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <?php if ($categoryId): ?>
                        <input type="hidden" name="category" value="<?= $categoryId ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search !== '' || $categoryId): ?>
                        <a href="menu.php" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-lg-6">
                <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                    <a href="menu.php" class="btn <?= !$categoryId ? 'btn-primary' : 'btn-outline-secondary'; ?>">All</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="?category=<?= (int)$cat['id']; ?>" class="btn <?= $categoryId === (int)$cat['id'] ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                            <?= htmlspecialchars($cat['category_name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Alert message -->
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Menu Grid -->
        <?php if (!$menuItems || mysqli_num_rows($menuItems) === 0): ?>
            <div class="text-center py-5 animate-fade-in">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x text-muted"></i>
                </div>
                <h4 class="fw-bold mb-2">No items found</h4>
                <p class="text-muted mb-4">Try adjusting your search or filter criteria.</p>
                <a href="menu.php" class="btn btn-primary">View All Items</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php 
                $stagger = 0;
                while ($item = mysqli_fetch_assoc($menuItems)): 
                    $stagger = ($stagger + 1) % 5;
                    $imagePath = '';
                    if (!empty($item['item_image'])) {
                        $cleanName = basename($item['item_image']);
                        $uploadPath = "/cloud_9_cafe/uploads/{$cleanName}";
                        $imagePath = $uploadPath;
                    }
                ?>
                <div class="col-md-6 col-lg-4 col-xl-3 animate-on-scroll stagger-<?= $stagger; ?>">
                    <div class="card product-card card-hover h-100">
                        <div class="product-image" style="position: relative;">
                            <?php if ($imagePath): ?>
                                <img src="<?= htmlspecialchars($imagePath); ?>" alt="<?= htmlspecialchars($item['item_name']); ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <?php endif; ?>
                            <!-- Placeholder -->
                            <div class="product-placeholder" style="display: <?= $imagePath ? 'none' : 'flex'; ?>; 
                                 position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                                 background: linear-gradient(135deg, var(--cafe-primary-light) 0%, var(--cafe-primary) 100%);
                                 align-items: center; justify-content: center; flex-direction: column;">
                                <i class="fas fa-coffee fa-4x text-white mb-2"></i>
                                <span class="text-white small">Cloud 9 Cafe</span>
                            </div>
                            <div class="product-overlay">
                                <form method="POST" action="menu.php" class="d-inline">
                                    <input type="hidden" name="menu_item_id" value="<?= (int)$item['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart_btn" class="btn btn-accent rounded-pill">
                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-white" style="font-size: 0.7rem;">
                                    <?= htmlspecialchars($item['category_name'] ?? 'Menu'); ?>
                                </span>
                            </div>
                            <h5 class="product-title"><?= htmlspecialchars($item['item_name']); ?></h5>
                            <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($item['description']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">&#8377;<?= htmlspecialchars(number_format((float)$item['price'], 2)); ?></span>
                                <?php if ($isLoggedIn): ?>
                                    <form method="POST" action="menu.php" class="d-inline">
                                        <input type="hidden" name="menu_item_id" value="<?= (int)$item['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="add_to_cart_btn" class="btn btn-sm btn-outline-primary rounded-pill">
                                            Add <i class="fas fa-plus ms-1"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="/cloud_9_cafe/auth/login.php" class="btn btn-sm btn-outline-primary rounded-pill">
                                        Login to order
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container">
        <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, var(--cafe-accent) 0%, #E8C9A0 100%);">
            <div class="card-body p-5 text-center">
                <h3 class="fw-bold mb-3" style="color: var(--cafe-primary-dark);">
                    <i class="fas fa-percent me-2"></i>Special Offer
                </h3>
                <p class="mb-4" style="color: var(--cafe-primary-dark);">Get 10% off when you order 3 or more items. Use code <strong>BUNDLE10</strong></p>
                <a href="/cloud_9_cafe/user/cart.php" class="btn btn-primary btn-lg">
                    View Cart <i class="fas fa-shopping-cart ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
