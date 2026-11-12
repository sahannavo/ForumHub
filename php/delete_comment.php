<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_signup/login.php");
    exit;
}

if (isset($_GET['id'], $_GET['thread_id'])) {
    $commentId = intval($_GET['id']);
    $threadId = intval($_GET['thread_id']);

    // Check if user is comment author or admin
    $sql = "SELECT user_id, username FROM comments WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $commentId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $comment = mysqli_fetch_assoc($result);

        if ($comment && ($_SESSION['user_id'] === $comment['user_id'] || $_SESSION['role'] === 'admin')) {
            // Delete comment
            $deleteSql = "DELETE FROM comments WHERE id = ?";
            $deleteStmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($deleteStmt, $deleteSql)) {
                mysqli_stmt_bind_param($deleteStmt, "i", $commentId);
                mysqli_stmt_execute($deleteStmt);

                // Update comment count in threads table
                $updateSql = "UPDATE threads SET comment_count = comment_count - 1 WHERE id = ?";
                $updateStmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($updateStmt, $updateSql)) {
                    mysqli_stmt_bind_param($updateStmt, "i", $threadId);
                    mysqli_stmt_execute($updateStmt);
                }
                mysqli_stmt_close($updateStmt);

                $_SESSION['success_message'] = "Comment deleted successfully";
            }
            mysqli_stmt_close($deleteStmt);
        } else {
            $_SESSION['error_message'] = "You don't have permission to delete this comment";
        }
    }
    mysqli_stmt_close($stmt);
    header("Location: ../thread.php?id=$threadId");
} else {
    header("Location: ../index.php");
}
