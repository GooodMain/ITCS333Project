<?php
// Start the session
session_start();

// Include the database connection
include 'connection.php'; // Missing semicolon fixed here

// Check if the user is logged in via session
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit("User not logged in. Please log in to access your profile.");
}

// Fetch current user data using the logged-in user's user_id from the session
$userId = $_SESSION['user_id']; // Use 'user_id' consistently
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $pdo->prepare($sql);

// Execute the query with the user ID
$stmt->execute([$userId]);

// Fetch the user data from the database
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Handle the case where the user is not found
    exit("User not found or query failed.");
}

// Set a default profile picture
$defaultProfilePic = 'Unknown.png'; // Update this path to the location of Unknown.png
$currentProfilePic = !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : $defaultProfilePic;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General styles */
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 600px;
        }
        .card {
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
        }
        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: bold;
            color: #343a40;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-text {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="text-center">Edit Profile</h2>
            <!-- Profile Picture Display -->
            <div class="text-center mb-4">
                <img src="<?= htmlspecialchars($currentProfilePic) ?>" alt="Profile Picture" class="profile-pic">
            </div>

            <!-- Form -->
            <form id="profile-form" enctype="multipart/form-data" action="Update_profile.php" method="POST">
                <!-- Hidden field for user ID -->
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id'] ?? '') ?>">

                <!-- Email field -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                        required
                    >
                    <div class="invalid-feedback">Please enter your email.</div>
                </div>

                <!-- Full Name field -->
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name:</label>
                    <input 
                        type="text" 
                        id="fullName" 
                        name="fullName" 
                        class="form-control" 
                        value="<?= htmlspecialchars($user['fullName'] ?? '') ?>" 
                        required
                    >
                    <div class="invalid-feedback">Please enter your full name.</div>
                </div>

                <!-- Phone Number field -->
                <div class="mb-3">
                    <label for="phoneNum" class="form-label">Phone Number:</label>
                    <input 
                        type="text" 
                        id="phoneNum" 
                        name="phoneNum" 
                        class="form-control" 
                        value="<?= htmlspecialchars($user['phoneNum'] ?? '') ?>" 
                        required
                    >
                    <div class="invalid-feedback">Please enter your phone number.</div>
                </div>

                <!-- Password field -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter new password (leave blank to keep current)"
                    >
                    <small class="form-text text-muted">Leave blank to keep the current password.</small>
                </div>

                <!-- Profile Picture upload -->
                <div class="mb-3">
                    <label for="profile-picture" class="form-label">Profile Picture:</label>
                    <input 
                        type="file" 
                        id="profile-picture" 
                        name="profile-picture" 
                        class="form-control" 
                        accept="image/*"
                    >
                    <small class="form-text text-muted">Leave blank to keep the current picture.</small>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>