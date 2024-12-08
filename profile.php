<?php
// Start the session
session_start();
 
// Include the database connection
include 'connection.php';
 
// Check if the user is logged in via session
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit("User not logged in. Please log in to access your profile.");
}
 
// Fetch current user data using the logged-in user's user_id from the session
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$user) {
    exit("User not found or query failed.");
}
 
// Set a default profile picture
$defaultProfilePic = 'Unknown.png';
$currentProfilePic = !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : $defaultProfilePic;
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $email = $_POST['email'] ?? '';
    $fullName = $_POST['fullName'] ?? '';
    $phoneNum = $_POST['phoneNum'] ?? '';
    $password = $_POST['password'] ?? '';

    // Regular Expressions
    $stuEmail_RE = '/^[a-zA-Z0-9._%+-]+@stu\.uob\.edu\.bh$/';
    $instructorEmail_RE = '/^[a-z]+@uob\.edu\.bh$/';
    $fullName_RE = '/^[a-zA-Z\s]{3,50}$/';
    $phoneNum_RE = '/^(00973|\+973)?\s?(([36]\d{7})|(17\d{6}))$/';
    $password_RE = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9_#@%*\\-]{8,50}$/";
 
    // Validation
    $error = '';
    if (!preg_match($stuEmail_RE, $email) && !preg_match($instructorEmail_RE, $email)) {
        $error = "Invalid UoB email format. <br>";
    }
 
    if (!preg_match($fullName_RE, $fullName)) {
        $error .= "Full name must contain only letters and spaces. <br>";
    }
 
    if (!preg_match($phoneNum_RE, $phoneNum)) {
        $error .= "Invalid phone number format. <br>";
    }
 
    if (!empty($password) && !preg_match($password_RE, $password)) {
        $error .= "Password must be 8-50 characters, include uppercase, lowercase, digit, and special character. <br>";
    }
   
    // Prepare for updating the database
    $updateSql = "UPDATE user SET email = ?, fullName = ?, phoneNum = ?";
    $params = [$email, $fullName, $phoneNum];
 
    // Handle password update
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateSql .= ", password = ?";
        $params[] = $hashedPassword;
    }
 
    // Handle profile picture upload
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] === UPLOAD_ERR_OK) {
        // Define constants
        $target_dir = "uploads/";
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5 MB
 
        // Validate file type
        $file_type = $_FILES['profile-picture']['type'];
        if (!in_array($file_type, $allowed_types)) {
            exit("Error: Only JPG, PNG, and GIF files are allowed.");
        }
 
        // Check file size
        if ($_FILES['profile-picture']['size'] > $max_size) {
            exit("Error: File size cannot exceed 5 MB.");
        }
 
        // Sanitize filename
        $original_filename = htmlspecialchars(basename($_FILES["profile-picture"]["name"]), ENT_QUOTES, 'UTF-8');
        $file_ext = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
 
        // Generate a unique name and check if it already exists
        do {
            $unique_name = uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $unique_name;
        } while (file_exists($target_file));
       
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        // Attempt to upload file
        if (move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $target_file)) {
            // Set appropriate permissions
            chmod($target_file, 0644);
            echo "File uploaded successfully.";
            // Update the profile picture path in the database
            $updateSql .= ", profile_picture = ?";
            $params[] = $target_file;
        } else {
            $error_code = $_FILES["profile-picture"]["error"];
            exit("Error uploading file: " . $error_code);
        }
    }
 
    // Complete the SQL query
    $updateSql .= " WHERE user_id = ?";
    $params[] = $userId;
 
    // Execute the update
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute($params);
 
    // Redirect or display a success message
    header("Location:index.php"); // Redirect to the profile page after updating
    exit("Profile updated successfully.");
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; }
        .container { margin-top: 50px; max-width: 600px; }
        .card { padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #ffffff; }
        .profile-pic { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #007bff; }
        h2 { color: #007bff; margin-bottom: 20px; }
        .form-label { font-weight: bold; color: #343a40; }
        .btn-primary { background-color: #007bff; border: none; }
        .btn-primary:hover { background-color: #0056b3; }
        .form-text { color: #6c757d; }
    </style>
</head>
<body>
<?php include("header.php"); ?>
    <div class="container">
        <div class="card">
            <h2 class="text-center">Edit Profile</h2>
            <div class="text-center mb-4">
                <img src="<?= htmlspecialchars($currentProfilePic) ?>" alt="Profile Picture" class="profile-pic">
            </div>
 
            <form id="profile-form" enctype="multipart/form-data" method="POST">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id'] ?? '') ?>">
 
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please enter your email.</div>
                </div>
 
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" class="form-control" value="<?= htmlspecialchars($user['fullName'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please enter your full name.</div>
                </div>
 
                <div class="mb-3">
                    <label for="phoneNum" class="form-label">Phone Number:</label>
                    <input type="text" id="phoneNum" name="phoneNum" class="form-control" value="<?= htmlspecialchars($user['phoneNum'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please enter your phone number.</div>
                </div>
 
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password (leave blank to keep current)">
                    <small class="form-text text-muted">Leave blank to keep the current password.</small>
                </div>
 
                <div class="mb-3">
                    <label for="profile-picture" class="form-label">Profile Picture:</label>
                    <input type="file" id="profile-picture" name="profile-picture" class="form-control" accept="image/*">
                    <small class="form-text text-muted">Leave blank to keep the current picture.</small>
                </div>
 
                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
            </form>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>