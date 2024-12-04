<?php
require 'connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "<div class='alert alert-danger'>You must be logged in to book a room.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDate = $_POST['date'];
    $selectedTimeSlot = $_POST['time_slot'];
    $userEmail = $_SESSION['user'];
    $classType = $_POST['cl_type'];

    // Define available time slots from 10 AM to 6 PM, each slot is 1 hour
    $timeSlots = [
        '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00'
    ];

    // Calculate start and end time for the selected time slot
    $startTime = $timeSlots[$selectedTimeSlot];
    $endTime = date('H:i:s', strtotime($startTime) + 3600);
    $startDateTime = "$selectedDate $startTime";
    $endDateTime = "$selectedDate $endTime";

    // Check if the selected time slot is already booked (Conflict checking algorithm)
    $checkBookingQuery = "SELECT * FROM room_bookings WHERE (start_time < :end_time AND end_time > :start_time) AND cl_type = :cl_type";
    $db = $pdo->prepare($checkBookingQuery);
    $db->execute([
        ':start_time' => $startDateTime,
        ':end_time' => $endDateTime,
        ':cl_type' => $classType
    ]);

    if ($db->rowCount() > 0) {
        echo "<div class='alert alert-danger'>The selected room is already booked for this time slot.</div>";
    } else {
        // Insert new booking
        $insertBookingQuery = "INSERT INTO room_bookings (start_time, end_time, class_id, cl_type) VALUES (?, ?, ?, ?)";
        $insertStmt = $pdo->prepare($insertBookingQuery);
        $insertStmt->execute([$startDateTime, $endDateTime, $userEmail, $classType]);

        echo "<div class='alert alert-success'>Booking successful!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script>
        function disableUnavailableDays() {
            const dateInput = document.getElementById('date');
            const selectedDate = new Date(dateInput.value);
            const day = selectedDate.getDay();
            if (day === 5 || day === 6) {
                dateInput.setCustomValidity('Bookings are not allowed on Fridays and Saturdays.');
            } else {
                dateInput.setCustomValidity('');
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            const timeSlotSelect = document.getElementById('time_slot');
            const timeIntervals = [
                '10:00 - 11:00',
                '11:00 - 12:00',
                '12:00 - 13:00',
                '13:00 - 14:00',
                '14:00 - 15:00',
                '15:00 - 16:00',
                '16:00 - 17:00',
                '17:00 - 18:00'
            ];

            // Populate time slots in the dropdown
            timeIntervals.forEach((interval, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = interval;
                timeSlotSelect.appendChild(option);
            });
        });
    </script>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Book a Room</h2>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-container mt-4">
            <div class="mb-3">
                <label for="date" class="form-label">Select Date:</label>
                <input type="date" id="date" name="date" class="form-control" required onchange="disableUnavailableDays()">
            </div>
            <div class="mb-3">
                <label for="time_slot" class="form-label">Select Time Slot:</label>
                <select id="time_slot" name="time_slot" class="form-control" required></select>
            </div>
            <div class="mb-3">
                <label for="cl_type" class="form-label">Select Class Type:</label>
                <select id="cl_type" name="cl_type" class="form-control" required>
                    <?php
                    $classTypes = $pdo->query("SELECT DISTINCT type FROM class_type")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($classTypes as $type) {
                        echo "<option value='{$type['type']}'>{$type['type']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Book Now</button>
        </form>
    </div>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Cancel a Booking</h2>
        <form method="POST" action="cancel_booking.php" class="form-container mt-4">
            <div class="mb-3">
                <label for="booking_id" class="form-label">Booking ID:</label>
                <input type="text" id="booking_id" name="booking_id" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Cancel Booking</button>
        </form>
    </div>

    <?php include("footer.php"); ?>
</body>
</html>
