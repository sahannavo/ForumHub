<?php
session_start();
require_once 'php/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$threadId = intval($_GET['id']);

// Fetch thread
$sql = "SELECT * FROM threads WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $threadId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$thread = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$thread) {
    echo "Thread not found.";
    exit;
}

// Only author or admin can edit
if (!isset($_SESSION['username']) || ($_SESSION['username'] !== $thread['author'] && ($_SESSION['role'] ?? '') !== 'admin')) {
    echo "You are not authorized to edit this thread.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $content = trim($_POST['content']);

    if ($title && $category && $content) {
        $updateSql = "UPDATE threads SET title = ?, category = ?, content = ? WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "sssi", $title, $category, $content, $threadId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);

        header("Location: thread.php?id=" . $threadId);
        exit;
    } else {
        $error = "All fields are required.";
    }
}

// For category select
$categories = ["Programming", "Gaming", "Education", "Careers", "Technology", "Science"];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Thread</title>
    <link rel="stylesheet" href="css/edit_thread.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    

</head>

<body>
    <div class="thread-form">
        <h2>Edit Thread</h2>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="title">Thread Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($thread['title']) ?>" required class="form-control">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required class="form-control">
                    <option value="" disabled>Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat ?>" <?= ($thread['category'] == $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="content">Thread Content</label>
                <textarea id="content" name="content" required class="form-control"><?= htmlspecialchars($thread['content']) ?></textarea>
            </div>

            <button type="submit" class="btn">Update Thread</button>
            <a href="thread.php?id=<?= $threadId ?>" class="btn" style="background:var(--accent);color:#fff;">Cancel</a>
        </form>
    </div>
</body>

</html>