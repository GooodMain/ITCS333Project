<?php
require 'connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "<div class='alert alert-danger'>You must be logged in to book a room.</div>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container mt-5">
        <h1 class="text-center">Room Booking System</h1>

        <form id="bookingForm" class="mt-4">
            <div class="mb-3">
                <label for="date" class="form-label">Select Date:</label>
                <input type="date" id="date" name="date" class="form-control" required>
            </div>

            <div class="mb-3">
    <label for="classType" class="form-label">Select Room Type:</label>
    <select id="classType" name="classType" class="form-select" required>
        <option value="">-- Select the type of class --</option>
        <?php
        // Fetch room types
        $result = $pdo->query("SELECT * FROM class_type");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$row['class_type_id']}'>{$row['type_name']}</option>";
        }
        ?>
    </select>
</div>

        </form>

        <div id="availableclasses" class="mt-4"></div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Booking Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Successfully booked the time slot!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Disable Fridays and Saturdays, and past dates
            const today = new Date();
            const dayOfWeek = today.getDay();
            const minDate = today.toISOString().split("T")[0]; // Current date in YYYY-MM-DD format

            // Disable Fridays and Saturdays (5 and 6 are Friday and Saturday)
            let disabledDays = [5, 6]; // Friday and Saturday
            let disabledDates = [];

            // Disable Fridays and Saturdays and past dates
            $("#date").attr("min", minDate); // Disable past dates
            $("#date").on("input", function () {
                const selectedDate = new Date($(this).val());
                const selectedDay = selectedDate.getDay();

                // If selected day is Friday or Saturday, alert and reset to current date
                if (disabledDays.includes(selectedDay)) {
                    alert("You cannot select Fridays or Saturdays.");
                    $(this).val(minDate);
                }
            });

            // Fetch classes when room type and date are selected
            $('#classType, #date').on('change', function () {
                const date = $('#date').val();
                const classType = $('#classType').val();

                if (date && classType) {
                    $.ajax({
                        url: 'get_classes.php',
                        type: 'GET',
                        data: { date: date, classType: classType },
                        success: function (data) {
                            $('#availableclasses').html(data);
                        }
                    });
                }
            });

            // Book a time slot
            $(document).on('click', '.time-slot', function () {
                const timeSlotId = $(this).data('time-slot-id');
                const classId = $(this).data('class-id');
                const date = $('#date').val();

                $.ajax({
                    url: 'book_slot.php',
                    type: 'POST',
                    data: { timeSlotId: timeSlotId, classId: classId, date: date },
                    success: function (response) {
                        if (response === 'success') {
                            // Show success modal
                            $('#successModal').modal('show');

                            // Refresh the available classes and time slots
                            $('#classType').trigger('change');
                        } else {
                            alert('Error booking the time slot. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
    <?php include("footer.php"); ?>
</body>
</html>
