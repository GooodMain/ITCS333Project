<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php"); // Redirect to Home page if not logged in
    exit();
}

include 'connection.php';

// Fetch class types for the flexbox display
try {
    $result = $pdo->query('SELECT * FROM class_type');
    $class_types = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching class types: " . $e->getMessage());
}

// Check if a search term is set
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Fetch classes based on search term or get all classes
try {
    if ($searchTerm) {
        $stmt = $pdo->prepare('SELECT class_num, type_name, capacity, equipments, image, class_id 
                               FROM classes 
                               JOIN class_type ON classes.class_type_id = class_type.class_type_id
                               WHERE class_num LIKE :searchTerm OR type_name LIKE :searchTerm');
        $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $stmt = $pdo->prepare('SELECT class_num, type_name, capacity, equipments, image, class_id 
                               FROM classes 
                               JOIN class_type ON classes.class_type_id = class_type.class_type_id');
        $stmt->execute();
    }
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching class types: " . $e->getMessage());
}

// Get the current date
$current_date = date('Y-m-d');

// Book a time slot if confirmed by the user
if (isset($_POST['book_time_slot'])) {
    $class_id = $_POST['class_id'];
    $time_slot_id = $_POST['time_slot_id'];

    // Insert booking into the bookings table
    try {
        $stmt = $pdo->prepare('INSERT INTO bookings (user_id, class_id, time_slot_id, booking_date, booking_status) 
                               VALUES (:user_id, :class_id, :time_slot_id, :booking_date, "confirmed")');
        $stmt->execute([
            'user_id' => $_SESSION['user'],  // Assuming user ID is stored in the session
            'class_id' => $class_id,
            'time_slot_id' => $time_slot_id,
            'booking_date' => $current_date
        ]);
        echo "<script>alert('Time slot booked successfully!');</script>";
    } catch (PDOException $e) {
        die("Error booking time slot: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rooms and Class Types</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css">

    <script>
        function fillSearch(classType) {
            document.getElementById('search').value = classType;
            document.getElementById('searchForm').submit();
        }

        function confirmBooking(class_id, time_slot_id) {
            if (confirm("Are you sure you want to book this time slot?")) {
                document.getElementById('class_id').value = class_id;
                document.getElementById('time_slot_id').value = time_slot_id;
                document.getElementById('bookingForm').submit();
            }
        }
    </script>
</head>
<body>
    <?php include("header.php"); ?>

    <!-- Display class types in a flexbox layout -->
    <div>
        <h2>Discover Our Rooms</h2>
        <div class="container-flex">
            <?php foreach ($class_types as $index => $class_type) { ?>
                <div class="card">
                    <img src="image/<?php echo htmlspecialchars($class_type['image']); ?>" alt="<?php echo htmlspecialchars($class_type['type_name']); ?>">
                    <h3><?php echo htmlspecialchars($class_type['type_name']); ?></h3>
                    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($class_type['capacity']); ?></p>
                    <p><strong>Equipments:</strong> <?php echo htmlspecialchars($class_type['equipments']); ?></p>
                    <p>There are <?php echo htmlspecialchars($class_type['class_count']); ?> of this type in the IT college</p>
                    <!-- Button to trigger the search -->
                    <button type="button" class="btn btn-info" onclick="fillSearch('<?php echo htmlspecialchars($class_type['type_name']); ?>')">Search</button>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Search and display rooms -->
    <div class="container">
        <form id="searchForm" method="GET" action="rooms.php">
            <div class="form-group">
                <label for="search">Search by Class Number or Class Type:</label>
                <input type="text" name="search" id="search" class="form-control" value="<?php echo htmlspecialchars($searchTerm); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <form id="bookingForm" method="POST" action="rooms.php">
            <input type="hidden" name="class_id" id="class_id">
            <input type="hidden" name="time_slot_id" id="time_slot_id">
            <input type="hidden" name="book_time_slot" value="1">
        </form>

        <table>
            <tr>
                <th>Image</th>
                <th>Class Number</th>
                <th>Type</th>
                <th>Available Time Slots</th>
                <th>Comments</th> 
            </tr>
            <?php if (count($rooms) > 0) {
                foreach ($rooms as $row) {
                    echo "<tr>";
                    echo "<td><img src='image/" . htmlspecialchars($row["image"]) . "' alt='" . htmlspecialchars($row["type_name"]) . "'></td>";
                    echo "<td>" . htmlspecialchars($row["class_num"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["type_name"]) . "</td>";

                    // Fetch available time slots for the current date, including canceled bookings
                    $stmt_slots = $pdo->prepare('SELECT time_slot_id, DATE_FORMAT(start_time, "%H:%i") AS start_time, DATE_FORMAT(end_time, "%H:%i") AS end_time 
                                                 FROM time_slots 
                                                 WHERE time_slot_id NOT IN (
                                                     SELECT time_slot_id 
                                                     FROM bookings 
                                                     WHERE class_id = :class_id AND booking_date = :current_date AND booking_status = "confirmed"
                                                 )');
                    $stmt_slots->execute(['class_id' => $row['class_id'], 'current_date' => $current_date]);
                    $time_slots = $stmt_slots->fetchAll(PDO::FETCH_ASSOC);

                    echo "<td>";
                    if (count($time_slots) > 0) {
                        foreach ($time_slots as $slot) {
                            echo "<button class='btn btn-primary' onclick='confirmBooking(" . $row['class_id'] . ", " . $slot['time_slot_id'] . ")'>" . htmlspecialchars($slot["start_time"]) . " - " . htmlspecialchars($slot["end_time"]) . "</button> ";
                        }
                    } else {
                        echo "No available time slots";
                    }
                    echo "</td>";
                     // Add the "View Comments" button
            echo "<td><a href='comments.php?class_id=" . htmlspecialchars($row["class_id"]) . "' class='btn btn-primary'>View Comments</a></td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No rooms available</td></tr>";
            } ?>
        </table>
    </div>

    <?php include("footer.php"); ?>
</body>
</html>
