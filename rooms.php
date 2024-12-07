<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

include 'connection.php';

try {
    $result = $pdo->query('SELECT * FROM class_type');
    $class = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching class types: " . $e->getMessage());
}
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
                            <h6><?php echo htmlspecialchars($room['type_name']); ?></h6>
                            <div style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap;"><img
                                    src="image/<?php echo htmlspecialchars($room['image']); ?>"
                                    style="max-width: 100%; height: auto; margin: auto;" /></div>
                        </div>

                        <div class="card-action right-align">
                            <!-- Button to trigger the modal -->
                            <button type="button" class="btn btn-info btn-lg" data-toggle="modal"
                                data-target="#modal-<?php echo $index; ?>">More Details</button>

                            <!-- Modal -->
                            <div class="modal fade" id="modal-<?php echo $index; ?>" role="dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div id="txt" style="float:left;"><img
                                                src="image/<?php echo htmlspecialchars($room['image']); ?>"
                                                style="width:300px; height:200px;" /></div>
                                        <h4 class="modal-title"><?php echo htmlspecialchars($room['type_name']); ?></h4>
                                        <br> <br> <br> <br> <br>
                                        <p>Details about this room:</p>
                                        <br>
                                        <p>Capacity: <?php echo htmlspecialchars($room['capacity']); ?></p>
                                        <br>
                                        <p>Equipments: <?php echo htmlspecialchars($room['equipments']); ?></p>
                                        <br> <br>
                                        <p>There are <?php echo htmlspecialchars($room['class_count']); ?> of this type in the IT college</p>
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