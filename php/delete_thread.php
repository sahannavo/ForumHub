<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_signup/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $threadId = intval($_GET['id']);

    // Check if user is author or admin
    $sql = "SELECT author FROM threads WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $threadId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $thread = mysqli_fetch_assoc($result);

        if ($thread && ($_SESSION['username'] === $thread['author'] || $_SESSION['role'] === 'admin')) {
            // Delete thread (cascading deletes should handle comments and likes)
            $deleteSql = "DELETE FROM threads WHERE id = ?";
            $deleteStmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($deleteStmt, $deleteSql)) {
                mysqli_stmt_bind_param($deleteStmt, "i", $threadId);
                mysqli_stmt_execute($deleteStmt);
            }
            mysqli_stmt_close($deleteStmt);

            $_SESSION['success_message'] = "Thread deleted successfully";
            header("Location: ../index.php");
        } else {
            $_SESSION['error_message'] = "You don't have permission to delete this thread";
            header("Location: ../thread.php?id=$threadId");
        }
    }
    mysqli_stmt_close($stmt);
} else {
    header("Location: ../index.php");
}
