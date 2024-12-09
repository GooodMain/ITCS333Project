<?php
require 'connection.php';

$class_type_id = $_GET['class_type_id'] ?? null;
$booking_date = $_GET['booking_date'] ?? null;

// Validate input
if (!is_numeric($class_type_id) || !strtotime($booking_date)) {
    echo "<div class='alert alert-danger'>Invalid parameters provided.</div>";
    exit();
}

// Get classes of the selected type
$db = $pdo->prepare("
    SELECT c.class_id, c.class_num, ts.time_slot_id, ts.start_time, ts.end_time,
           EXISTS(
               SELECT 1 
               FROM bookings b 
               WHERE b.class_id = c.class_id 
                 AND b.time_slot_id = ts.time_slot_id 
                 AND b.booking_date = ? 
                 AND b.booking_status = 'confirmed'
           ) AS is_booked
    FROM classes c
    CROSS JOIN time_slots ts
    WHERE c.class_type_id = ?
    ORDER BY c.class_id, ts.time_slot_id
");
$db->execute([$booking_date, $class_type_id]);
$results = $db->fetchAll(PDO::FETCH_ASSOC);

if (empty($results)) {
    echo "<div class='alert alert-warning'>No classes or time slots available for the selected type and date.</div>";
    exit();
}

// Display time slots grouped by class
$currentClassId = null;
foreach ($results as $row) {
    if ($currentClassId !== $row['class_id']) {
        if ($currentClassId !== null) echo "</div>"; // Close previous group's div
        echo "<h5>Class: " . htmlspecialchars($row['class_num']) . "</h5><div class='d-flex flex-wrap mb-3'>";
        $currentClassId = $row['class_id'];
    }

    $buttonClass = $row['is_booked'] ? 'btn-secondary' : 'btn-primary';
    $disabled = $row['is_booked'] ? 'disabled' : '';
    echo "
        <form method='POST' class='me-2'>
            <input type='hidden' name='class_id' value='" . htmlspecialchars($row['class_id']) . "'>
            <input type='hidden' name='time_slot_id' value='" . htmlspecialchars($row['time_slot_id']) . "'>
            <input type='hidden' name='booking_date' value='" . htmlspecialchars($booking_date) . "'>
            <button type='submit' class='btn $buttonClass' $disabled>
                " . htmlspecialchars($row['start_time'] . ' - ' . $row['end_time']) . "
            </button>
        </form>
    ";
}
if ($currentClassId !== null) echo "</div>"; // Close last group's div
?>

