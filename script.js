$(document).ready(function () {
    // Disable Fridays and Saturdays, and past dates
    const today = new Date();
const minDate = today.toISOString().split("T")[0]; // Current date in YYYY-MM-DD format
$("#date").attr("min", minDate);

$("#date").on("change", function () {
    const selectedDate = new Date($(this).val());
    const selectedDay = selectedDate.getDay();

    // Reset to current date if invalid (Friday, Saturday, or past)
    if (selectedDate < today || [5, 6].includes(selectedDay)) {
        alert("You cannot select Fridays, Saturdays, or past dates.");
        $(this).val(minDate);
    }
});


    // Fetch classes when class type and date are selected
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

        // Log the data to make sure it is correct
        console.log("Booking Data:", { timeSlotId, classId, date });

        $.ajax({
            url: 'book_slot.php',
            type: 'POST',
            data: { timeSlotId: timeSlotId, classId: classId, date: date },
            success: function (response) {
                console.log("Response from server:", response);  // Log server response

                if (response === 'success') {
                    // Show success modal
                    $('#successModal').modal('show');

                    // Refresh the available classes and time slots
                    $('#classType').trigger('change');
                } else {
                    alert('Error booking the time slot. Please try again.');
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", error);
            }
        });
    });
});

