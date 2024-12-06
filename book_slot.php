<?php
require 'connection.php';

$timeSlotId = $_POST['timeSlotId'] ?? null;
$classId = $_POST['classId'] ?? null;
$date = $_POST['date'] ?? null;

if (!$timeSlotId || !$classId || !$date) {
    echo "Invalid parameters.";
    exit;
}

$dayOfWeek = date('w', strtotime($date));
if ($dayOfWeek == 5 || $dayOfWeek == 6 || $date < date('Y-m-d')) {
    echo "Invalid date selection. Booking not allowed.";
    exit;
}


// Get POST data
$classId = $_POST['classId'];
$timeSlotId = $_POST['timeSlotId'];
$date = $_POST['date'];

// Check if the required data is available
if (empty($classId) || empty($timeSlotId) || empty($date)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
    exit;
}

// Prepare the SQL query to insert the booking
$query = "INSERT INTO bookings (class_id, time_slot_id, booking_date) VALUES (?, ?, ?)";
$data = $pdo->prepare($query);

// Check if the statement was prepared successfully
if ($data === false) {
    echo 'error: Failed to prepare statement';
    exit;
}

// Bind parameters
$data->bind_param("iis", $classId, $timeSlotId, $date);

// Execute the query
if ($data->execute()) {
    echo 'success';
} else {
    // Capture and log the error from MySQL
    echo 'error: ' . $data->error;  // Show error message from MySQL
}
?>