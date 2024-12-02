<?php
// booking.php - Handles room booking functionality


// booking.php - Handles room booking functionality

require 'connection.php';
session_start();

// Function to get room availability
function getRoomAvailability($room_id, $date, $pdo) {
    $query = "SELECT start_time, end_time FROM room_bookings WHERE room_id = :room_id AND DATE(start_time) = :date";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':room_id' => $room_id,
        ':date' => $date
    ]);

    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $availability = [];

    // Determine room type
    $room_query = "SELECT type FROM class_type WHERE id = :room_id";
    $room_stmt = $pdo->prepare($room_query);
    $room_stmt->execute([':room_id' => $room_id]);
    $room = $room_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        return []; // No room found
    }

    // Generate slots based on room type
    $day_of_week = date('N', strtotime($date));
    if ($room['type'] === 'Lab' || $room['type'] === 'Benefit Lab') {
        $slots = [
            ['start' => '14:00', 'end' => '15:40'],
            ['start' => '16:00', 'end' => '17:40'],
            ['start' => '18:00', 'end' => '19:40']
        ];
    } elseif ($room['type'] === 'Open Lab') {
        $slots = [
            ['start' => '14:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '18:00'],
            ['start' => '18:00', 'end' => '20:00']
        ];
    } elseif ($room['type'] === 'Classroom') {
        if ($day_of_week == 1 || $day_of_week == 3) { // Monday, Wednesday
            $slots = [
                ['start' => '08:00', 'end' => '09:15'],
                ['start' => '09:30', 'end' => '10:45'],
                ['start' => '11:00', 'end' => '12:15'],
                ['start' => '12:30', 'end' => '13:45'],
                ['start' => '14:00', 'end' => '15:15'],
                ['start' => '15:30', 'end' => '16:45']
            ];
        } else { // Other weekdays
            $slots = [
                ['start' => '08:00', 'end' => '08:50'],
                ['start' => '09:00', 'end' => '09:50'],
                ['start' => '10:00', 'end' => '10:50'],
                ['start' => '11:00', 'end' => '11:50'],
                ['start' => '12:00', 'end' => '12:50'],
                ['start' => '13:00', 'end' => '13:50'],
                ['start' => '14:00', 'end' => '14:50'],
                ['start' => '15:00', 'end' => '15:50']
            ];
        }
    } else {
        return []; // Unknown room type
    }

    foreach ($slots as $slot) {
        $slot_start = "$date {$slot['start']}";
        $slot_end = "$date {$slot['end']}";

        // Check for conflicts
        $conflict_query = "SELECT * FROM room_bookings WHERE room_id = :room_id AND (start_time < :end_time AND end_time > :start_time)";
        $conflict_stmt = $pdo->prepare($conflict_query);
        $conflict_stmt->execute([
            ':room_id' => $room_id,
            ':start_time' => $slot_start,
            ':end_time' => $slot_end
        ]);

        if ($conflict_stmt->rowCount() == 0) {
            $availability[] = [
                'start_time' => $slot['start'],
                'end_time' => $slot['end']
            ];
        }
    }

    return $availability;
}

// Handling booking request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $user_id = $_SESSION['user_id'] ?? 1; // Example user ID for testing

    // Validate time inputs
    if (strtotime($start_time) >= strtotime($end_time)) {
        echo "<div class='alert alert-danger'>End time must be after start time.</div>";
    } else {
        // Check for conflicts
        $query = "SELECT * FROM room_bookings WHERE room_id = :room_id AND (start_time < :end_time AND end_time > :start_time)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':room_id' => $room_id,
            ':start_time' => "$date $start_time",
            ':end_time' => "$date $end_time"
        ]);

        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-danger'>Room is already booked for this timeslot.</div>";
        } else {
            // Insert new booking
            $insert = $pdo->prepare("INSERT INTO room_bookings (room_id, start_time, end_time, user_id) VALUES (?, ?, ?, ?)");
            $insert->execute([$room_id, "$date $start_time", "$date $end_time", $user_id]);
            echo "<div class='alert alert-success'>Booking successful!</div>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const roomSelect = document.getElementById('room_id');
    const dateInput = document.getElementById('date');
    const startTimeSelect = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    roomSelect.addEventListener('change', updateSlots);
    dateInput.addEventListener('input', updateSlots);

    function updateSlots() {
        startTimeSelect.innerHTML = '';
        endTimeInput.value = '';

        const roomId = roomSelect.value;
        const date = dateInput.value;

        if (!roomId || !date) return;

        // Fetch available slots for the selected room and date
        fetch('fetch_slots.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ room_id: roomId, date: date })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Populate start time dropdown
            data.slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.start;
                option.textContent = `${slot.start} - ${slot.end}`;
                startTimeSelect.appendChild(option);
            });

            // Update end time when a slot is selected
            startTimeSelect.addEventListener('change', function () {
                const selectedSlot = data.slots.find(slot => slot.start === this.value);
                endTimeInput.value = selectedSlot ? selectedSlot.end : '';
            });
        })
        .catch(error => {
            console.error('Error fetching slots:', error);
            alert('Failed to load slots.');
        });
    }
});
</script>

</head>
<body>
    <?php include("header.php"); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Book a Room</h2>
        <form method="POST" class="form-container mt-4">
    <div class="mb-3">
        <label for="room_id" class="form-label">Select Room:</label>
        <select id="room_id" name="room_id" class="form-control" required>
            <?php
            $rooms = $pdo->query("SELECT * FROM class_type")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rooms as $room) {
                echo "<option value='{$room['id']}'>{$room['type']} - Capacity: {$room['capacity']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="date" class="form-label">Select Date:</label>
        <input type="date" id="date" name="date" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="start_time" class="form-label">Start Time:</label>
        <select id="start_time" name="start_time" class="form-control" required></select>
    </div>
    <div class="mb-3">
        <label for="end_time" class="form-label">End Time:</label>
        <input type="text" id="end_time" name="end_time" class="form-control" readonly>
    </div>
    <button type="submit" class="btn btn-primary w-100">Book Now</button>
</form>

    </div>

    <?php include("footer.php"); ?>
</body>
</html>