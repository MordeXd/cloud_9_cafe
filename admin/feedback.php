<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
$pageTitle = 'Manage Feedback - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['feedback_reply_btn'])) {
    $feedbackId = (int) $_POST['feedback_id'];
    $replyMessage = cleanInput($_POST['reply_message']);

    $updateQuery = "UPDATE feedback SET reply_message='$replyMessage' WHERE id=$feedbackId";
    if (mysqli_query($con, $updateQuery)) {
        $message = 'Reply saved successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to save reply.';
        $messageType = 'danger';
    }
}

$feedbackList = mysqli_query($con, "SELECT feedback.*, users.full_name FROM feedback LEFT JOIN users ON feedback.user_id = users.id ORDER BY feedback.id DESC");
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
                <li class="nav-item"><a class="nav-link active" href="feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="/cloud_9_cafe/logout.php">Logout</a></li>
            </ul>
        </aside>
        <section class="content-card">
            <h1 class="h3 mb-4">Feedback Reply</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="" class="table-form-wrap mb-4">
                <div class="mb-3">
                    <label class="form-label">Feedback ID</label>
                    <input type="text" name="feedback_id" class="form-control" data-validation="required min max" data-min="1" data-max="20">
                    <span id="feedback_id_error"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reply Message</label>
                    <textarea name="reply_message" class="form-control" rows="5" data-validation="required min max" data-min="10" data-max="500"></textarea>
                    <span id="reply_message_error"></span>
                </div>
                <button type="submit" name="feedback_reply_btn" class="btn btn-cafe">Send Reply</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>User</th><th>Subject</th><th>Rating</th><th>Reply</th></tr></thead>
                    <tbody>
                        <?php if ($feedbackList && mysqli_num_rows($feedbackList) > 0): ?>
                            <?php while ($feedback = mysqli_fetch_assoc($feedbackList)): ?>
                                <tr>
                                    <td><?= $feedback['id'] ?></td>
                                    <td><?= htmlspecialchars($feedback['full_name'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($feedback['subject']) ?></td>
                                    <td><?= htmlspecialchars($feedback['rating']) ?></td>
                                    <td><?= htmlspecialchars($feedback['reply_message'] ?? 'No reply yet') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No feedback found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>