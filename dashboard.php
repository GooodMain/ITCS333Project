<?php
session_start();
include 'connection.php';
//only admins can access
if ($_SESSION['user_type'] !== 'admin') {
    header("Location: home.php");
    exit();
}

// Fetch metrics
$totalRooms = $pdo->query("SELECT COUNT(*) FROM class_type")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
$classTypes = $pdo->query("SELECT type_name, class_count FROM class_type")->fetchAll(PDO::FETCH_ASSOC);
$totalClasses = $pdo->query("SELECT SUM(class_count) AS total FROM class_type")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Style sheet Link-->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include("header.php"); ?>

<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Total Types of Rooms</h5>
                    <p><?= $totalRooms ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Total Bookings</h5>
                    <p><?= $totalBookings ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <p><?= $totalUsers ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Total Classes</h5>
                <p><?= $totalClasses ?></p>
            </div>
        </div>
    </div>
    <hr>
    <h3>Class Types</h3>
    <div class="row">
        <?php foreach ($classTypes as $classType): ?>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($classType['type_name']) ?></h5>
                        <p>Class Count: <?= $classType['class_count'] ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
