<?php
session_start();
include 'connection.php';

// Check if class_id is provided in the URL
if (!isset($_GET['class_id']) || empty($_GET['class_id'])) {
    echo "Room ID is required to view comments.";
    exit();
}

$class_id = intval($_GET['class_id']); 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (class_id, user_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$class_id, $user_id, $comment]);
        echo "<div class='alert alert-success'>Comment added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Comment cannot be empty!</div>";
    }
}

// Fetch comments for the specified class
$stmt = $pdo->prepare("SELECT c.comment_text, c.created_at, u.fullName AS user_name, c.admin_response 
                        FROM comments c 
                        JOIN user u ON c.user_id = u.user_id 
                        WHERE c.class_id = ? ORDER BY c.created_at DESC");
$stmt->execute([$class_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <h2>Comments for Room ID: <?= htmlspecialchars($class_id) ?></h2>

    <!-- Add Comment Form -->
    <form method="POST" action="comments.php?class_id=<?= htmlspecialchars($class_id) ?>">
        <div class="mb-3">
            <label for="comment" class="form-label">Leave a Comment:</label>
            <textarea id="comment" name="comment" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!-- Display Comments -->
    <h3 class="mt-4">Comments</h3>
    <?php if (!empty($comments)): ?>
        <ul class="list-group">
            <?php foreach ($comments as $comment): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($comment['user_name']) ?>:</strong> <?= htmlspecialchars($comment['comment_text']) ?>
                    <br><small class="text-muted">Posted on <?= htmlspecialchars($comment['created_at']) ?></small>
                    <?php if (!empty($comment['admin_response'])): ?>
                        <br><strong>Admin Response:</strong> <?= htmlspecialchars($comment['admin_response']) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No comments yet. Be the first to comment!</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>