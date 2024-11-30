<!DOCTYPE html>
<html>
<head>
<title>Bookings</title>
<body>
<?php include("header.php"); ?>

<?php
// Include connection file
require 'connection.php';

// Function to get room availability
function getRoomAvailability($room_id, $date, $pdo) {
    // Use named placeholders for the query
    $query = "SELECT start_time, end_time, class_id, cl_type 
              FROM room_bookings 
              WHERE room_id = :room_id AND DATE(start_time) = :date";

    // Prepare the statement
    $stmt = $pdo->prepare($query);

    // Bind parameters and execute
    $stmt->execute([
        ':room_id' => $room_id,
        ':date' => $date
    ]);

    // Fetch all results
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $availability = [];
    $day_start = new DateTime("$date 08:00:00");
    $day_end = new DateTime("$date 20:00:00");

    if (empty($bookings)) {
        $availability[] = [
            'start_time' => $day_start->format('H:i'),
            'end_time' => $day_end->format('H:i')
        ];
    } else {
        usort($bookings, function($a, $b) {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        });

        $last_end = clone $day_start;
        foreach ($bookings as $booking) {
            $booking_start = new DateTime($booking['start_time']);
            if ($last_end < $booking_start) {
                $availability[] = [
                    'start_time' => $last_end->format('H:i'),
                    'end_time' => $booking_start->format('H:i')
                ];
            }
            $last_end = new DateTime($booking['end_time']);
        }

        if ($last_end < $day_end) {
            $availability[] = [
                'start_time' => $last_end->format('H:i'),
                'end_time' => $day_end->format('H:i')
            ];
        }
    }

    return [
        'bookings' => $bookings,
        'availability' => $availability
    ];
}


require 'connection.php';

if (!isset($pdo)) {
    die("Database connection is not set. Check connection.php.");
}



$status = getRoomAvailability(1, '2024-11-30', $pdo);

echo "Bookings:\n";
foreach ($status['bookings'] as $booking) {
    echo "Class ID: " . $booking['class_id'] . 
         ", Type: " . $booking['cl_type'] . 
         ", From: " . $booking['start_time'] . 
         ", To: " . $booking['end_time'] . "\n";
}

echo "\nAvailable Times:\n";
foreach ($status['availability'] as $slot) {
    echo "From: " . $slot['start_time'] . " To: " . $slot['end_time'] . "\n";
}
?>

 
 
 
 
<?php include("footer.php"); ?>
</body>
</html>