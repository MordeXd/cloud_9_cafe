<?php
// User profile page
require_once '../includes/auth.php';
requireUser();
require_once '../config/db.php';
require_once '../includes/mailer.php';
require_once '../includes/upload_helpers.php';
$env = loadEnv(__DIR__ . '/../.env');
$pageTitle = 'My Profile - Cloud 9 Cafe';
$activePage = 'profile';

$message = '';
$messageType = '';
$userId = currentUserId();
$userQuery = mysqli_query($con, "SELECT * FROM users WHERE id = $userId LIMIT 1");
$userData = $userQuery ? mysqli_fetch_assoc($userQuery) : null;

if (isset($_POST['profile_btn'])) {
    $fullName = cleanInput($_POST['full_name']);
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);

    $profileImageSql = '';
    $uploadError = '';
    if (!empty($_FILES['profile_image']['name'])) {
        [$relativePath, $uploadError] = saveUploadedImage(
            $_FILES['profile_image'],
            'profile_photo',
            $userData['full_name'] ?? ($_SESSION['user_name'] ?? 'user'),
            'profile_'
        );

        if ($uploadError) {
            $message = $uploadError;
            $messageType = 'danger';
        } else {
            $safePath = mysqli_real_escape_string($con, $relativePath);
            $profileImageSql = ", profile_image='$safePath'";
        }
    }

    if ($messageType !== 'danger') {
        $updateQuery = "UPDATE users SET full_name='$fullName', email='$email', phone='$phone' $profileImageSql WHERE id=$userId";

        if (mysqli_query($con, $updateQuery)) {
            $_SESSION['user'] = $email;
            $_SESSION['user_name'] = $fullName;
            $message = 'Profile updated successfully.';
            $messageType = 'success';
            // Refresh data to reflect saved changes and new image path.
            $userQuery = mysqli_query($con, "SELECT * FROM users WHERE id = $userId LIMIT 1");
            $userData = $userQuery ? mysqli_fetch_assoc($userQuery) : $userData;
        } else {
            $message = 'Profile update failed.';
            $messageType = 'danger';
        }
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/user_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-4">Update Profile</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($userData['full_name'] ?? '') ?>" data-validation="required min alphabetic max" data-min="2" data-max="50">
                        <span id="full_name_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" data-validation="required email">
                        <span id="email_error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($userData['phone'] ?? '') ?>" data-validation="required number min max" data-min="10" data-max="10">
                        <span id="phone_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" class="form-control" data-validation="fileSize fileType" data-filesize="5" data-filetype="jpg,jpeg,png,webp">
                        <span id="profile_image_error"></span>
                    </div>
                </div>
                <button type="submit" name="profile_btn" class="btn btn-cafe">Save Profile</button>
            </form>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

