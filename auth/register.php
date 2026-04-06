<?php
include '../config/db.php';
include '../includes/mailer.php';
$env = loadEnv(__DIR__ . '/../.env');
$pageTitle = 'Register - Cloud 9 Cafe';

$message = '';
$messageType = '';
$uploadDir = __DIR__ . '/../' . rtrim($env['UPLOAD_PATH'] ?? 'uploads/', '/') . '/';

if (isset($_POST['register_btn'])) {
    $firstName = cleanInput($_POST['firstName']);
    $lastName = cleanInput($_POST['lastName']);
    $fullName = $firstName . ' ' . $lastName;
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);
    $password = $_POST['password'];
    $gender = cleanInput($_POST['gender']);

    $profileImageName = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $profileImageName = time() . '_' . basename($_FILES['profile_picture']['name']);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadDir . $profileImageName);
    }

    $checkUser = mysqli_query($con, "SELECT id FROM users WHERE email='$email'");

    if (mysqli_num_rows($checkUser) > 0) {
        $message = 'Email already exists. Please use another email.';
        $messageType = 'danger';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $activationToken = bin2hex(random_bytes(32));
        $insertQuery = "INSERT INTO users (full_name, email, phone, password, gender, profile_image, role, status, activation_token, is_verified) 
                        VALUES ('$fullName', '$email', '$phone', '$hashedPassword', '$gender', '$profileImageName', 'user', 'inactive', '$activationToken', 0)";

        if (mysqli_query($con, $insertQuery)) {
            $activationLink = ($env['APP_URL'] ?? 'http://localhost/cloud_9_cafe') . '/auth/activate.php?token=' . $activationToken;
            $body = '<p>Hi ' . htmlspecialchars($fullName) . ',</p><p>Thanks for registering at Cloud 9 Cafe. Please click the link below to activate your account:</p><p><a href="' . $activationLink . '">' . $activationLink . '</a></p>';
            sendAppMail($email, 'Activate your Cloud 9 Cafe account', $body);
            $message = 'Registration successful. Check your email for the activation link.';
            $messageType = 'success';
        } else {
            $message = 'Registration failed. Please try again.';
            $messageType = 'danger';
        }
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="content-card form-shell">
        <h1 class="h3 mb-3">Create Account</h1>
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
        <?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstName" class="form-control" data-validation="required min alphabetic max" data-min="2" data-max="20">
                    <span id="firstName_error"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastName" class="form-control" data-validation="required min alphabetic max" data-min="2" data-max="20">
                    <span id="lastName_error"></span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" name="email" class="form-control" data-validation="required email">
                <span id="email_error"></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" data-validation="required number min max" data-min="10" data-max="10">
                <span id="phone_error"></span>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" id="confirmPassword_confirm" name="password" class="form-control" data-validation="required strongPassword">
                    <span id="password_error"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" id="password" name="confirmPassword" class="form-control" data-validation="required confirmPassword">
                    <span id="confirmPassword_error"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select" data-validation="required select">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <span id="gender_error"></span>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" name="profile_picture" class="form-control" data-validation="required fileSize fileType" data-filesize="2" data-filetype="jpg,jpeg,png">
                    <span id="profile_picture_error"></span>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="terms" name="terms" data-validation="required">
                <label class="form-check-label" for="terms">I agree to the terms and conditions</label>
                <span id="terms_error"></span>
            </div>
            <button type="submit" name="register_btn" class="btn btn-cafe w-100">Register</button>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
