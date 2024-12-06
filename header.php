<head>
    <title>Asma</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style type="text/css">
        .brand {
            background: #cbb09c !important;
        }

        .brand-text {
            color: #cbb09c !important;
            background-color: lightgrey;
            font-weight: bold;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: lightgrey;
        }

        #menu-text {
            text-decoration: underline;
        }

        /* Media query for responsiveness */
        @media (max-width: 768px) {
            .nav-buttons { 
                
                display: none; /* Hide buttons */
            }
            #menu-button { 
                display: inline-block; /* Show burger menu */
            }
        }

        @media (min-width: 769px) {
            #menu-button { 
                display: none; /* Hidden by default; visible only on smaller screens */
                text-align: center; /* Center text inside the button */
                padding: 0 10px; /* Same padding as other buttons */
                width: auto; /* Ensure it adjusts to fit the text */
            }
         }
                
    </style>
</head>
<body>

<h4 class="brand-logo brand-text">UOB - IT COLLEGE ROOM BOOKING</h4>

<nav class="white z-depth-0">
    <div class="container">
        <!-- Burger Menu for smaller screens -->
        <a href="#" id="menu-button" data-target="mobile-nav" class="btn brand z-depth-0 sidenav-trigger">
            <span id="menu-text">Menu</span>
        </a>

        <!-- Desktop Navigation -->
        <ul class="nav-buttons">
            <li><a href="index.php" class="btn brand z-depth-0">Home</a></li>
            <li><a href="rooms.php" class="btn brand z-depth-0">Rooms</a></li>
            <li><a href="bookings.php" class="btn brand z-depth-0">Bookings</a></li>
            <li><a href="profile.php" class="btn brand z-depth-0">Profile</a></li>
            <li><a href="reports.php" class="btn brand z-depth-0">Reports</a></li>
            <li><a href="logout.php" class="btn brand z-depth-0">Logout</a></li>
            <?php if ($_SESSION['user_type'] === 'admin'): ?>
                <li><a class="btn brand z-depth-0" href="dashboard.php">Dashboard</a></li>
                <li><a class="btn brand z-depth-0" href="room_management.php">Room Management</a></li>
            <?php endif; ?>
        </ul>

        <!-- Mobile Navigation -->
        <ul id="mobile-nav" class="sidenav">
            <li><a href="index.php" class="btn brand z-depth-0">Home</a></li>
            <li><a href="rooms.php" class="btn brand z-depth-0">Rooms</a></li>
            <li><a href="bookings.php" class="btn brand z-depth-0">Bookings</a></li>
            <li><a href="profile.php" class="btn brand z-depth-0">Profile</a></li>
            <li><a href="reports.php" class="btn brand z-depth-0">Reports</a></li>
            
            <?php if ($_SESSION['user_type'] === 'admin'): ?>
                <li><a class="btn brand z-depth-0" href="dashboard.php">Dashboard</a></li>
                <li><a class="btn brand z-depth-0" href="room_management.php">Room Management</a></li>
            <?php endif; ?>

            <li><a href="logout.php" class="btn brand z-depth-0">Logout</a></li>
        </ul>
    </div>
</nav>



<!-- Include JS for Materialize and Mobile Menu -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the sidenav (burger menu)
        var elems = document.querySelectorAll('.sidenav');
        M.Sidenav.init(elems);
    });
</script>

</body>
