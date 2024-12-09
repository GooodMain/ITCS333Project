<?php
session_start();
require 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check for POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['booking_id'])) {
        $bookingId = $data['booking_id'];
        $userId = $_SESSION['user_id'];

        try {
            // Update booking status to 'cancelled'
            $stmt = $pdo->prepare("
                UPDATE bookings 
                SET booking_status = 'cancelled' 
                WHERE booking_id = :booking_id AND user_id = :user_id
            ");
            $stmt->execute([
                ':booking_id' => $bookingId,
                ':user_id' => $userId,
            ]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to cancel booking.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'An error occurred while cancelling the booking.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing booking ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
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


