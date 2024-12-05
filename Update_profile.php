<?php
session_start();
include 'connection.php';

// Validate CSRF token
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

// Retrieve form data
$userId = $_POST['user_id'];
$email = $_POST['email'];
$fullName = $_POST['fullName'];
$phoneNum = $_POST['phoneNum'];
$password = $_POST['password'];
$userType = $_POST['user_type'];

// Debug input data
var_dump($email, $fullName, $phoneNum, $password, $userType);

// Validate form data
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}
if (empty($fullName)) {
    die("Full name is required.");
}
if (empty($phoneNum) || !preg_match("/^[0-9]{10}$/", $phoneNum)) {
    die("Invalid phone number. Must be 10 digits.");
}

// Handle password update
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
} else {
    // Retrieve existing password from the database
    $stmt = $pdo->prepare("SELECT password FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);
    $hashedPassword = $stmt->fetchColumn();
}

// Handle profile picture upload
if (!empty($_FILES['picture']['name'])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES['picture']['name']);
    $targetFilePath = $targetDir . $fileName;

    // Validate file type
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($fileType), $allowedTypes)) {
        die("Only JPG, JPEG, PNG, and GIF files are allowed.");
    }

    // Move the uploaded file
    if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFilePath)) {
        $profilePicture = $targetFilePath;
    } else {
        die("Failed to upload profile picture.");
    }
} else {
    // Keep existing profile picture
    $stmt = $pdo->prepare("SELECT picture FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profilePicture = $stmt->fetchColumn();
}

// Debug profile picture path
var_dump($profilePicture);

// Update user data in the database
$stmt = $pdo->prepare("UPDATE user SET email = ?, fullName = ?, phoneNum = ?, password = ?, picture = ?, user_type = ? WHERE user_id = ?");
$success = $stmt->execute([$email, $fullName, $phoneNum, $hashedPassword, $profilePicture, $userType, $userId]);

if ($success) {
    echo "Profile updated successfully!";
    header("Location: profile.php"); // Redirect to the profile page
    exit;
} else {
    // Display error details
    $errorInfo = $stmt->errorInfo();
    echo "Failed to update profile. Error: " . $errorInfo[2];
}
?>