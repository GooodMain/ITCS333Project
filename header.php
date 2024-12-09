<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asma</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style type="text/css">
        body {
            background-color: #f0f0f0;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background: linear-gradient(150deg, #0056b3, #0099ff);
            color: white;
            padding: 30px 20px;
            text-align: left;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
        }

        .brand-text {
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0;
            text-transform: uppercase;
        }

        .header img {
            width: 120px;
            height: auto;
            margin-right: 20px;
        }

        nav {
            background-color: #ffffff;
            border-bottom: 2px solid #0056b3;
            z-index: 10;
        }

        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 10px 0;
            flex-wrap: wrap;
        }

        .nav-buttons a {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s;
            text-align: center;
        }

        .nav-buttons a:hover {
            background-color: #0056b3;
        }

        #menu-button {
            background: #007BFF;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
            display: none;
        }

        #menu-button:hover {
            background: #0056b3;
        }

        @media (max-width: 768px) {
            .nav-buttons {
                display: none;
            }
            #menu-button {
                display: inline-block;
            }
        }

        @media (min-width: 769px) {
            #menu-button {
                display: none;
            }
        }

        .sidenav {
            width: 250px;
        }
        .sidenav a {
            padding: 15px;
            text-align: left;
        }

        .content {
            padding-top: 80px;
        }
    </style>
</head>
<body>
    <header class="header">
        <img src="image/pic.png" />
        <h4 class="brand-logo brand-text">UOB - IT COLLEGE ROOM BOOKING</h4>
    </header>

    <nav class="white z-depth-0">
        <div class="container">
            <a href="#" id="menu-button" data-target="mobile-nav" class="btn z-depth-0 sidenav-trigger">
                <span id="menu-text">Menu</span>
            </a>
            <ul class="nav-buttons">
                <li><a href="index.php">Home</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="bookings.php">Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="logout.php">Logout</a></li>
                <?php if ($_SESSION['user_type'] === 'admin'): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="room_management.php">Room Management</a></li>
                    <li><a href="user_management.php">User Management</a></li>
                <?php endif; ?>
            </ul>
            <ul id="mobile-nav" class="sidenav">
                <li><a href="index.php">Home</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="bookings.php">Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="reports.php">Reports</a></li>
                <?php if ($_SESSION['user_type'] === 'admin'): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="room_management.php">Room Management</a></li>
                    <li><a href="user_management.php">User Management</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="content">
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.sidenav');
            M.Sidenav.init(elems);
        });
    </script>
</body>
</html>