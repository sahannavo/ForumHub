<?php
// search.php
// This file handles the search functionality for the forum.
require_once 'php/db.php';

$q = trim($_GET['q'] ?? '');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Search Results - Forum Hub</title>
    <link rel="stylesheet" href="index.php"> <!-- Use your main CSS or copy styles here -->

    <style>
        /* ===== Minimal Styles for Search Results ===== */
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --accent: #4cc9f0;
            --text: #2b2d42;
            --text-light: #8d99ae;
            --bg: #f8f9fa;
            --card-bg: #fff;
            --border-color: #e0e0e0;
            --shadow: 0 4px 6px rgba(0,0,0,0.07);
            --radius: 8px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--bg);
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2 {
            margin-bottom: 30px;
            color: var(--primary);
        }

        .thread-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 22px 20px 18px 20px;
            margin-bottom: 22px;
        }

        .thread-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.25rem;
            color: var(--text);
        }

        .thread-meta {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 10px;
        }

        .thread-content p {
            margin: 0 0 10px 0;
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

        .btn {
            display: inline-block;
            padding: 10px 22px;
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-family: inherit;
            font-size: 1rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 600px) {
            .container {
                padding: 0 8px;
            }
            .thread-card {
                padding: 14px 8px;
            }
        }
        
    </style>

</head>

<body>

    <div class="container">
        <h2>Search Results for "<?php echo htmlspecialchars($q); ?>"</h2>
        <?php
        if ($q !== '') {
            $stmt = mysqli_prepare($conn, "SELECT * FROM threads WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC");
            $like = "%$q%";
            mysqli_stmt_bind_param($stmt, "ss", $like, $like);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="thread-card">';
                    echo '<h3><a href="thread.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h3>';
                    echo '<div class="thread-meta">';
                    echo '<span>' . htmlspecialchars($row['category']) . '</span> | ';
                    echo '<span>' . htmlspecialchars($row['author']) . '</span> | ';
                    echo '<span>' . htmlspecialchars(date('M j, Y', strtotime($row['created_at']))) . '</span>';
                    echo '</div>';
                    echo '<div class="thread-content">';
                    echo '<p>' . nl2br(htmlspecialchars(substr($row['content'], 0, 200))) . '...</p>';
                    // view comment button
                    echo '<a href="thread.php?id=' . $row['id'] . '" class="action-btn"><i class="far fa-comment"></i> View Comments</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No results found.</p>';
            }
            mysqli_stmt_close($stmt);
        } else {
            echo '<p>Please enter a search term.</p>';
        }
        ?>
        <a href="index.php" class="btn btn-primary">Back to Home</a>
    </div>
    
    <?php
    include_once "footer.php";
    ?>


</body>

</html>