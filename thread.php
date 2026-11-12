<?php
session_start();
require_once 'php/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$threadId = intval($_GET['id']);

// Get thread details
$threadSql = "SELECT t.*, u.username, 
             COUNT(DISTINCT tl.id) AS like_count
             FROM threads t
             LEFT JOIN users u ON t.author = u.username
             LEFT JOIN thread_likes tl ON tl.thread_id = t.id
             WHERE t.id = ?
             GROUP BY t.id";
$threadStmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($threadStmt, $threadSql)) {
    mysqli_stmt_bind_param($threadStmt, "i", $threadId);
    mysqli_stmt_execute($threadStmt);
    $threadResult = mysqli_stmt_get_result($threadStmt);
    $thread = mysqli_fetch_assoc($threadResult);
    mysqli_stmt_close($threadStmt);

    if (!$thread) {
        header("Location: index.php");
        exit;
    }
}

// Get comments for this thread
$commentsSql = "SELECT c.* 
               FROM comments c
               LEFT JOIN users u ON c.user_id = u.id
               WHERE c.thread_id = ?
               ORDER BY c.created_at ASC";
$commentsStmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($commentsStmt, $commentsSql)) {
    mysqli_stmt_bind_param($commentsStmt, "i", $threadId);
    mysqli_stmt_execute($commentsStmt);
    $commentsResult = mysqli_stmt_get_result($commentsStmt);
    $comments = mysqli_fetch_all($commentsResult, MYSQLI_ASSOC);
    mysqli_stmt_close($commentsStmt);
}

