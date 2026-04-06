<?php
// User feedback page
require_once '../includes/auth.php';
requireUser();
require_once '../config/db.php';
$pageTitle = 'Feedback - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['feedback_btn'])) {
    $userId = currentUserId();
    $subject = cleanInput($_POST['subject']);
    $rating = (int) $_POST['rating'];
    $messageText = cleanInput($_POST['message']);

    $insertQuery = "INSERT INTO feedback (user_id, subject, rating, message)
                    VALUES ($userId, '$subject', $rating, '$messageText')";

    if (mysqli_query($con, $insertQuery)) {
        $message = 'Feedback submitted successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to submit feedback.';
        $messageType = 'danger';
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <aside class="sidebar-card">
            <h4 class="h6">User Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="reservations.php">Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link active" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="change_password.php">Change Password</a></li>
            </ul>
        </aside>
        <section class="content-card">
            <h1 class="h3 mb-4">Share Your Feedback</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" data-validation="required min max" data-min="3" data-max="100">
                        <span id="subject_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select" data-validation="required select">
                            <option value="">Select Rating</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Good</option>
                            <option value="2">2 - Fair</option>
                            <option value="1">1 - Poor</option>
                        </select>
                        <span id="rating_error"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="5" data-validation="required min max" data-min="10" data-max="500"></textarea>
                    <span id="message_error"></span>
                </div>
                <button type="submit" name="feedback_btn" class="btn btn-cafe">Submit Feedback</button>
            </form>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>