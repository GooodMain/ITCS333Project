<?php
session_start();
include 'connection.php';

// Check if form is submitted and user is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $email = $_POST['email'];
    $fullName = $_POST['fullName'];
    $phoneNum = $_POST['phoneNum'];
    $user_type = $_POST['user_type'];
    
    // Handle password update
    $password = $_POST['password'];
    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

    // Handle file upload
    $profilePicture = null;
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == UPLOAD_ERR_OK) {
        // Define the upload directory and ensure it exists
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $profilePicture = $uploadDir . basename($_FILES['profile-picture']['name']);
        move_uploaded_file($_FILES['profile-picture']['tmp_name'], $profilePicture);
    }

    // Prepare the SQL update statement
    $sql = "UPDATE users SET email = ?, fullName = ?, phoneNum = ?, user_type = ?" . ($hashedPassword ? ", password = ?" : "") . ($profilePicture ? ", profile_picture = ?" : "") . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $params = [$email, $fullName, $phoneNum, $user_type];
    if ($hashedPassword) {
        $params[] = $hashedPassword;
    }
    if ($profilePicture) {
        $params[] = $profilePicture;
    }
    $params[] = $userId;

    // Execute the update statement
    if ($stmt->execute($params)) {
        echo "Profile updated successfully!";
        header("Location: edit_profile.php"); // Redirect to profile page after successful update
        exit();
    } else {
        echo "Error updating profile.";
    }
}
?>