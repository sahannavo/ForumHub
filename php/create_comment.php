<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_signup/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thread_id'], $_POST['content'])) {
    $threadId = intval($_POST['thread_id']);
    $content = trim($_POST['content']);
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    if (empty($content)) {
        $_SESSION['comment_error'] = "Comment content cannot be empty";
        header("Location: ../thread.php?id=$threadId");
        exit;
    }

    $sql = "INSERT INTO comments (thread_id, user_id, username, content) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "iiss", $threadId, $userId, $username, $content);
        mysqli_stmt_execute($stmt);

        // Update comment count in threads table
        $updateSql = "UPDATE threads SET comment_count = comment_count + 1 WHERE id = ?";
        $updateStmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($updateStmt, $updateSql)) {
            mysqli_stmt_bind_param($updateStmt, "i", $threadId);
            mysqli_stmt_execute($updateStmt);
        }
        mysqli_stmt_close($updateStmt);

        header("Location: ../thread.php?id=$threadId");
    } else {
        $_SESSION['comment_error'] = "Database error. Please try again.";
        header("Location: ../thread.php?id=$threadId");
    }
    mysqli_stmt_close($stmt);
} else {
    header("Location: ../index.php");
}
