<?php
session_start();
include 'connection.php';


// Fetch current user data using the logged-in user's ID
$userId = $_SESSION['userId'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user data is found
if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Profile</h2>
        <form id="profile-form" enctype="multipart/form-data" action="update_profile.php" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="userId" value="<?= $user['id'] ?>">

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                <div class="invalid-feedback">Please enter your email.</div>
            </div>

            <div class="mb-3">
                <label for="fullName" class="form-label">Full Name:</label>
                <input type="text" id="fullName" name="fullName" class="form-control" value="<?= htmlspecialchars($user['fullName']) ?>" required>
                <div class="invalid-feedback">Please enter your full name.</div>
            </div>

            <div class="mb-3">
                <label for="phoneNum" class="form-label">Phone Number:</label>
                <input type="text" id="phoneNum" name="phoneNum" class="form-control" value="<?= htmlspecialchars($user['phoneNum']) ?>" required>
                <div class="invalid-feedback">Please enter your phone number.</div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control">
                <small class="form-text text-muted">Leave blank to keep the current password.</small>
            </div>

            <div class="mb-3">
                <label for="user_type" class="form-label">User Type:</label>
                <select id="user_type" name="user_type" class="form-select" required>
                    <option value="" disabled>Select user type</option>
                    <option value="admin" <?= $user['user_type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= $user['user_type'] == 'user' ? 'selected' : '' ?>>User</option>
                </select>
                <div class="invalid-feedback">Please select user type.</div>
            </div>

            <div class="mb-3">
                <label for="profile-picture" class="form-label">Profile Picture:</label>
                <input type="file" id="profile-picture" name="profile-picture" class="form-control" accept="image/*">
                <small class="form-text text-muted">Leave blank to keep the current picture.</small>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>