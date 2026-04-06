<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';

$pageTitle = 'Manage Menu Items - Cloud 9 Cafe';
$activePage = 'menu_items';

$message = '';
$messageType = '';

// Get categories for select
$categories = mysqli_query($con, "SELECT id, category_name FROM categories WHERE status='active' ORDER BY category_name");

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    if ($deleteId > 0) {
        mysqli_query($con, "DELETE FROM menu_items WHERE id=$deleteId");
        header('Location: menu_items.php?msg=deleted');
        exit;
    }
}

// Surface delete message
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Menu item deleted.';
    $messageType = 'success';
}

// Detect edit mode
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editItem = null;
if ($editId > 0) {
    $res = mysqli_query($con, "SELECT * FROM menu_items WHERE id=$editId LIMIT 1");
    $editItem = $res ? mysqli_fetch_assoc($res) : null;
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (isset($_POST['menu_btn'])) {
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $itemName = cleanInput($_POST['item_name']);
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $description = cleanInput($_POST['description']);
    $price = (float)($_POST['price'] ?? 0);
    $status = cleanInput($_POST['status']);

    $imageSql = '';
    if (!empty($_FILES['item_image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['item_image']['name']);
        move_uploaded_file($_FILES['item_image']['tmp_name'], $uploadDir . $imageName);
        $imageSql = ", item_image='$imageName'";
    }

    if ($itemId > 0) {
        // Update existing
        $update = "UPDATE menu_items SET category_id=$categoryId, item_name='$itemName', description='$description', price=$price, status='$status' $imageSql WHERE id=$itemId";
        if (mysqli_query($con, $update)) {
            $message = 'Menu item updated successfully.';
            $messageType = 'success';
            header('Location: menu_items.php');
            exit;
        } else {
            $message = 'Failed to update menu item.';
            $messageType = 'danger';
        }
    } else {
        // Insert new
        $insert = "INSERT INTO menu_items (category_id, item_name, description, price, status" . ($imageSql ? ', item_image' : '') . ") VALUES ($categoryId, '$itemName', '$description', $price, '$status'" . ($imageSql ? ", '$imageName'" : '') . ")";
        if (mysqli_query($con, $insert)) {
            $message = 'Menu item added successfully.';
            $messageType = 'success';
            header('Location: menu_items.php');
            exit;
        } else {
            $message = 'Failed to add menu item.';
            $messageType = 'danger';
        }
    }
}

$menuList = mysqli_query($con, "SELECT menu_items.*, categories.category_name FROM menu_items LEFT JOIN categories ON menu_items.category_id = categories.id ORDER BY menu_items.id DESC");

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/admin_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-4"><?= $editItem ? 'Edit Menu Item' : 'Add Menu Item' ?></h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" enctype="multipart/form-data" class="mb-4">
                <input type="hidden" name="item_id" value="<?= $editItem['id'] ?? 0 ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="item_name" class="form-control" data-validation="required min max" data-min="2" data-max="100" value="<?= htmlspecialchars($editItem['item_name'] ?? '') ?>">
                        <span id="item_name_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" data-validation="required select">
                            <option value="">Select Category</option>
                            <?php if ($categories && mysqli_num_rows($categories) > 0): ?>
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($editItem['category_id']) && $editItem['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['category_name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                        <span id="category_id_error"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" data-validation="required min max" data-min="5" data-max="500"><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
                    <span id="description_error"></span>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" data-validation="required number" value="<?= htmlspecialchars($editItem['price'] ?? '') ?>">
                        <span id="price_error"></span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" data-validation="required select">
                            <option value="">Select Status</option>
                            <option value="active" <?= (isset($editItem['status']) && $editItem['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (isset($editItem['status']) && $editItem['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <span id="status_error"></span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="item_image" class="form-control" data-validation="fileSize fileType" data-filesize="2" data-filetype="jpg,jpeg,png">
                        <span id="item_image_error"></span>
                        <?php if (!empty($editItem['item_image'])): ?>
                            <small class="text-muted d-block mt-1">Current: <?= htmlspecialchars($editItem['item_image']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" name="menu_btn" class="btn btn-cafe"><?= $editItem ? 'Update Item' : 'Add Item' ?></button>
                <?php if ($editItem): ?>
                    <a href="menu_items.php" class="btn btn-outline-secondary ms-2">Cancel Edit</a>
                <?php endif; ?>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($menuList && mysqli_num_rows($menuList) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($menuList)): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></td>
                                    <td><?= htmlspecialchars($item['price']) ?></td>
                                    <td><?= htmlspecialchars($item['status']) ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-link" href="menu_items.php?edit=<?= $item['id'] ?>">Edit</a>
                                        <a class="btn btn-sm btn-link text-danger" href="menu_items.php?delete=<?= $item['id'] ?>" onclick="return confirm('Delete this menu item?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No menu items found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
