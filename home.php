<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Bootstrap Link-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Style sheet Link -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* Prevent scrolling */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden; /* Disable scrollbars */
        }

        /* Fullscreen Container */
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden; /* Disable scrolling */
        }

        /* Ensure content doesn't overflow */
        .row {
            display: flex;
            flex-wrap: nowrap;
            max-width: 100vw; /* Prevent overflow on horizontal axis */
            max-height: 100vh; /* Prevent overflow on vertical axis */
            margin: 0;
            height: 100%;
        }

        .col-md-6 {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Image Style */
        img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Image will cover the area without distortion */
        }

        /* Styling for buttons to align them in a row */
        .btn-container {
            display: flex;
            justify-content: space-between; /* Space between the buttons */
            gap: 10px; /* Optional, adds some space between the buttons */
        }

        .btn {
            flex: 1; /* Buttons take up equal space */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row w-100 shadow-lg rounded">
            <!-- Left Section: Image -->
            <div class="col-md-6 p-0">
                <img src="image/ITcollege.jpeg" alt="College of IT" class="img-fluid rounded-start">
            </div>

            <!-- Right Section: About Us and Buttons -->
            <div class="col-md-6 p-5 bg-white d-flex flex-column justify-content-center">
                <h2>About Us</h2>
                <p>
                    Welcome to our room booking system! This platform allows students and instructors
                    of the IT College to book rooms efficiently and securely. Whether you're planning
                    a meeting or reserving a space for studying, our system makes it easy to find and manage bookings.
                </p>
                <div class="mt-4 btn-container">
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
