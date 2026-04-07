<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/db.php';
require_once '../config/Env.php';
require_once '../includes/mailer.php';
$pageTitle = 'Manage Feedback - Cloud 9 Cafe';
$activePage = 'feedback';

$message = '';
$messageType = '';

if (isset($_POST['feedback_reply_btn'])) {
    $feedbackId = (int) $_POST['feedback_id'];
    $replyMessage = cleanInput($_POST['reply_message']);

    // Fetch user info for this feedback (needed for email reply).
    $fbRes = mysqli_query($con, "
        SELECT f.subject, f.message, u.email, u.full_name 
        FROM feedback f 
        JOIN users u ON f.user_id = u.id 
        WHERE f.id = $feedbackId
        LIMIT 1");

    if ($fbRes && mysqli_num_rows($fbRes) === 1) {
        $feedbackRow = mysqli_fetch_assoc($fbRes);

        $updateQuery = "UPDATE feedback SET reply_message='$replyMessage' WHERE id=$feedbackId";
        if (mysqli_query($con, $updateQuery)) {
            $message = 'Reply saved and emailed to the user.';
            $messageType = 'success';

            // Send reply email to the user (best effort).
            $toEmail  = $feedbackRow['email'];
            $userName = $feedbackRow['full_name'] ?: 'there';
            $appName  = Env::get('APP_NAME', 'Cloud 9 Cafe');

            $body = "
                <p>Hi " . htmlspecialchars($userName) . ",</p>
                <p>Thanks for your feedback on {$appName}. Here's our reply:</p>
                <p><strong>Your original subject:</strong> " . htmlspecialchars($feedbackRow['subject']) . "</p>
                <p><strong>Your message:</strong><br>" . nl2br(htmlspecialchars($feedbackRow['message'])) . "</p>
                <hr>
                <p><strong>Our reply:</strong><br>" . nl2br(htmlspecialchars($replyMessage)) . "</p>
                <p>— {$appName} Team</p>
            ";
            sendAppMail($toEmail, "Reply to your feedback at {$appName}", $body);
        } else {
            $message = 'Failed to save reply.';
            $messageType = 'danger';
        }
    } else {
        $message = 'Feedback not found or user missing.';
        $messageType = 'danger';
    }
}

$feedbackList = mysqli_query($con, "SELECT feedback.*, users.full_name FROM feedback LEFT JOIN users ON feedback.user_id = users.id ORDER BY feedback.id DESC");
include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/admin_sidebar.php'; ?>
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

