<?php
session_start();
require 'connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check the credentials (email and password)
    $stmt = $pdo->prepare("SELECT email, fullName, password FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store user information in the session
        $_SESSION['user'] = $user['email']; // Store email in session
        $_SESSION['fullName'] = $user['fullName']; // Store full name in session
        
        header("Location: home.php"); // Redirect to home page
        exit();
    } else {
        echo "Invalid email or password!";
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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container mt-5">
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
        <p class="text-center mt-3">Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>

</html>
