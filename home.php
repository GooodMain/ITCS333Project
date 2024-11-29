<?php
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
    <title>Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

<body>
    <?php include("header.php"); ?>

    <h4 class="center grey-text">ROOMS</h4>

    <div clase="container">
        <div class="row">
            <?php foreach ($class as $room) { ?>

                <div class="col s6 md3">
                    <div class="card z-depth-0">
                        <div class="card-content center">
                            <h6><?php echo htmlspecialchars($room['type']); ?></h6>
                            <div><?php echo htmlspecialchars($room['capacity']); ?>
                                <div><?php echo htmlspecialchars($room['equipments']); ?></div>
                            </div>
                            <div class="card-action right-align"></div>
                            <a class="brand-text" href="#">Room Details</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php include("footer.php"); ?>
</body>

</html>