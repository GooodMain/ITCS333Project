<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: home.php"); // Redirect to Home page if not logged in
    exit();
}

include 'connection.php';


$result = $pdo->query('SELECT * FROM class_type');
$class = $result->fetchAll(PDO::FETCH_ASSOC);

// Check if a search term is set
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

try {
    if ($searchTerm) {
        $stmt = $pdo->prepare('SELECT class_num, type_name, capacity, equipments, image, class_id 
                               FROM classes 
                               JOIN class_type ON classes.class_type_id = class_type.class_type_id
                               WHERE class_num LIKE :searchTerm');
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

?>
<!DOCTYPE html>
<html>

<head>
    <title>Rooms</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        img {
            width: 100px;
            height: auto;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            touch-action: manipulation;
            cursor: pointer;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .btn-primary {
            color: #fff;
            background-color: #337ab7;
            border-color: #2e6da4;
        }
    </style>
</head>

<body>
    <?php include("header.php"); ?>
    <div class="container">
        <h2 styles="position=center">Discover our Rooms</h2>
        <div class="row">
            <?php foreach ($class as $index => $room) { // Add an index for unique IDs ?>
                <div class="col s6 md3">
                    <div class="card z-depth-0">
                        <div class="card-content center">
                            <h6><?php echo htmlspecialchars($room['type_name']); ?></h6>
                            <div style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap;"><img
                                    src="image/<?php echo htmlspecialchars($room['image']); ?>"
                                    style="width: 80%; height: 80%;" /></div>
                        </div>

                        <div class="card-action right-align">
                            <!-- Button to trigger the modal -->
                            <button type="button" class="btn btn-info btn-lg" data-toggle="modal"
                                data-target="#modal-<?php echo $index; ?>">More Details</button>

                            <!-- Modal -->
                            <div class="modal" id="modal-<?php echo $index; ?>" role="dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div id="txt" style="float:left;">
                                            <img src="image/<?php echo htmlspecialchars($room['image']); ?>"
                                                style="width:450px; height:auto;" />
                                        </div>
                                        <h4 class="modal-title"><?php echo htmlspecialchars($room['type_name']); ?></h4>
                                        <br><br><br><br><br>
                                        <p>Details about this room:</p>
                                        <br><br>
                                        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity']); ?></p>
                                        <br>
                                        <p><strong>Equipments:</strong> <?php echo htmlspecialchars($room['equipments']); ?>
                                        </p>
                                        <br>
                                        <p>There are <?php echo htmlspecialchars($room['class_count']); ?> of this type in
                                            the IT college</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="container">
            <form method="GET" action="rooms.php">
                <div class="form-group">
                    <label for="search">Search by Class Number:</label>
                    <input type="text" name="search" id="search" class="form-control"
                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <table>
            <tr>
                <th>Image</th>
                <th>Class Number</th>
                <th>Type</th>
                <th>Available Time Slots</th>
            </tr>
            <?php if (count($rooms) > 0) {
                foreach ($rooms as $row) {
                    echo "<tr>";
                    echo "<td><img src='image/" . htmlspecialchars($row["image"]) . "' alt='" . htmlspecialchars($row["type_name"]) . "'></td>";
                    echo "<td>" . htmlspecialchars($row["class_num"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["type_name"]) . "</td>";

                    // Fetch available time slots for the current date, including canceled bookings
                    $stmt_slots = $pdo->prepare('SELECT time_slot_id, start_time, end_time 
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
                            echo "<button class='btn btn-primary'>" . htmlspecialchars($slot["start_time"]) . " - " . htmlspecialchars($slot["end_time"]) . "</button> ";
                        }
                    } else {
                        echo "No available time slots";
                    }
                    echo "</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No rooms available</td></tr>";
            } ?>
        </table>
    

    <?php include("footer.php"); ?>
</body>

</html>