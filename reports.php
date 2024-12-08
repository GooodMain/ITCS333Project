<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}
require 'connection.php';

// Set user ID manually for testing purposes
$user_id = 1; // Replace with the desired user ID
$userFullName = 'Test User'; // Replace with the desired user full name

// Functions to fetch data
function getRoomUsageStats($pdo) {
    $query = "SELECT c.class_num, COUNT(b.booking_id) AS booking_count
              FROM classes c
              LEFT JOIN bookings b ON c.class_id = b.class_id
              GROUP BY c.class_id
              ORDER BY booking_count DESC";
    return $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

function getUpcomingBookings($pdo, $user_id) {
    $query = "SELECT c.class_num, t.start_time, t.end_time, b.booking_date
              FROM bookings b
              JOIN classes c ON b.class_id = c.class_id
              JOIN time_slots t ON b.time_slot_id = t.time_slot_id
              WHERE b.user_id = :user_id AND b.booking_date >= CURDATE()
              ORDER BY b.booking_date, t.start_time";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPastBookings($pdo, $user_id) {
    $query = "SELECT c.class_num, t.start_time, t.end_time, b.booking_date
              FROM bookings b
              JOIN classes c ON b.class_id = c.class_id
              JOIN time_slots t ON b.time_slot_id = t.time_slot_id
              WHERE b.user_id = :user_id AND b.booking_date < CURDATE()
              ORDER BY b.booking_date DESC, t.start_time DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch data
$roomUsageStats = getRoomUsageStats($pdo);
$upcomingBookings = getUpcomingBookings($pdo, $user_id);
$pastBookings = getPastBookings($pdo, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporting & Analytics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include("header.php"); ?>
    <div class="container mt-4">
        <h1>Reporting & Analytics</h1>
        <p>Welcome, <strong><?= htmlspecialchars($userFullName) ?></strong></p>

        <!-- Room Usage Statistics -->
        <section>
            <h2>Room Usage Statistics</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roomUsageStats as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room['class_num']) ?></td>
                            <td><?= htmlspecialchars($room['booking_count']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <canvas id="roomUsageChart"></canvas>
        </section>

        <!-- Upcoming Bookings -->
        <section class="mt-5">
            <h2>Your Upcoming Bookings</h2>
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
                        <tr><td colspan="3">No upcoming bookings.</td></tr>
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

        <!-- Past Bookings -->
        <section class="mt-5">
            <h2>Your Past Bookings</h2>
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
                        <tr><td colspan="3">No past bookings.</td></tr>
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

    <script>
        const ctx = document.getElementById('roomUsageChart').getContext('2d');
        const roomUsageData = <?= json_encode($roomUsageStats) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: roomUsageData.map(item => item.class_num),
                datasets: [{
                    label: 'Bookings',
                    data: roomUsageData.map(item => item.booking_count),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <?php include("footer.php"); ?>
</body>
</html>
