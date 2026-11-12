<?php
session_start();
require_once 'php/db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            if ($row['role'] == 'admin') {
                $role = 'admin';
                // echo $role;
            } else if ($row['role'] == 'user') {
                $user = $row['username'];
                $_SESSION['user'] = $user;
                $role = 'user';
                // echo $role;
            } else {
                $role = 'Guest';
                // echo $role;
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        $role = 'Guest';
        // echo $role;
    }
} else {
    $role = 'Guest';
    // echo $role;
}





// Function to check if user liked a thread
function hasUserLikedThread($conn, $threadId, $userId)
{
    if (!$userId) return false;
    $sql = "SELECT * FROM thread_likes WHERE thread_id = ? AND user_id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $threadId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }
    return false;
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
    <link rel="stylesheet" href="css/index.css">

</head>

<body>

    <div class="bg"></div>
    <!-- Fixed Header with Navbar -->
    <header>
        <div class="container header-content">
            <!-- search bar -->
            <form class="search-bar" action="search.php" method="get" style="margin-right: 20px;">
                <input type="text" name="q" class="search-input" placeholder="Search discussions..." required>
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>
            <!-- logo -->
            <a href="index.php" class="logo">
                <img src="https://img.icons8.com/color/48/000000/chat--v1.png" alt="Forum Hub Logo">
                <span>Forum<span>Hub</span></span>
            </a>

            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if ($role === 'admin'): ?>
                        <li><a>Admin</a></li>
                    <?php endif; ?>
                    <?php if ($role === 'user'): ?>
                        <li><a>user</a></li>
                    <?php endif; ?>
                    <?php if ($role == 'user'): ?>
                        <li><a href="login_signup/logout.php">Logout</a></li>

                    <?php elseif ($role == 'Guest'): ?>
                        <li><a href="login_signup/login.php">Login</a></li>
                        <li><a href="login_signup/signup.php">Register</a></li>
                    <?php elseif ($role == 'admin'): ?>
                        <li><a href="login_signup/logout.php">Logout</a></li>

                    <?php endif; ?>
                    <li><a href="About_us.php">About</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="content">
            <!-- Left Sidebar -->
            <!-- categories bar -->
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
                </div>


            </aside>

            <!-- Main Content Area -->
            <div class="animate-fadeIn delay-2">
                <!-- threads creating forum -->
                <div class="thread-form">
                    <h2>Create New Thread</h2>
                    <form id="createThreadForm" action="php/create_thread.php" method="POST">
                        <div class="form-group">
                            <label for="title">Thread Title</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="What's your question?" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="" disabled selected>Select a category</option>
                                <option value="Programming">Programming</option>
                                <option value="Gaming">Gaming</option>
                                <option value="Education">Education</option>
                                <option value="Careers">Careers</option>
                                <option value="Technology">Technology</option>
                                <option value="Science">Science</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="content">Thread Content</label>
                            <textarea id="content" name="content" class="form-control" placeholder="Describe your question in detail..." required></textarea>
                        </div>
                        <?php if ($role == 'user'): ?>
                            <button onclick="checkwho()" type="submit" class="btn btn-primary">Post</button>
                        <?php elseif ($role == 'admin'): ?>
                            <button onclick="checkwho()" type="submit" class="btn btn-primary">Post</button>
                        <?php elseif ($role == 'Guest'): ?>
                            <button onclick="alert('Please login to create a thread.')" type="button" class="btn btn-primary">Post</button>
                        <?php endif; ?>

                    </form>

                </div>


                <h2 style="margin-bottom: 20px;">Recent Discussions</h2>
                <!-- displaying threads -->
                <?php
                $sql = "SELECT t.*, u.username, 
                        COUNT(DISTINCT c.id) AS comment_count,
                        COUNT(DISTINCT tl.id) AS like_count
                        FROM threads t
                        LEFT JOIN users u ON t.author = u.username
                        LEFT JOIN comments c ON c.thread_id = t.id
                        LEFT JOIN thread_likes tl ON tl.thread_id = t.id
                        GROUP BY t.id
                        ORDER BY t.created_at DESC LIMIT 10";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $hasLiked = hasUserLikedThread($conn, $row['id'], $_SESSION['user_id'] ?? null);

                        echo '<div class="thread-card animate-fadeIn">';
                        // Thread title
                        echo '<h3 class="open-thread-modal" data-thread-id="' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</h3>';

                        echo '<div class="thread-meta">';
                        echo '<span><i class="far fa-folder"></i> ' . htmlspecialchars($row['category']) . '</span>';
                        echo '<span><i class="far fa-user"></i> ' . htmlspecialchars($row['username'] ?? 'Anonymous') . '</span>';
                        echo '<span><i class="far fa-clock"></i> ' . htmlspecialchars(date('M j, Y', strtotime($row['created_at']))) . '</span>';
                        echo '<span><i class="far fa-comment"></i> ' . htmlspecialchars($row['comment_count']) . ' comments</span>';
                        echo '<span><i class="far fa-thumbs-up"></i> ' . htmlspecialchars($row['like_count']) . ' likes</span>';
                        echo '</div>';

                        echo '<div class="thread-content">';
                        echo '<p>' . nl2br(htmlspecialchars(substr($row['content'], 0, 200))) . '...</p>';
                        echo '</div>';


                        // Actions
                        echo '<div class="thread-actions">';
                        echo '<button class="action-btn like-btn ' . ($hasLiked ? 'liked' : '') . '" data-thread-id="' . $row['id'] . '">';
                        echo '<i class="far fa-thumbs-up"></i> ' . ($hasLiked ? 'Liked' : 'Like');
                        echo '</button>';

                        // Show edit/delete only if user is author or admin (not guest)
                        if ($role !== 'Guest' && (isset($user) && $user == $row['username'] || $role == 'admin')) {

                            echo '<a href="php/delete_thread.php?id=' . $row['id'] . '" class="action-btn delete-btn" onclick="return confirm(\'Are you sure you want to delete this thread?\')"><i class="far fa-trash-alt"></i> Delete</a>';
                        }
                        // comment button
                        // view comment button
                        echo '<a href="thread.php?id=' . $row['id'] . '" class="action-btn"><i class="far fa-comment"></i> View Comments</a>';
                        echo '</div>'; // .thread-actions

                        echo '</div>'; // .thread-card
                    }
                } else {
                    echo '<div class="thread-card">';
                    echo '<h3>No discussions yet</h3>';
                    echo '<p>Be the first to start a discussion!</p>';
                }
                ?>
            </div>
            <!-- righr sideber -->
            <?php
            include_once "Right_Sidebar.php";
            ?>

        </div>
    </main>

    <!-- Thread Details Modal -->
    <div id="threadModal" class="thread-modal" style="display:none;">
        <div class="thread-modal-content">
            <span class="close-modal" onclick="closeThreadModal()">&times;</span>
            <div id="threadModalBody">
                <!-- Thread details will be loaded here via JS -->
            </div>
        </div>
    </div>


    <script>
        // Like button functionality
        if (data.success) {
            // Toggle the liked state
            this.classList.toggle('liked');

            // Update the like count display
            const likeCountElement = this.closest('.thread-card').querySelector('.fa-thumbs-up').parentElement;
            let currentCount = parseInt(likeCountElement.textContent.trim().split(' ')[0]);

            // Update count based on like/unlike action
            currentCount = isLiked ? currentCount - 1 : currentCount + 1;
            likeCountElement.innerHTML = `<i class="far fa-thumbs-up"></i> ${currentCount} likes`;

            // Update button text
            this.innerHTML = `<i class="far fa-thumbs-up"></i> ${isLiked ? 'Like' : 'Liked'}`;
        }
    </script>
    <!-- Footer -->
    <?php

    include_once "footer.php";
    ?>
    <script>
        // Open thread modal

        document.querySelectorAll('.open-thread-modal').forEach(item => {
            item.addEventListener('click', event => {
                const threadId = event.currentTarget.getAttribute('data-thread-id');
                fetch(`php/get_thread_details.php?id=${threadId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const thread = data.thread;
                            document.getElementById('threadModalBody').innerHTML = `
                                <h2>${thread.title}</h2>
                                <p><strong>Category:</strong> ${thread.category}</p>
                                <p><strong>Posted by:</strong> ${thread.username}</p>
                                <p><strong>Date:</strong> ${new Date(thread.created_at).toLocaleDateString()}</p>
                                <p>${thread.content}</p>
                            `;
                            document.getElementById('threadModal').style.display = 'block';
                        } else {
                            alert('Error loading thread details.');
                        }
                    });
            });
        });

        // Close thread modal
        function closeThreadModal() {
            document.getElementById('threadModal').style.display = 'none';
        }
    </script>
</body>

</html>
Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque reiciendis voluptate velit nisi quia consectetur aliquam aut nesciunt ipsam, eum optio maxime cum odit, iure aperiam ducimus dolorum qui officia excepturi quae consequuntur sit? Facilis optio, aperiam, laborum totam harum eveniet reprehenderit hic dignissimos nobis soluta voluptate necessitatibus quam. Laboriosam.