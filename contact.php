<?php
include 'config/db.php';
$pageTitle = 'Contact - Cloud 9 Cafe';

$message = '';
$messageType = '';

if (isset($_POST['contact_btn'])) {
    $fullName = cleanInput($_POST['full_name']);
    $email = cleanInput($_POST['email']);
    $subject = cleanInput($_POST['subject']);
    $messageText = cleanInput($_POST['message']);

    $insertQuery = "INSERT INTO contact_messages (full_name, email, subject, message)
                    VALUES ('$fullName', '$email', '$subject', '$messageText')";

    if (mysqli_query($con, $insertQuery)) {
        $message = 'Your message has been sent successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to send message. Please try again.';
        $messageType = 'danger';
    }
}

include 'includes/header.php';
?>
<div class="container">
    <div class="content-card form-shell">
        <h1 class="h2 mb-4">Contact Us</h1>
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" data-validation="required min alphabetic max" data-min="2" data-max="50">
                <span id="full_name_error"></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" name="email" class="form-control" data-validation="required email">
                <span id="email_error"></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" data-validation="required min max" data-min="3" data-max="100">
                <span id="subject_error"></span>
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="5" data-validation="required min max" data-min="10" data-max="500"></textarea>
                <span id="message_error"></span>
            </div>
            <button type="submit" name="contact_btn" class="btn btn-cafe">Send Message</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>