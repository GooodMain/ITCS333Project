<?php
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $email = $_POST['email'];
    $fullName = $_POST['fullName'];
    $phoneNum = $_POST['phoneNum'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $error = '';

    // Regular Expressions
    $stuEmail_RE = '/^[a-zA-Z0-9._%+-]+@stu\.uob\.edu\.bh$/';
    $instructorEmail_RE = '/^[a-z]+@uob\.edu\.bh$/';
    $fullName_RE = '/^[a-zA-Z\s]{3,50}$/';
    $phoneNum_RE = '/^(00973|\+973)?\s?(([36]\d{7})|(17\d{6}))$/';
    $password_RE = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9_#@%*\\-]{8,50}$/";

    // Validation
    if (!preg_match($stuEmail_RE, $email) && !preg_match($instructorEmail_RE, $email)) 
    {
        $error = "Invalid UoB email format. <br>";
    }

    if (!preg_match($fullName_RE, $fullName)) 
    {
        $error .= "Full name must contain only letters and spaces. <br>";
    }

    if (!preg_match($phoneNum_RE, $phoneNum)) 
    {
        $error .= "Invalid phone number format. <br>";
    }

    if (!preg_match($password_RE, $password)) 
    {
        $error .= "Password must be 8-50 characters, include uppercase, lowercase, digit, and special character. <br>";
    }

    // Check if passwords match
    if ($password !== $confirmPassword) 
    {
        $error .= "Passwords do not match. <br>";
    }

    // Check if email already exists in the database
    if ($error == '') 
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) 
        {
            $error .= "This email is already registered. <br>";
        }
    }

    // If validation fails, display error message
    if ($error) 
    {
        echo "<div class='alert alert-danger'>$error</div>";
    } 
    else 
    {
        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert into the database
        $stmt = $pdo->prepare("INSERT INTO user (email, fullName, phoneNum, password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$email, $fullName, $phoneNum, $passwordHash])) 
        {
            // Redirect to login page on success
            header("Location: login.php");
            exit();
        } 
        else 
        {
            echo "<div class='alert alert-danger'>Registration failed. Please try again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Bootstrap Link-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
        <h2 class="text-center">Register</h2>
        <form method="POST" class="mt-4" onsubmit="return validatePasswords()">
            <div class="mb-3">
                <label for="email" class="form-label">UOB Email:</label>
                <input type="email" id="email" name="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="fullName" class="form-label">Full Name:</label>
                <input type="text" id="fullName" name="fullName" class="form-control" required value="<?= isset($fullName) ? htmlspecialchars($fullName) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="phoneNum" class="form-label">Phone Number:</label>
                <input type="text" id="phoneNum" name="phoneNum" class="form-control" required value="<?= isset($phoneNum) ? htmlspecialchars($phoneNum) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
            </div>
            <p id="error-message" class="text-danger text-center"></p>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="text-center mt-3">Already have an account? <a class="btnR" href="login.php">Login</a></p>
    </div></div>

    <script>
        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;
            const errorMessage = document.getElementById("error-message");

            if (password !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match!";
                return false; // Prevent form submission
            }

            errorMessage.textContent = ""; // Clear the error message if passwords match
            return true; // Allow form submission
        }
    </script>
</body>

</html>
