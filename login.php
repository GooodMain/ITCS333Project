<?php
session_start();
require 'connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check the credentials (email and password)
    $stmt = $pdo->prepare("SELECT user_id, email, fullName, password, user_type FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store user information in the session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user'] = $user['email']; // Store email in session
        $_SESSION['fullName'] = $user['fullName']; // Store full name in session
        $_SESSION['user_type'] = $user['user_type']; // Store user type in session

        // Redirect based on user type
        if ($_SESSION['user_type'] === 'admin') {
            header("Location: dashboard.php"); // Redirect to admin dashboard
        } else {
            header("Location: index.php"); // Redirect to the user dashboard or home page
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap Link-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Style sheet Link-->
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            width:100%;
            min-height: 700px;
            overflow: hidden;
            background-color: lightgrey;
        }

        .container {
            height:100%;
            width:50%;
        }

        #leftdiv{
            float:left;
            background-color: #f0f0f0;
            height: 90vh;
            border-right: 2.5px solid lightgrey; 
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
            font-size: 1.2rem;
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
        }
        .btn:hover {
            background-color: lightgrey;
            color: grey;
            box-shadow: 5px 5px 1px 0px lightgrey;
            border: 1px solid grey;
        }

        .btnR {
            color: black;
        }
        .btnR:hover {
            color: grey;
        }

    </style>
</head>

<body>
    <div class="container mt-5" id="leftdiv"> 
        <div id="leftdivcard">
            <img src="image/ITcollege.jpeg" alt="College of IT" class="img-fluid rounded-start">
        </div>
    </div>

    <div class="container mt-5" id="rightdiv">  
        <div id="rightdivcard"> 
            <h2 class="text-center">Login</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3">Don't have an account? <a class="btnR" href="register.php">Register</a></p>
        </div>
    </div>
</body>

</html>
