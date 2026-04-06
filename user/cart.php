<?php
require_once '../includes/auth.php';
requireUser();
require_once '../config/db.php';
$pageTitle = 'My Cart - Cloud 9 Cafe';
$activePage = 'cart';

$message = '';
$messageType = '';
$userId = currentUserId();

if (isset($_POST['update_cart_btn'])) {
    $cartId = (int) $_POST['cart_id'];
    $quantity = max(1, (int) $_POST['quantity']);
    if (mysqli_query($con, "UPDATE cart SET quantity = $quantity WHERE id = $cartId AND user_id = $userId")) {
        $message = 'Cart updated successfully.';
        $messageType = 'success';
    }
}

if (isset($_POST['remove_cart_btn'])) {
    $cartId = (int) $_POST['cart_id'];
    if (mysqli_query($con, "DELETE FROM cart WHERE id = $cartId AND user_id = $userId")) {
        $message = 'Item removed from cart.';
        $messageType = 'success';
    }
}

if (isset($_POST['clear_cart_btn'])) {
    if (mysqli_query($con, "DELETE FROM cart WHERE user_id = $userId")) {
        $message = 'Cart cleared successfully.';
        $messageType = 'success';
    }
}

if (isset($_POST['checkout_btn'])) {
    $cartItemsForOrder = mysqli_query($con, "SELECT cart.quantity, menu_items.price FROM cart LEFT JOIN menu_items ON cart.menu_item_id = menu_items.id WHERE cart.user_id = $userId");
    $totalAmount = 0;
    if ($cartItemsForOrder) {
        while ($row = mysqli_fetch_assoc($cartItemsForOrder)) {
            $totalAmount += ($row['price'] * $row['quantity']);
        }
    }

    if ($totalAmount > 0) {
        $insertOrder = mysqli_query($con, "INSERT INTO orders (user_id, total_amount, order_status) VALUES ($userId, '$totalAmount', 'pending')");
        if ($insertOrder) {
            mysqli_query($con, "DELETE FROM cart WHERE user_id = $userId");
            $message = 'Checkout completed and cart converted to order successfully.';
            $messageType = 'success';
        } else {
            $message = 'Checkout failed.';
            $messageType = 'danger';
        }
    } else {
        $message = 'Your cart is empty.';
        $messageType = 'danger';
    }
}

$cartQuery = mysqli_query($con, "SELECT cart.*, menu_items.item_name, menu_items.price, menu_items.item_image
                                 FROM cart
                                 LEFT JOIN menu_items ON cart.menu_item_id = menu_items.id
                                 WHERE cart.user_id = $userId
                                 ORDER BY cart.id DESC");

$cartItems = [];
$grandTotal = 0;
if ($cartQuery) {
    while ($row = mysqli_fetch_assoc($cartQuery)) {
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $grandTotal += $row['subtotal'];
        $cartItems[] = $row;
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/user_sidebar.php'; ?>
        <section class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">My Cart</h1>
                <a href="/cloud_9_cafe/menu.php" class="btn btn-outline-secondary">Back to Menu</a>
            </div>

            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>

            <?php if (!empty($cartItems)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td>₹<?= htmlspecialchars($item['price']) ?></td>
                                    <td>
                                        <form method="post" action="" class="d-flex gap-2">
                                            <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control" style="max-width:90px;">
                                            <button type="submit" name="update_cart_btn" class="btn btn-sm btn-cafe">Update</button>
                                        </form>
                                    </td>
                                    <td>₹<?= number_format($item['subtotal'], 2) ?></td>
                                    <td>
                                        <form method="post" action="">
                                            <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                            <button type="submit" name="remove_cart_btn" class="btn btn-sm btn-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="checkout-summary mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Grand Total: ₹<?= number_format($grandTotal, 2) ?></h4>
                        <form method="post" action="">
                            <button type="submit" name="checkout_btn" class="btn btn-cafe">Checkout</button>
                        </form>
                    </div>
                    <form method="post" action="">
                        <button type="submit" name="clear_cart_btn" class="btn btn-outline-danger">Clear Cart</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Your cart is empty.</div>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>