// Check if user liked this thread
$hasLiked = false;
if (isset($_SESSION['user_id'])) {
    $likeSql = "SELECT * FROM thread_likes WHERE thread_id = ? AND user_id = ?";
    $likeStmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($likeStmt, $likeSql)) {
        mysqli_stmt_bind_param($likeStmt, "ii", $threadId, $_SESSION['user_id']);
        mysqli_stmt_execute($likeStmt);
        $likeResult = mysqli_stmt_get_result($likeStmt);
        $hasLiked = mysqli_num_rows($likeResult) > 0;
        mysqli_stmt_close($likeStmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Hub - Connect & Discuss</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="https://img.icons8.com/color/48/000000/chat--v1.png">
    <link rel="stylesheet" href="css/thread.css">
    <link rel="stylesheet" href="css/index.css">

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

    <main class="container">
        <div class="content">
            <!-- Left Sidebar -->
            <aside class="animate-fadeIn delay-1">
                <div class="sidebar-card">
                    <h2>Categories</h2>
                    <ul>
                        <li><a href="category.php?name=Programming">Programming</a></li>
                        <li><a href="category.php?name=Gaming">Gaming</a></li>
                        <li><a href="category.php?name=Education">Education</a></li>
                        <li><a href="category.php?name=Careers">Careers</a></li>
                        <li><a href="category.php?name=Technology">Technology</a></li>
                        <li><a href="category.php?name=Science">Science</a></li>
                    </ul>
            </aside>

            <!-- Main Content Area -->
            <div class="animate-fadeIn delay-2">
                <div class="thread-card">
                    <h3><?= htmlspecialchars($thread['title']) ?></h3>

                    <div class="thread-meta">
                        <span><i class="far fa-folder"></i> <?= htmlspecialchars($thread['category']) ?></span>
                        <span><i class="far fa-user"></i> <?= htmlspecialchars($thread['username']) ?></span>
                        <span><i class="far fa-clock"></i> <?= htmlspecialchars(date('M j, Y', strtotime($thread['created_at']))) ?></span>
                        <span><i class="far fa-comment"></i> <?= count($comments) ?> comments</span>
                        <span><i class="far fa-thumbs-up"></i> <?= htmlspecialchars($thread['like_count']) ?> likes</span>
                    </div>

                    <div class="thread-content">
                        <p><?= nl2br(htmlspecialchars($thread['content'])) ?></p>
                    </div>

                    <?php if (!empty($thread['tags'])): ?>
                        <div class="thread-tags">
                            <?php
                            $tags = explode(',', $thread['tags']);
                            foreach ($tags as $tag):
                                if (!empty(trim($tag))): ?>
                                    <span class="tag">#<?= htmlspecialchars(trim($tag)) ?></span>
                            <?php endif;
                            endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="thread-actions">
                        <button class="action-btn like-btn <?= $hasLiked ? 'liked' : '' ?>" data-thread-id="<?= $thread['id'] ?>">
                            <i class="far fa-thumbs-up"></i> Like
                        </button>


                        <?php if (isset($_SESSION['username']) && ($_SESSION['username'] === $thread['author'] || ($_SESSION['role'] ?? '') === 'admin')): ?>
                            <!-- edite button -->
                            <?php if (isset($_SESSION['username']) && $_SESSION['username'] === $thread['author']): ?>
                                <a href="edit_thread.php?id=<?= $thread['id'] ?>" class="action-btn"><i class="far fa-edit"></i> Edit</a>
                            <?php endif; ?>
                            <!-- delete button -->
                            <a href="php/delete_thread.php?id=<?= $thread['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this thread?')">
                                <i class="far fa-trash-alt"></i> Delete
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="thread-card" style="margin-top: 30px;">
                    <h3>Comments (<?= count($comments) ?>)</h3>

                    <?php if (isset($_SESSION['username'])): ?>
                        <form action="php/create_comment.php" method="POST" style="margin-bottom: 30px;">
                            <input type="hidden" name="thread_id" value="<?= $thread['id'] ?>">
                            <div class="form-group">
                                <textarea id="content" name="content" class="form-control" placeholder="Write your comment..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>

                            <?php if (isset($_SESSION['comment_error'])): ?>
                                <p style="color: var(--accent); margin-top: 10px;"><?= $_SESSION['comment_error'] ?></p>
                                <?php unset($_SESSION['comment_error']); ?>
                            <?php endif; ?>
                        </form>
                    <?php else: ?>
                        <p><a href="login_signup/login.php" class="btn btn-primary">Login to Comment</a></p>
                    <?php endif; ?>

                    <div class="comments-list">
                        <?php if (empty($comments)): ?>
                            <p>No comments yet. Be the first to comment!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                        <div>
                                            <h4 style="margin: 0; font-size: 1rem;"><?= htmlspecialchars($comment['username']) ?></h4>
                                            <small style="color: var(--gray);"><?= htmlspecialchars(date('M j, Y g:i a', strtotime($comment['created_at']))) ?></small>
                                        </div>
                                    </div>
                                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>

                                    <?php if (isset($_SESSION['username']) && ($_SESSION['username'] === $comment['username'] || ($_SESSION['role'] ?? '') === 'admin')): ?>
                                        <div style="margin-top: 10px;">
                                            <!-- edite comment -->
                                            <?php if (isset($_SESSION['username']) && ($_SESSION['username'] === $comment['username'])): ?>
                                                <!-- this can see only for the user -->
                                                <a href="edit_comment.php?id=<?= $comment['id'] ?>" class="action-btn" style="font-size: 0.8rem;">
                                                    <i class="far fa-edit"></i> Edit
                                                </a>
                                            <!-- <?php endif; ?> -->

                                            <!-- delete comment -->
                                            <!-- this part can see to uther , adimn and uther of the thread -->
                                            <?php
                                            // Show delete button if user is admin, comment author, or thread author
                                            if (
                                                (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                                                (isset($_SESSION['username']) && $_SESSION['username'] === $comment['username']) ||
                                                (isset($_SESSION['username']) && $_SESSION['username'] === $thread['author'])
                                            ):
                                            ?>
                                                <a href="php/delete_comment.php?id=<?= $comment['id'] ?>&thread_id=<?= $thread['id'] ?>"
                                                    class="action-btn delete-btn"
                                                    style="font-size: 0.8rem;"
                                                    onclick="return confirm('Are you sure you want to delete this comment?')">
                                                    <i class="far fa-trash-alt"></i> Delete
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <?php
            include_once "Right_Sidebar.php";
            // Include the right sidebar code here
            ?>
        </div>
    </main>
    <?php
    include_once "footer.php";
    ?>
    <script>
        // Like button functionality
        const likeBtn = document.querySelector('.like-btn');
        if (likeBtn) {
            likeBtn.addEventListener('click', function() {
                const threadId = this.getAttribute('data-thread-id');
                const isLiked = this.classList.contains('liked');

                fetch(`php/${isLiked ? 'unlike_thread' : 'like_thread'}.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `thread_id=${threadId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.toggle('liked');
                            const likeCountElement = this.closest('.thread-card').querySelector('.fa-thumbs-up').parentElement;
                            let currentCount = parseInt(likeCountElement.textContent.trim().split(' ')[0]);
                            likeCountElement.textContent = isLiked ? `${currentCount - 1} likes` : `${currentCount + 1} likes`;
                        } else if (data.error === 'login_required') {
                            window.location.href = 'login_signup/login.php';
                        }
                    });
            });
        }
    </script>
</body>

</html>