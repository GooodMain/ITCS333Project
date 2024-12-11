<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOB IT Room Booking</title>
    <!-- Include Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
nav {
    background: linear-gradient(150deg, #0056b3, #0099ff);
    color: white;
    padding: 0 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}

nav .brand-logo {
    font-size: 1.8rem;
    font-weight: bold;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
}

nav .brand-logo img {
    width: 50px;
    height: auto;
    margin-right: 15px;
}

.nav-wrapper ul {
    display: flex;
    align-items: center;
}

.nav-wrapper ul li a {
    font-size: 14px; /* Standardize text size */
    font-family: Arial, sans-serif; /* Standardize font family */
    color: white;
    padding: 0 10px;
    transition: background 0.3s;
}

.nav-wrapper ul li a:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.sidenav a {
    color: #0056b3;
    font-size: 14px; /* Match the navbar text size */
    font-family: Arial, sans-serif; /* Standardize font family */
}

.sidenav .user-view {
    background: linear-gradient(150deg, #0056b3, #0099ff);
    color: white;
    padding: 20px;
}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="nav-wrapper">
            <a href="index.php" class="brand-logo">
                <img src="image/pic.png" alt="Logo"> UOB - IT College Room Booking
            </a>
            <a href="#" data-target="mobile-nav" class="sidenav-trigger">
                <i class="material-icons">menu</i>
            </a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
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

    <!-- Mobile Navigation -->
    <ul class="sidenav" id="mobile-nav">
        <li>
            <div class="user-view">
                <span class="white-text name">Welcome, <?php echo $_SESSION['user_name'] ?? 'Guest'; ?></span>
            </div>
        </li>
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

    <!-- Include Materialize and Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the sidenav for mobile navigation
            var elems = document.querySelectorAll('.sidenav');
            M.Sidenav.init(elems);
        });
    </script>
</body>
</html>
