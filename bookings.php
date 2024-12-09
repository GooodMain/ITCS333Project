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

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $class_id = $_POST['class_id'];
    $time_slot_id = $_POST['time_slot_id'];
    $booking_date = $_POST['booking_date'];

    // Check if the time slot is already booked for the selected class and date
    $db = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE class_id = ? AND time_slot_id = ? AND booking_date = ?");
    $db->execute([$class_id, $time_slot_id, $booking_date]);
    $isBooked = $db->fetchColumn();

    if ($isBooked) {
        $error = "The selected time slot is already booked.";
    } else {
        // Insert the booking
        $db = $pdo->prepare("INSERT INTO bookings (user_id, class_id, time_slot_id, booking_date) VALUES (?, ?, ?, ?)");
        if ($db->execute([$user_id, $class_id, $time_slot_id, $booking_date])) {
            $success = "Booking confirmed successfully!";
        } else {
            $error = "Failed to book. Please try again.";
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
    </div>

    <script>
        document.getElementById('class_type').addEventListener('change', function () {
            const classTypeId = this.value;
            const bookingDate = document.getElementById('booking_date').value;

            if (classTypeId && bookingDate) {
                fetch(`book_slot.php?class_type_id=${classTypeId}&booking_date=${bookingDate}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('time-slots-container').innerHTML = data;
                    });
            }
        });

        document.getElementById('booking_date').addEventListener('change', function () {
            const classTypeId = document.getElementById('class_type').value;
            const bookingDate = this.value;

            if (classTypeId && bookingDate) {
                fetch(`book_slot.php?class_type_id=${classTypeId}&booking_date=${bookingDate}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('time-slots-container').innerHTML = data;
                    });
            }
        });
    </script>
    <?php include ("footer.php");?>
</body>
</html>