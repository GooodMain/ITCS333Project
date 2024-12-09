<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch user information from session
$userEmail = $_SESSION['user'];
$userFullName = $_SESSION['fullName'] ?? 'Unknown User';
$user_id = $_SESSION['user_id'] ?? 1; // Default for testing; replace with actual logic

// Include database connection
require_once 'connection.php';

// Functions to fetch data
function getRoomUsageStats($pdo) {
    $query = "SELECT c.class_num, 
                     COUNT(CASE WHEN b.booking_status = 'confirmed' THEN 1 END) AS confirmed_bookings,
                     COUNT(CASE WHEN b.booking_status = 'cancelled' THEN 1 END) AS cancelled_bookings
              FROM classes c
              LEFT JOIN bookings b ON c.class_id = b.class_id
              GROUP BY c.class_id
              ORDER BY confirmed_bookings DESC";
    return $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

function getAllBookingsByStatus($pdo, $user_id, $status) {
    $query = "SELECT c.class_num, t.start_time, t.end_time, b.booking_date
              FROM bookings b
              JOIN classes c ON b.class_id = c.class_id
              JOIN time_slots t ON b.time_slot_id = t.time_slot_id
              WHERE b.user_id = :user_id AND b.booking_status = :status
              ORDER BY b.booking_date DESC, t.start_time DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'status' => $status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch data
$roomUsageStats = getRoomUsageStats($pdo);
$confirmedBookings = getAllBookingsByStatus($pdo, $user_id, 'confirmed');
$cancelledBookings = getAllBookingsByStatus($pdo, $user_id, 'cancelled');
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

        <!-- Room Usage Statistics -->
        <section>
            <h2>Room Usage Statistics</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Confirmed Bookings</th>
                        <th>Cancelled Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roomUsageStats as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room['class_num']) ?></td>
                            <td><?= htmlspecialchars($room['confirmed_bookings']) ?></td>
                            <td><?= htmlspecialchars($room['cancelled_bookings']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <canvas id="roomUsageChart"></canvas>
        </section>

        <!-- Confirmed Bookings -->
        <section class="mt-5">
            <h2>Your Confirmed Bookings</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($confirmedBookings)): ?>
                        <tr><td colspan="3">No confirmed bookings.</td></tr>
                    <?php else: ?>
                        <?php foreach ($confirmedBookings as $booking): ?>
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

        <!-- Cancelled Bookings -->
        <section class="mt-5">
            <h2>Your Cancelled Bookings</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cancelledBookings)): ?>
                        <tr><td colspan="3">No cancelled bookings.</td></tr>
                    <?php else: ?>
                        <?php foreach ($cancelledBookings as $booking): ?>
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
                datasets: [
                    {
                        label: 'Confirmed Bookings',
                        data: roomUsageData.map(item => item.confirmed_bookings),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Cancelled Bookings',
                        data: roomUsageData.map(item => item.cancelled_bookings),
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
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
