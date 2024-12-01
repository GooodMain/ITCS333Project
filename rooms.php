<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Corrected connection details
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'booking_system';

// Establish the connection
$con = mysqli_connect($host, $username, $password, $dbname);
// Check connection
if (!$con) {
    die('Connection error: ' . mysqli_connect_error());
}

// Query to fetch data from the 'class' table
$sql = 'SELECT * FROM class_type';
$result = mysqli_query($con, $sql);
// Fetch and print all records
$class = mysqli_fetch_all($result, MYSQLI_ASSOC);
// Free result and close connection
mysqli_free_result($result);
mysqli_close($con);

?>


<!DOCTYPE html>
<html>

<head>
    <title>Rooms</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<body>
    <?php include("header.php"); ?>

    <h4 class="center grey-text">ROOMS</h4>

<div class="container">
    <div class="row">
        <?php foreach ($class as $index => $room) { // Add an index for unique IDs ?>
            <div class="col s6 md3">
                <div class="card z-depth-0">
                    <div class="card-content center">
                        <h6><?php echo htmlspecialchars($room['type']); ?></h6>
                    </div>

                    <div class="card-action right-align">
                        <!-- Button to trigger the modal -->
                        <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#modal-<?php echo $index; ?>">More Details</button>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-<?php echo $index; ?>" role="dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title"><?php echo htmlspecialchars($room['type']); ?></h4>
                                </div>
                                <div class="modal-body">
                                    <p>Details about the room:</p>
                                    <p>Capacity: <?php echo htmlspecialchars($room['capacity']); ?></p>
                                    <p>Equipments: <?php echo htmlspecialchars($room['equipments']); ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
    
    <?php include("footer.php"); ?>
</body>

</html>