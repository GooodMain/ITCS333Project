<?php
require 'connection.php';

$class_type_id = $_GET['class_type_id'];
$booking_date = $_GET['booking_date'];

// Get classes of the selected type
$db = $pdo->prepare("SELECT * FROM classes WHERE class_type_id = ?");
$db->execute([$class_type_id]);
$classes = $db->fetchAll(PDO::FETCH_ASSOC);

// Get all time slots
$db = $pdo->prepare("SELECT * FROM time_slots");
$db->execute();
$timeSlots = $db->fetchAll(PDO::FETCH_ASSOC);

// Get booked time slots
$db = $pdo->prepare("SELECT class_id, time_slot_id FROM bookings WHERE booking_date = ?");
$db->execute([$booking_date]);
$bookedSlots = $db->fetchAll(PDO::FETCH_ASSOC);

$bookedMap = [];
foreach ($bookedSlots as $slot) {
    $bookedMap[$slot['class_id']][$slot['time_slot_id']] = true;
}

foreach ($classes as $class): ?>
    <h5><?= htmlspecialchars($class['class_num']) ?></h5>
    <div class="d-flex flex-wrap mb-3">
        <?php foreach ($timeSlots as $slot): ?>
            <?php $isBooked = isset($bookedMap[$class['class_id']][$slot['time_slot_id']]); ?>
            <form method="POST" class="me-2">
                <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                <input type="hidden" name="time_slot_id" value="<?= $slot['time_slot_id'] ?>">
                <input type="hidden" name="booking_date" value="<?= htmlspecialchars($booking_date) ?>">
                <button type="submit" class="btn <?= $isBooked ? 'btn-secondary' : 'btn-primary' ?>" <?= $isBooked ? 'disabled' : '' ?>>
                    <?= htmlspecialchars($slot['start_time'] . ' - ' . $slot['end_time']) ?>
                </button>
            </form>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>