<?php
session_start();
require_once 'db.php'; // $conn must be defined in db.php

// Get the current user's username for the author field
$author = $_SESSION['username'] ?? 'guest';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "This page can only be accessed via form submission.";
    exit;
}

$title = trim(mysqli_real_escape_string($conn, $_POST['title'] ?? ''));
$category = trim(mysqli_real_escape_string($conn, $_POST['category'] ?? ''));
$content = trim(mysqli_real_escape_string($conn, $_POST['content'] ?? ''));
$created_at = date('Y-m-d H:i:s');

if (empty($title) || empty($category) || empty($content)) {
    echo "Title, category, and content are required.";
    exit;
}

$sql = "INSERT INTO threads (title, category, content, author, created_at) VALUES ('$title', '$category', '$content', '$author', '$created_at')";
if (mysqli_query($conn, $sql)) {
    header("Location: ../index.php?success=1");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>