<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch user information from session
$userEmail = $_SESSION['user'];
$userFullName = isset($_SESSION['fullName']) ? $_SESSION['fullName'] : 'Unknown User';

// Include database connection
require_once 'connection.php';

// Function to get room usage statistics
function getRoomUsageStats($pdo) {
    $query = "SELECT c.class_num, COUNT(b.booking_id) as booking_count
              FROM classes c
              LEFT JOIN bookings b ON c.class_id = b.class_id
              GROUP BY c.class_id
              ORDER BY booking_count DESC";
    
    $data1 = $pdo->query($query);
    return $data1->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get user's upcoming bookings
function getUpcomingBookings($pdo, $user_id) {
    $query = "SELECT b.booking_id, c.class_num, t.start_time, t.end_time, b.booking_date
              FROM bookings b
              JOIN classes c ON b.class_id = c.class_id
              JOIN time_slots t ON b.time_slot_id = t.time_slot_id
              WHERE b.user_id = :user_id AND b.booking_date >= CURDATE()
              ORDER BY b.booking_date, t.start_time";
    
    $data1 = $pdo->prepare($query);
    $data1->execute(['user_id' => $user_id]);
    return $data1->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get user's past bookings
function getPastBookings($pdo, $user_id) {
    $query = "SELECT b.booking_id, c.class_num, t.start_time, t.end_time, b.booking_date
              FROM bookings b
              JOIN classes c ON b.class_id = c.class_id
              JOIN time_slots t ON b.time_slot_id = t.time_slot_id
              WHERE b.user_id = :user_id AND b.booking_date < CURDATE()
              ORDER BY b.booking_date DESC, t.start_time DESC";
    
    $data1 = $pdo->prepare($query);
    $data1->execute(['user_id' => $user_id]);
    return $data1->fetchAll(PDO::FETCH_ASSOC);
}

// Use the actual user ID from the session instead of a hardcoded value.
$user_id = $_SESSION['user_id'] ?? 1;

// Get room usage statistics and user's bookings
$roomStats = getRoomUsageStats($pdo);
$upcomingBookings = getUpcomingBookings($pdo, $user_id);
$pastBookings = getPastBookings($pdo, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking System Analytics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include("header.php"); ?>

<div class="container mt-4">
    <h1 class="mb-4">Booking System Analytics</h1>
    <p>Hello, <strong><?= htmlspecialchars($userFullName) ?></strong><br>This is your report</p>

    <section class="mb-4">
        <h2 class="mb-3">Room Usage Statistics</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Booking Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roomStats as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['class_num']) ?></td>
                    <td><?= htmlspecialchars($room['booking_count']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="mb-4">
        <h2 class="mb-3">Your Upcoming Bookings</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($upcomingBookings)): ?>
                    <tr><td colspan="3">You have no upcoming bookings.</td></tr>
                <?php else: ?>
                    <?php foreach ($upcomingBookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['class_num']) ?></td>
                        <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                        <td><?= htmlspecialchars($booking['start_time'] . ' - ' . $booking['end_time']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="mb-4">
        <h2 class="mb-3">Your Past Bookings</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pastBookings)): ?>
                    <tr><td colspan="3">You have no past bookings.</td></tr>
                <?php else: ?>
                    <?php foreach ($pastBookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['class_num']) ?></td>
                        <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                        <td><?= htmlspecialchars($booking['start_time'] . ' - ' . $booking['end_time']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</div>

<?php include("footer.php"); ?>
</body>
</html>
