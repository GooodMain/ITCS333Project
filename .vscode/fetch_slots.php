<?php
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'] ?? null;
    $date = $_POST['date'] ?? null;

    if (!$room_id || !$date) {
        echo json_encode(['error' => 'Room ID and Date are required.']);
        exit;
    }

    try {
        // Get room type
        $query = "SELECT type FROM class_type WHERE id = :room_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':room_id' => $room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            echo json_encode(['error' => 'Invalid Room ID.']);
            exit;
        }

        $roomType = $room['type'];
        $slots = [];

        // Define slots based on room type
        $dayOfWeek = date('N', strtotime($date));

        if ($roomType === 'Lab' || $roomType === 'Benefit Lab') {
            $slots = [
                ['start' => '14:00', 'end' => '15:40'],
                ['start' => '16:00', 'end' => '17:40'],
                ['start' => '18:00', 'end' => '19:40']
            ];
        } elseif ($roomType === 'Open Lab') {
            $slots = [
                ['start' => '14:00', 'end' => '16:00'],
                ['start' => '16:00', 'end' => '18:00'],
                ['start' => '18:00', 'end' => '20:00']
            ];
        } elseif ($roomType === 'Classroom') {
            if ($dayOfWeek == 1 || $dayOfWeek == 3) { // Monday, Wednesday
                $slots = [
                    ['start' => '08:00', 'end' => '09:15'],
                    ['start' => '09:30', 'end' => '10:45'],
                    ['start' => '11:00', 'end' => '12:15'],
                    ['start' => '12:30', 'end' => '13:45'],
                    ['start' => '14:00', 'end' => '15:15'],
                    ['start' => '15:30', 'end' => '16:45']
                ];
            } else { // Sunday, Tuesday, Thursday
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
        }

        echo json_encode(['slots' => $slots]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to fetch slots: ' . $e->getMessage()]);
    }
}
