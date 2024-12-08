<?php
session_start();
include 'connection.php';

// Ensure only admins can access
if ($_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add/Edit/Delete actions for the "class_type" table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entity']) && $_POST['entity'] === 'class_type') {
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
    header("Location: room_management.php#room_management");
    exit();
}

// Handle Add/Edit/Delete actions for the "classes" table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entity']) && $_POST['entity'] === 'classes') {
    $action = $_POST['action'];
    $classId = $_POST['class_id'] ?? null;

    if ($action === 'add') {
        $className = $_POST['class_name'];
        $classTypeId = $_POST['class_type_id'];

        $stmt = $pdo->prepare("INSERT INTO classes (class_num, class_type_id) VALUES (?, ?)");
        $stmt->execute([$className, $classTypeId]);
    } elseif ($action === 'edit') {
        $className = $_POST['class_name'];
        $classTypeId = $_POST['class_type_id'];

        $stmt = $pdo->prepare("UPDATE classes SET class_num = ?, class_type_id = ? WHERE class_id = ?");
        $stmt->execute([$className, $classTypeId, $classId]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM classes WHERE class_id = ?");
        $stmt->execute([$classId]);
    }
    header("Location: room_management.php#class_management");
    exit();
}

// Fetch data for editing "class_type"
$roomToEdit = null;
if (isset($_GET['edit_room_id'])) {
    $roomId = $_GET['edit_room_id'];
    $stmt = $pdo->prepare("SELECT * FROM class_type WHERE class_type_id = ?");
    $stmt->execute([$roomId]);
    $roomToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch data for editing "classes"
$classToEdit = null;
if (isset($_GET['edit_class_id'])) {
    $classId = $_GET['edit_class_id'];
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id = ?");
    $stmt->execute([$classId]);
    $classToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-danger {
            background-color: #be123c !important;
        }

        .btn-secondary {
            background-color: #f97316 !important;
        }

        .btn-primary {
            background-color: green !important;
        }
    </style>
</head>

<body>
    <?php include("header.php"); ?>

    <div class="container mt-4">
        <h2 id="room_management">Room Type Management</h2>
        <!-- Room Management Form -->
        <form method="POST" action="room_management.php#room_management" class="mb-4">
            <input type="hidden" name="entity" value="class_type">
            <input type="hidden" name="action" value="<?= $roomToEdit ? 'edit' : 'add' ?>">
            <?php if ($roomToEdit): ?>
                <input type="hidden" name="room_id" value="<?= $roomToEdit['class_type_id'] ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="type_name" class="form-control" placeholder="Room Type"
                        value="<?= $roomToEdit['type_name'] ?? '' ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="capacity" class="form-control" placeholder="Capacity"
                        value="<?= $roomToEdit['capacity'] ?? '' ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="equipments" class="form-control" placeholder="Equipments"
                        value="<?= $roomToEdit['equipments'] ?? '' ?>" required>
                </div>
                <div class="col-md-3">
                    <button type="submit"
                        class="btn btn-primary"><?= $roomToEdit ? 'Update Room' : 'Add Room' ?></button>
                </div>
            </div>
        </form>

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
                if (empty($rooms)) {
                    echo "<tr><td colspan='4'>No room types available.</td></tr>";
                }
                foreach ($rooms as $room) {
                    echo "<tr>
                            <td>{$room['type_name']}</td>
                            <td>{$room['capacity']}</td>
                            <td>{$room['equipments']}</td>
                            <td>
                                <a href='room_management.php?edit_room_id={$room['class_type_id']}#room_management' class='btn btn-secondary'>Edit</a>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='entity' value='class_type'>
                                    <input type='hidden' name='action' value='delete'>
                                    <input type='hidden' name='room_id' value='{$room['class_type_id']}'>
                                    <button type='submit' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>

        <h2 id="class_management">Class Management</h2>
        <!-- Class Management Form -->
        <form method="POST" action="room_management.php#class_management" class="mb-4">
            <input type="hidden" name="entity" value="classes">
            <input type="hidden" name="action" value="<?= $classToEdit ? 'edit' : 'add' ?>">
            <?php if ($classToEdit): ?>
                <input type="hidden" name="class_id" value="<?= $classToEdit['class_id'] ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="class_name" class="form-control" placeholder="Class Name"
                        value="<?= $classToEdit['class_num'] ?? '' ?>" required>
                </div>
                <div class="col-md-3">
                    <select name="class_type_id" class="form-control" required>
                        <option value="">Select Room Type</option>
                        <?php
                        $roomTypes = $pdo->query("SELECT * FROM class_type")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($roomTypes as $roomType) {
                            $selected = $classToEdit && $classToEdit['class_type_id'] == $roomType['class_type_id'] ? 'selected' : '';
                            echo "<option value='{$roomType['class_type_id']}' $selected>{$roomType['type_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit"
                        class="btn btn-primary"><?= $classToEdit ? 'Update Class' : 'Add Class' ?></button>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Room Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $classes = $pdo->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC);
                if (empty($classes)) {
                    echo "<tr><td colspan='3'>No classes available.</td></tr>";
                }
                foreach ($classes as $class) {
                    $roomType = $pdo->prepare("SELECT type_name FROM class_type WHERE class_type_id = ?");
                    $roomType->execute([$class['class_type_id']]);
                    $roomType = $roomType->fetch(PDO::FETCH_ASSOC);
                    echo "<tr>
                            <td>{$class['class_num']}</td>
                            <td>{$roomType['type_name']}</td>
                            <td>
                                <a href='room_management.php?edit_class_id={$class['class_id']}#class_management' class='btn btn-secondary'>Edit</a>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='entity' value='classes'>
                                    <input type='hidden' name='action' value='delete'>
                                    <input type='hidden' name='class_id' value='{$class['class_id']}'>
                                    <button type='submit' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
