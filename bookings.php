<?php
session_start();
require 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch class types from the database
$db = $pdo->prepare("SELECT * FROM class_type");
$db->execute();
$classTypes = $db->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's confirmed bookings
$user_id = $_SESSION['user_id'];
$userBookings = $pdo->prepare("SELECT b.booking_id, b.booking_date, c.class_num, ts.start_time, ts.end_time 
    FROM bookings b
    JOIN classes c ON b.class_id = c.class_id
    JOIN time_slots ts ON b.time_slot_id = ts.time_slot_id
    WHERE b.user_id = :user_id AND b.booking_status = 'confirmed'
    ORDER BY b.booking_date, ts.start_time");
$userBookings->execute(['user_id' => $user_id]);
$bookings = $userBookings->fetchAll(PDO::FETCH_ASSOC);

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_date'])) {
    $class_id = $_POST['class_id'];
    $time_slot_id = $_POST['time_slot_id'];
    $booking_date = $_POST['booking_date'];

    // Validate input
    if (!is_numeric($class_id) || !is_numeric($time_slot_id) || !strtotime($booking_date)) {
        $error = "Invalid input data.";
    } else {
        $selectedDate = new DateTime($booking_date);
        $today = new DateTime('today');
        $dayOfWeek = $selectedDate->format('N'); // 1 = Monday, ..., 7 = Sunday

        if ($selectedDate < $today) {
            $error = "You cannot book on a past date.";
        } elseif ($dayOfWeek == 5 || $dayOfWeek == 6) { // 5 = Friday, 6 = Saturday
            $error = "Bookings are not allowed on Fridays or Saturdays.";
        } else {
            // Check if the time slot is already booked for the selected class and date
            $db = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE class_id = ? AND time_slot_id = ? AND booking_date = ? AND booking_status = 'confirmed'");
            $db->execute([$class_id, $time_slot_id, $booking_date]);
            $isBooked = $db->fetchColumn();

            if ($isBooked) {
                $error = "The selected time slot is already booked.";
            } else {
                // Insert the booking
                if ($pdo->prepare("INSERT INTO bookings (user_id, class_id, time_slot_id, booking_date, booking_status) VALUES (?, ?, ?, ?, 'confirmed')")
                    ->execute([$user_id, $class_id, $time_slot_id, $booking_date])
                ) {
                    $success = "Booking confirmed successfully!";
                } else {
                    $error = "Failed to book. Please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("header.php"); ?>
    <div class="container mt-5">
        <h2 class="text-center">Book a Class</h2>

        <!-- Display success or error messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Booking Form -->
        <form method="POST" id="booking-form" class="mt-4">
            <div class="mb-3">
                <label for="booking_date" class="form-label">Select Date:</label>
                <input type="date" id="booking_date" name="booking_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="class_type" class="form-label">Class Type:</label>
                <select id="class_type" name="class_type" class="form-control" required>
                    <option value="" disabled selected>Select a class type</option>
                    <?php foreach ($classTypes as $classType): ?>
                        <option value="<?= $classType['class_type_id'] ?>"><?= htmlspecialchars($classType['type_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <div id="time-slots-container" class="mt-4"></div>

        <!-- User's Confirmed Bookings -->
        <div class="mt-5">
            <h3>Your Confirmed Bookings</h3>
            <?php if (!empty($bookings)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Time Slot</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['class_num']) ?></td>
                                <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                                <td><?= htmlspecialchars($booking['start_time'] . ' - ' . $booking['end_time']) ?></td>
                                <td>
                                    <button
                                        class="btn btn-danger btn-sm"
                                        onclick="cancelBooking(<?= htmlspecialchars($booking['booking_id']) ?>)">
                                        Cancel
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No confirmed bookings found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                fetch('cancel_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            booking_id: bookingId
                        }), // Send the booking ID as JSON
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Refresh the page to reflect changes
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            }
        }

        const bookingDateInput = document.getElementById('booking_date');
        const classTypeDropdown = document.getElementById('class_type');

        // Set minimum date to today's date
        const today = new Date().toISOString().split('T')[0];
        bookingDateInput.setAttribute('min', today);

        // Disable Fridays and Saturdays
        bookingDateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const day = selectedDate.getUTCDay(); // Sunday = 0, Monday = 1, ..., Saturday = 6
            if (day === 5 || day === 6) { // 5 = Friday, 6 = Saturday
                alert("Bookings are not allowed on Fridays or Saturdays. Please select another date.");
                this.value = ""; // Clear the invalid date
            }
        });

        // Fetch time slots dynamically when date or class type changes
        document.querySelectorAll('#class_type, #booking_date').forEach(input => {
            input.addEventListener('change', function() {
                const classTypeId = classTypeDropdown.value;
                const bookingDate = bookingDateInput.value;

                if (classTypeId && bookingDate) {
                    fetch(`book_slot.php?class_type_id=${classTypeId}&booking_date=${bookingDate}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Failed to fetch time slots.');
                            return response.text();
                        })
                        .then(data => {
                            document.getElementById('time-slots-container').innerHTML = data;
                        })
                        .catch(error => {
                            document.getElementById('time-slots-container').innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                        });
                }
            });
        });
    </script>
    <?php include("footer.php"); ?>
</body>

</html>