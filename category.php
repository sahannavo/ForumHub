<?php
// category.php - This handles category-based thread searches
session_start();
require_once 'php/db.php';

// Get category from URL parameter and sanitize it
$category = isset($_GET['name']) ? htmlspecialchars(trim($_GET['name'])) : '';
$validCategories = ['Programming', 'Gaming', 'Education', 'Careers', 'Technology', 'Science'];

// Validate category
if (!in_array($category, $validCategories)) {
    header("Location: index.php");
    exit;
}

// Check if user is logged in (for like functionality)
$userId = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category) ?> - Forum Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="https://img.icons8.com/color/48/000000/chat--v1.png">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --accent: #4cc9f0;
            --text: #2b2d42;
            --text-light: #8d99ae;
            --bg: #f8f9fa;
            --card-bg: #ffffff;
            --border: #e0e0e0;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 8px 15px rgba(0, 0, 0, 0.1);
            --radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background-color: var(--bg);
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        header {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow);
            padding: 1rem 0;
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .logo img {
            margin-right: 10px;
            width: 36px;
        }

        a {
            text-decoration: none;
        }

        .thread-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .thread-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .thread-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .thread-content {
            margin-bottom: 1rem;
            line-height: 1.7;
        }

        .thread-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .tag {
            background: rgba(76, 201, 240, 0.1);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .action-btn {
            display: inline-block;
            padding: 7px 15px;
            background: rgba(67,97,238,0.08);
            color: var(--primary);
            border-radius: var(--radius);
            font-size: 0.95rem;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .action-btn:hover {
            background: rgba(67,97,238,0.18);
        }
        .action-btn i {
            margin-right: 0.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <header>
        <div class="container header-content">
            <a href="index.php" class="logo">
                <img src="https://img.icons8.com/color/48/000000/chat--v1.png" alt="Forum Hub Logo">
                <span>Forum<span>Hub</span></span>
            </a>

            <nav>
                <ul>
                    <li><a href="index.php">Go back</a></li>
                </ul>

            </nav>
        </div>
    </header>

    <main class="container" style="min-height: 100vh;">
        <div class="content">
            <div class="animate-fadeIn delay-2">
                <h2>Threads in <?= htmlspecialchars($category) ?></h2>

                <?php
                // Fetch threads from the selected category
                $sql = "SELECT t.*, u.username, 
                        COUNT(DISTINCT c.id) AS comment_count,
                        COUNT(DISTINCT tl.id) AS like_count
                        FROM threads t
                        LEFT JOIN users u ON t.author = u.username
                        LEFT JOIN comments c ON c.thread_id = t.id
                        LEFT JOIN thread_likes tl ON tl.thread_id = t.id
                        WHERE t.category = ?
                        GROUP BY t.id
                        ORDER BY t.created_at DESC";

                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $category);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Check if current user liked this thread
                            $hasLiked = false;
                            if ($userId) {
                                $likeSql = "SELECT * FROM thread_likes WHERE thread_id = ? AND user_id = ?";
                                $likeStmt = mysqli_stmt_init($conn);
                                if (mysqli_stmt_prepare($likeStmt, $likeSql)) {
                                    mysqli_stmt_bind_param($likeStmt, "ii", $row['id'], $userId);
                                    mysqli_stmt_execute($likeStmt);
                                    $likeResult = mysqli_stmt_get_result($likeStmt);
                                    $hasLiked = mysqli_num_rows($likeResult) > 0;
                                    mysqli_stmt_close($likeStmt);
                                }
                            }

                            // Display thread card
                            echo '<div class="thread-card">';
                            echo '<h3><a href="thread.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h3>';

                            echo '<div class="thread-meta">';
                            echo '<span><i class="far fa-folder"></i> ' . htmlspecialchars($row['category']) . '</span>';
                            echo '<span><i class="far fa-user"></i> ' . htmlspecialchars($row['username'] ?? 'Anonymous') . '</span>';
                            echo '<span><i class="far fa-clock"></i> ' . htmlspecialchars(date('M j, Y', strtotime($row['created_at']))) . '</span>';
                            echo '<span><i class="far fa-comment"></i> ' . $row['comment_count'] . ' comments</span>';
                            echo '<span><i class="far fa-thumbs-up"></i> ' . $row['like_count'] . ' likes</span>';
                            echo '</div>';

                            echo '<div class="thread-content">';
                            echo '<p>' . nl2br(htmlspecialchars(substr($row['content'], 0, 200))) . '...</p>';
                            echo '</div>';

                            if (!empty($row['tags'])) {
                                echo '<div class="thread-tags">';
                                $tags = explode(',', $row['tags']);
                                foreach ($tags as $tag) {
                                    if (!empty(trim($tag))) {
                                        echo '<span class="tag">#' . htmlspecialchars(trim($tag)) . '</span>';
                                    }
                                }
                                echo '</div>';
                            }


                            // view thread button
                            echo '<a href="thread.php?id=' . $row['id'] . '" class="action-btn"> View Thread</a>';
                            echo '</div>';

                            echo '</div>'; // Close thread-card
                        }
                    } else {
                        echo '<div class="thread-card">';
                        echo '<h3>No threads found in this category</h3>';
                        echo '<p>Be the first to start a discussion in ' . htmlspecialchars($category) . '!</p>';
                        if (!isset($_SESSION['username'])) {
                            echo '<p><a href="login_signup/login.php" class="btn btn-primary">Login to Post</a></p>';
                        }
                        echo '</div>';
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    echo '<div class="thread-card">';
                    echo '<h3>Error loading threads</h3>';
                    echo '<p>Please try again later.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>


    <?php
    include_once "footer.php";
    ?>

</body>

</html>