<?php
// User reservation page
require_once '../includes/auth.php';
requireUser();
require_once '../config/db.php';
$pageTitle = 'My Reservations - Cloud 9 Cafe';
$activePage = 'reservations';

$message = '';
$messageType = '';

if (isset($_POST['reservation_btn'])) {
    $userId = currentUserId();
    $reservationDate = cleanInput($_POST['reservation_date']);
    $reservationTime = cleanInput($_POST['reservation_time']);
    $guestCount = (int) $_POST['guest_count'];
    $occasion = cleanInput($_POST['occasion']);
    $specialRequest = cleanInput($_POST['special_request']);

    $insertQuery = "INSERT INTO reservations (user_id, reservation_date, reservation_time, guest_count, occasion, special_request)
                    VALUES ($userId, '$reservationDate', '$reservationTime', $guestCount, '$occasion', '$specialRequest')";

    if (mysqli_query($con, $insertQuery)) {
        $message = 'Reservation submitted successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to submit reservation.';
        $messageType = 'danger';
    }
}

$reservationList = [];
$userId = currentUserId();
$reservationQuery = mysqli_query($con, "SELECT * FROM reservations WHERE user_id = $userId ORDER BY id DESC");
if ($reservationQuery) {
    while ($row = mysqli_fetch_assoc($reservationQuery)) {
        $reservationList[] = $row;
    }
}

include '../includes/header.php';
?>
<div class="container">
    <div class="dashboard-shell">
        <?php include '../includes/user_sidebar.php'; ?>
        <section class="content-card">
            <h1 class="h3 mb-4">Book a Table</h1>
            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reservation Date</label>
                        <input type="date" name="reservation_date" class="form-control" data-validation="required">
                        <span id="reservation_date_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reservation Time</label>
                        <input type="time" name="reservation_time" class="form-control" data-validation="required">
                        <span id="reservation_time_error"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Number of Guests</label>
                        <input type="number" name="guest_count" class="form-control" min="1" max="20" data-validation="required number">
                        <span id="guest_count_error"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Occasion</label>
                        <select name="occasion" class="form-select" data-validation="required select">
                            <option value="">Select Occasion</option>
                            <option value="casual">Casual Visit</option>
                            <option value="birthday">Birthday</option>
                            <option value="meeting">Meeting</option>
                            <option value="other">Other</option>
                        </select>
                        <span id="occasion_error"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Special Request</label>
                    <textarea name="special_request" class="form-control" rows="4" data-validation="max" data-max="300"></textarea>
                    <span id="special_request_error"></span>
                </div>
                <button type="submit" name="reservation_btn" class="btn btn-cafe">Submit Reservation</button>
            </form>

            <hr class="my-4">
            <h2 class="h5 mb-3">My Reservation History</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reservationList)): ?>
                            <?php foreach ($reservationList as $reservation): ?>
                                <tr>
                                    <td>#<?= $reservation['id'] ?></td>
                                    <td><?= htmlspecialchars($reservation['reservation_date']) ?></td>
                                    <td><?= htmlspecialchars($reservation['reservation_time']) ?></td>
                                    <td><?= htmlspecialchars($reservation['guest_count']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($reservation['status'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No reservations found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
