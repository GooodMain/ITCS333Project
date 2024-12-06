<?php
session_start();
include 'connection.php';

// Ensure only admins can access
if ($_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add/Edit/Delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $roomId = $_POST['room_id'] ?? null;

    if ($action === 'add') {
        $typeName = $_POST['type_name'];
        $capacity = $_POST['capacity'];
        $equipments = $_POST['equipments'];

        $stmt = $pdo->prepare("INSERT INTO class_type (type_name, capacity, equipments) VALUES (?, ?, ?)");
        $stmt->execute([$typeName, $capacity, $equipments]);
    } elseif ($action === 'edit') {
        $typeName = $_POST['type_name'];
        $capacity = $_POST['capacity'];
        $equipments = $_POST['equipments'];

        $stmt = $pdo->prepare("UPDATE class_type SET type_name = ?, capacity = ?, equipments = ? WHERE class_type_id = ?");
        $stmt->execute([$typeName, $capacity, $equipments, $roomId]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM class_type WHERE class_type_id = ?");
        $stmt->execute([$roomId]);
    }
    header("Location: room_management.php"); // Refresh the page after action
    exit();
}

// Fetch the room data for editing
$roomToEdit = null;
if (isset($_GET['edit_room_id'])) {
    $roomId = $_GET['edit_room_id'];
    $stmt = $pdo->prepare("SELECT * FROM class_type WHERE class_type_id = ?");
    $stmt->execute([$roomId]);
    $roomToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- Style sheet Link-->
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include("header.php"); ?>

<div class="container mt-4">
    <h2>Room Management</h2>

    <!-- Add/Edit Room Form -->
    <form method="POST" class="mb-4">
        <input type="hidden" name="action" value="<?= $roomToEdit ? 'edit' : 'add' ?>">
        <?php if ($roomToEdit): ?>
            <input type="hidden" name="room_id" value="<?= $roomToEdit['class_type_id'] ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <input type="text" name="type_name" class="form-control" placeholder="Room Type" value="<?= $roomToEdit['type_name'] ?? '' ?>" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="capacity" class="form-control" placeholder="Capacity" value="<?= $roomToEdit['capacity'] ?? '' ?>" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="equipments" class="form-control" placeholder="Equipments" value="<?= $roomToEdit['equipments'] ?? '' ?>" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn brand z-depth-0"><?= $roomToEdit ? 'Update Room' : 'Add Room' ?></button>
            </div>
        </div>
    </form>

    <!-- Room List -->
    <table class="table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Capacity</th>
                <th>Equipments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php
    $rooms = $pdo->query("SELECT * FROM class_type")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rooms as $room) {
        $class_type_id = $room['class_type_id']; // Assuming 'class_type_id' is available

        echo "<tr>
                <td>{$room['type_name']}</td>
                <td>{$room['capacity']}</td>
                <td>{$room['equipments']}</td>
                <td>
                    <!-- Edit Button -->
                    <a href='room_management.php?edit_room_id={$class_type_id}' class='btn brand z-depth-0'>Edit</a>
                    
                    <!-- Delete Button with Confirmation -->
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='action' value='delete'>
                        <input type='hidden' name='room_id' value='{$class_type_id}'>
                        <button type='submit' class='btn brand z-depth-0' onclick='return confirm(\"Are you sure you want to delete this room?\")'>Delete</button>
                    </form>
                </td>
            </tr>";
    }
    ?>
</tbody>

</div>

<?php include("footer.php"); ?>

</body>
</html>
