<?php

require 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Debugging: Check the incoming data
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'No data received.']);
        exit();
    }

    if (isset($data['booking_id'])) {
        echo cancelBooking($data['booking_id'], $_SESSION['user_id']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    }
    exit();
}

function cancelBooking($bookingId, $userId) {
    global $pdo;

    try {
        $db = $pdo->prepare("
            UPDATE bookings 
            SET booking_status = 'cancelled' 
            WHERE booking_id = :booking_id AND user_id = :user_id
        ");
        $db->execute([
            ':booking_id' => $bookingId,
            ':user_id' => $userId,
        ]);

        if ($db->rowCount() > 0) {
            return json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
        } else {
            return json_encode(['success' => false, 'message' => 'Cancellation failed.']);
        }
    } catch (PDOException $e) {
        return json_encode(['success' => false, 'message' => 'An error occurred while cancelling the booking.']);
    }
}


