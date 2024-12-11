<?php
session_start();
include 'connection.php';

// Ensure only admins can access
if ($_SESSION['user_type'] !== 'admin') {
    header("Location: home.php");
    exit();
}

// Fetch all users from the "user" table
$stmt = $pdo->query("SELECT * FROM user");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user details for editing
$userToEdit = null;
if (isset($_GET['edit_user_id'])) {
    $userId = $_GET['edit_user_id'];
    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);
    $userToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle the Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $userType = $_POST['user_type'];
    $phoneNum = $_POST['phoneNum'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    $stmt = $pdo->prepare("UPDATE user SET fullName = ?, email = ?, user_type = ?, phoneNum = ?, password = ? WHERE user_id = ?");
    $stmt->execute([$fullName, $email, $userType, $phoneNum, $password, $userId]);

    header("Location: user_management.php");
    exit();
}

// Handle the Add form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name']) && isset($_POST['email']) && isset($_POST['user_type'])) {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $userType = $_POST['user_type'];
    $phoneNum = $_POST['phoneNum'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    // Regular Expressions for validation
    $stuEmail_RE = '/^[a-zA-Z0-9._%+-]+@stu\.uob\.edu\.bh$/';
    $instructorEmail_RE = '/^[a-z]+@uob\.edu\.bh$/';
    $fullName_RE = '/^[a-zA-Z\s]{3,50}$/';
    $phoneNum_RE = '/^(00973|\+973)?\s?(([36]\d{7})|(17\d{6}))$/';
    $password_RE = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])[A-Za-z0-9_#@%*\\-]{8,50}$/";

    $error = '';

    // Validation checks
    if (!preg_match($stuEmail_RE, $email) && !preg_match($instructorEmail_RE, $email)) {
        $error .= "Invalid UoB email format. <br>";
    }

    if (!preg_match($fullName_RE, $fullName)) {
        $error .= "Full name must contain only letters and spaces. <br>";
    }

    if (!preg_match($phoneNum_RE, $phoneNum)) {
        $error .= "Invalid phone number format. <br>";
    }

    if (!preg_match($password_RE, $_POST['password'])) {
        $error .= "Password must be 8-50 characters, include uppercase, lowercase, digit, and special character. <br>";
    }

    // Prevent duplicate emails
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $error .= "This email is already registered! <br>";
    }

    if ($error) {
        echo "<div class='alert alert-danger'>$error</div>";
    } else {
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO user (fullName, email, user_type, phoneNum, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $userType, $phoneNum, $password]);

        header("Location: user_management.php");
        exit();
    }
}

// Handle the Delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // Delete user from the database
    $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->execute([$userId]);

    header("Location: user_management.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

</head>


<body>
    <?php include("header.php"); ?>

    <div class="container mt-4">
        <h2>User Management</h2>

        <!-- Add New User Form -->
        <h3>Add New User</h3>
                <form method="POST" action="user_management.php">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phoneNum" class="form-label">Phone Number</label>
                    <input type="text" id="phoneNum" name="phoneNum" class="form-control" value="<?= isset($_POST['phoneNum']) ? htmlspecialchars($_POST['phoneNum']) : '' ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="user_type" class="form-label">User Type</label>
                    <select id="user_type" name="user_type" class="form-control" required>
                        <option value="admin" <?= isset($_POST['user_type']) && $_POST['user_type'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= isset($_POST['user_type']) && $_POST['user_type'] === 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Add User</button>
        </form>


        <!-- User Edit Form -->
        <?php if ($userToEdit): ?>
            <h3>Edit User</h3>
            <form method="POST" action="user_management.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="user_id" value="<?= $userToEdit['user_id'] ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : htmlspecialchars($userToEdit['fullName']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($userToEdit['email']) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phoneNum" class="form-label">Phone Number</label>
                    <input type="text" id="phoneNum" name="phoneNum" class="form-control" value="<?= isset($_POST['phoneNum']) ? htmlspecialchars($_POST['phoneNum']) : htmlspecialchars($userToEdit['phoneNum']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="user_type" class="form-label">User Type</label>
                    <select id="user_type" name="user_type" class="form-control" required>
                        <option value="admin" <?= isset($_POST['user_type']) && $_POST['user_type'] === 'admin' ? 'selected' : ($userToEdit['user_type'] === 'admin' ? 'selected' : '') ?>>Admin</option>
                        <option value="user" <?= isset($_POST['user_type']) && $_POST['user_type'] === 'user' ? 'selected' : ($userToEdit['user_type'] === 'user' ? 'selected' : '') ?>>User</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-warning">Update User</button>
        </form>

        <?php endif; ?>

        <!-- User List -->
        <h3>User List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>User Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['fullName']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phoneNum']) ?></td>
                        <td><?= htmlspecialchars($user['user_type']) ?></td>
                        <td>
                            <a href="?edit_user_id=<?= $user['user_id'] ?>" class="btn btn-warning">Edit</a>
                            <form method="POST" action="user_management.php" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
