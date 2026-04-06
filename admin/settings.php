<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Cafe Settings - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['settings_btn'])) {
    $cafeName = cleanInput($_POST['cafe_name']);
    $contactEmail = cleanInput($_POST['contact_email']);
    $phone = cleanInput($_POST['phone']);
    $address = cleanInput($_POST['address']);

    $logoSql = '';
    if (!empty($_FILES['logo']['name'])) {
        $logoName = time() . '_' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], '../uploads/' . $logoName);
        $logoSql = ", logo='$logoName'";
    }

    $checkSettings = mysqli_query($con, "SELECT id FROM cafe_settings LIMIT 1");
    if ($checkSettings && mysqli_num_rows($checkSettings) > 0) {
        $settings = mysqli_fetch_assoc($checkSettings);
        $settingsId = $settings['id'];
        $updateQuery = "UPDATE cafe_settings SET cafe_name='$cafeName', contact_email='$contactEmail', phone='$phone', address='$address' $logoSql WHERE id=$settingsId";
        $result = mysqli_query($con, $updateQuery);
    } else {
        $logoInsert = $logoSql !== '' ? str_replace(', logo=', '', $logoSql) : "''";
        $insertQuery = "INSERT INTO cafe_settings (cafe_name, contact_email, phone, logo, address) VALUES ('$cafeName', '$contactEmail', '$phone', $logoInsert, '$address')";
        $result = mysqli_query($con, $insertQuery);
    }

    if ($result) {
        $message = 'Settings saved successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to save settings.';
        $messageType = 'danger';
    }
}

$settingsQuery = mysqli_query($con, "SELECT * FROM cafe_settings LIMIT 1");
$settingsData = $settingsQuery ? mysqli_fetch_assoc($settingsQuery) : null;

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <aside class="sidebar-card">
            <h4 class="h6">Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="menu_items.php">Menu Items</a></li>
                <li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="reservations.php">Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link active" href="settings.php">Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/logout.php">Logout</a></li>
            </ul>
        </aside>
        <section class="content-card">
            <h1 class="h3 mb-4">Cafe Settings</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" class="table-form-wrap" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cafe Name</label>
                        <input type="text" name="cafe_name" class="form-control" value="<?= htmlspecialchars($settingsData['cafe_name'] ?? '') ?>" data-validation="required min max" data-min="2" data-max="100">
                        <span id="cafe_name_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="text" name="contact_email" class="form-control" value="<?= htmlspecialchars($settingsData['contact_email'] ?? '') ?>" data-validation="required email">
                        <span id="contact_email_error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settingsData['phone'] ?? '') ?>" data-validation="required number min max" data-min="10" data-max="10">
                        <span id="phone_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control" data-validation="fileSize fileType" data-filesize="2" data-filetype="jpg,jpeg,png">
                        <span id="logo_error"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="4" data-validation="required min max" data-min="10" data-max="300"><?= htmlspecialchars($settingsData['address'] ?? '') ?></textarea>
                    <span id="address_error"></span>
                </div>
                <button type="submit" name="settings_btn" class="btn btn-cafe">Save Settings</button>
            </form>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>