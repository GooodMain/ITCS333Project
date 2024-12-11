<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
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

        .container-flex {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .card {
            width: 23%;
            margin: 10px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .card img {
            max-width: 100%;
            height: auto;
        }
    </style>
    <script>
        function fillSearch(classType) {
            document.getElementById('search').value = classType;
            document.getElementById('searchForm').submit();
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
    </div>

    <?php include("footer.php"); ?>
</body>
</html>

