<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Bootstrap Link-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .bdy {
            margin: 0 ;
            padding: 0 ;
            height: 100% ;
            width:100% ;
            min-height: 700px ;
            overflow: hidden ;
            background-color: lightgrey ;
            text-align: center ;
        }

        .containerr {
            height:100% ;
            width:50% ;
        }

        #leftdiv{
            float:left ;
            background-color: #f0f0f0 ;
            height: 90vh;
            border-right: 2.5px solid lightgrey;
            margin-top: 48px; 
            margin-bottom: 48px;
        }

        #leftdivcard{
            margin:0 auto;
            width: 50%;
            background-color: #f0f0f0;
            margin-top: 45vh; 
            transform: translateY(-50%);
            border-radius:10px;
            text-align: center;
        }

        #rightdiv{
            float:right;
            background-color: #f0f0f0;
            height: 90vh;
            font-size: 1.3rem;
            margin-top: 48px; 
            margin-bottom: 48px;
        }

        #rightdivcard{
            margin:0 auto;
            width: 50%;
            margin-top: 40vh;
            transform: translateY(-50%);
            background-position: bottom;
            background-size: 20px 2px;
            background-repeat: repeat-x;
        }

        img {
            max-width: 50%;
            max-height: 700px;
            border-radius:10px;
            position: center;
            box-shadow: 10px 10px 1px 0px lightgrey;
        }
        
        .btn {
            background-color: darkgrey;
            color: white;
            border: 1px solid grey;
            font-size: 1.2rem;
            width: 10vw;
        }
        .btn:hover {
            background-color: lightgrey;
            color: grey;
            box-shadow: 5px 5px 1px 0px lightgrey;
            border: 1px solid grey;
        }

    </style>
</head>

<body class="bdy">
    <div class="containerr" id="leftdiv">
        <div id="leftdivcard">
            <!-- Left Section: Image -->
            <img src="image/ITcollege.jpeg" alt="College of IT" class="img-fluid rounded-start">
        </div>
    </div>
    <!-- Right Section: About Us and Buttons -->
    <div class="containerr" id="rightdiv">
        <div id="rightdivcard"> 
            <h2>About us</h2>
            <p>
                Welcome to our room booking system! This platform allows students and instructors
                of the IT College to book rooms efficiently and securely. Whether you're planning
                a meeting or reserving a space for studying, our system makes it easy to find and manage bookings.
            </p>
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
        </div>
    </div>
</body>

</html>
