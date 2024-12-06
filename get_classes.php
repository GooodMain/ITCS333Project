<?php
require 'connection.php';

$classType = $_GET['classType'] ?? null;
$date = $_GET['date'] ?? null;

if (!$classType || !$date) {
    echo "<p class='text-danger'>Invalid parameters.</p>";
    exit;
}

// Validate the selected date
$dayOfWeek = date('w', strtotime($date));
if ($dayOfWeek == 5 || $dayOfWeek == 6 || $date < date('Y-m-d')) {
    echo "<p class='text-danger'>Invalid date selection. Please choose a valid weekday.</p>";
    exit;
}

$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

// Fetch all classes of the selected type
$query = "
    SELECT c.class_id, c.class_num 
    FROM classes c
    WHERE c.class_type_id = :classType
";
$data = $pdo->prepare($query);
$data->bindValue(':classType', $classType, PDO::PARAM_INT);
$data->execute();
$result = $data->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) {
    foreach ($result as $row) {
        echo "<div class='mt-4'>";
        echo "<h3>Class ID: {$row['class_id']} (Class Number: {$row['class_num']})</h3>";

        $classId = $row['class_id'];

        // Fetch available time slots
        $timeSlotQuery = "
            SELECT ts.time_slot_id, ts.start_time, ts.end_time 
            FROM time_slots ts 
            WHERE ts.start_time > :timeCheck 
            AND NOT EXISTS (
                SELECT 1 
                FROM bookings b 
                WHERE b.class_id = :classId 
                AND b.booking_date = :date 
                AND b.time_slot_id = ts.time_slot_id
            )
        ";
        
        // Use current time for today; otherwise, allow all slots
        $timeCheck = $date === $currentDate ? $currentTime : '00:00:00';

        $timeSlotdata = $pdo->prepare($timeSlotQuery);
        $timeSlotdata->bindValue(':timeCheck', $timeCheck);
        $timeSlotdata->bindValue(':classId', $classId, PDO::PARAM_INT);
        $timeSlotdata->bindValue(':date', $date);
        $timeSlotdata->execute();
        $timeSlotResult = $timeSlotdata->fetchAll(PDO::FETCH_ASSOC);

        if (count($timeSlotResult) > 0) {
            echo "<div class='mt-2'>";
            foreach ($timeSlotResult as $slot) {
                echo "<button 
                        class='btn btn-outline-primary time-slot' 
                        data-time-slot-id='{$slot['time_slot_id']}' 
                        data-class-id='{$classId}' 
                        data-start-time='{$slot['start_time']}'>
                        {$slot['start_time']} - {$slot['end_time']}
                      </button>";
            }
            echo "</div>";
        } else {
            echo "<p class='text-muted'>No available time slots for this class.</p>";
        }

        echo "</div>";
    }
} else {
    echo "<p class='text-muted'>No classes available for the selected type.</p>";
}
?>
