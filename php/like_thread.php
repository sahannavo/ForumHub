<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'login_required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thread_id'])) {
    $threadId = intval($_POST['thread_id']);
    $userId = $_SESSION['user_id'];
    
    // Check if already liked
    $checkSql = "SELECT 1 FROM thread_likes WHERE thread_id = ? AND user_id = ?";
    $checkStmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($checkStmt, $checkSql)) {
        mysqli_stmt_bind_param($checkStmt, "ii", $threadId, $userId);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            echo json_encode(['success' => false, 'error' => 'already_liked']);
            mysqli_stmt_close($checkStmt);
            exit;
        }
        mysqli_stmt_close($checkStmt);
    }
    
    // Add like
    $insertSql = "INSERT INTO thread_likes (thread_id, user_id) VALUES (?, ?)";
    $insertStmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($insertStmt, $insertSql)) {
        mysqli_stmt_bind_param($insertStmt, "ii", $threadId, $userId);
        $success = mysqli_stmt_execute($insertStmt);
        
        if ($success) {
            // Get updated like count
            $countSql = "SELECT COUNT(*) as like_count FROM thread_likes WHERE thread_id = ?";
            $countStmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($countStmt, $countSql)) {
                mysqli_stmt_bind_param($countStmt, "i", $threadId);
                mysqli_stmt_execute($countStmt);
                $result = mysqli_stmt_get_result($countStmt);
                $countData = mysqli_fetch_assoc($result);
                
                echo json_encode([
                    'success' => true,
                    'new_count' => $countData['like_count']
                ]);
            } else {
                echo json_encode(['success' => true]);
            }
            mysqli_stmt_close($countStmt);
        } else {
            echo json_encode(['success' => false, 'error' => 'database_error']);
        }
        mysqli_stmt_close($insertStmt);
    } else {
        echo json_encode(['success' => false, 'error' => 'database_error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'invalid_request']);
}