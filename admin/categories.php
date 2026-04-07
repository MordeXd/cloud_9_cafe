<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Categories - Cloud 9 Cafe';
$activePage = 'categories';

$message = '';
$messageType = '';

if (isset($_POST['category_btn'])) {
    $categoryName = cleanInput($_POST['category_name']);
    $status = cleanInput($_POST['status']);

    $insertQuery = "INSERT INTO categories (category_name, status) VALUES ('$categoryName', '$status')";
    if (mysqli_query($con, $insertQuery)) {
        $message = 'Category saved successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to save category.';
        $messageType = 'danger';
    }
}

$categoryList = mysqli_query($con, "SELECT * FROM categories ORDER BY id DESC");
include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/admin_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-4">Manage Categories</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" class="table-form-wrap mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category_name" class="form-control" data-validation="required min max" data-min="2" data-max="50">
                        <span id="category_name_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" data-validation="required select">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <span id="status_error"></span>
                    </div>
                </div>
                <button type="submit" name="category_btn" class="btn btn-cafe">Save Category</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>Name</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if ($categoryList && mysqli_num_rows($categoryList) > 0): ?>
                            <?php while ($category = mysqli_fetch_assoc($categoryList)): ?>
                                <tr>
                                    <td><?= $category['id'] ?></td>
                                    <td><?= htmlspecialchars($category['category_name']) ?></td>
                                    <td><?= htmlspecialchars($category['status']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">No categories found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
