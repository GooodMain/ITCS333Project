<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) 
{
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch user information from session
$userEmail = $_SESSION['user'];

// Assuming the full name is stored in the session or you have it fetched from the database
// If it's not in the session, you might need to fetch it from the database based on the email
// For simplicity, let's assume the user's full name is stored in session as well
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

// Assume user is logged in and we have their user_id
$user_id = 1; // Replace with actual user authentication

// Get room usage statistics
$roomStats = getRoomUsageStats($pdo);

// Get user's upcoming and past bookings
$upcomingBookings = getUpcomingBookings($pdo, $user_id);
$pastBookings = getPastBookings($pdo, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking System Analytics</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
</head>
<body>

<?php include("header.php"); ?>

    <div>
        
        <p>Hello, <strong><?= htmlspecialchars($userFullName) ?></strong><br>  thiss you report </p>





    <main class="container">
        <h1>Booking System Analytics</h1>

        <section>
            <h2>Room Usage Statistics</h2>
            <table>
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
                        <td><?= $room['booking_count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Your Upcoming Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingBookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['class_num']) ?></td>
                        <td><?= $booking['booking_date'] ?></td>
                        <td><?= $booking['start_time'] . ' - ' . $booking['end_time'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Your Past Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pastBookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['class_num']) ?></td>
                        <td><?= $booking['booking_date'] ?></td>
                        <td><?= $booking['start_time'] . ' - ' . $booking['end_time'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
    <?php include("footer.php"); ?>
</body>
</html>






