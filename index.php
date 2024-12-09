<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: home.php"); // Redirect to Home page if not logged in
    exit();
}

// Fetch user information from session
$userEmail = $_SESSION['user'];
$userFullName = isset($_SESSION['fullName']) ? $_SESSION['fullName'] : 'Unknown User';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .center {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }

        .welcome-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            margin: auto;
        }

        h3 {
            color: #0056b3;
            margin-bottom: 10px;
        }

        h4 {
            color: #007bff;
            margin-bottom: 30px;
        }

        p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        strong {
            color: #0056b3;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php include("header.php"); ?>

    <div class="center">
        <div class="welcome-card">
            <h3>Welcome to the IT College</h3>
            <h4>University of Bahrain</h4>
            <p>Hello, <strong><?= htmlspecialchars($userFullName) ?></strong></p>
            <p>
                You can explore, check <a href="rooms.php">available rooms</a> on any date and time slot, 
                and <a href="bookings.php">book</a> a room at the IT College.
            </p>
        </div>
    </div>

    <?php include("footer.php"); ?>
</body>

</html>
