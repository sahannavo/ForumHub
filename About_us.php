<?php
session_start();
require_once 'php/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | ForumName</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        /* [Include all the CSS from your thread.php page here] */

        /* Simple About Page Additions */
        .about-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .about-header h1 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .about-header p {
            font-size: 1.1rem;
            color: var(--text-color);
            max-width: 700px;
            margin: 0 auto;
        }

        .about-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px var(--shadow-color);
            border: 1px solid var(--border-color);
        }

        .about-card h2 {
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent);
        }

        .simple-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 30px 0;
        }

        .stat-box {
            text-align: center;
            padding: 20px;
            min-width: 150px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .join-cta {
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>

<body>
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
                    <li><a href="About_us.php">About</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="gradient-bg"></div>

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
                <div class="about-header">
                    <h1>About Our Community</h1>
                    <p>Welcome to ForumName - a place where people connect, share knowledge, and grow together.</p>
                </div>

                <div class="about-card">
                    <h2>Our Story</h2>
                    <p>ForumName was created in 2020 to provide a friendly space for open discussions. What started as a small project has grown into a vibrant community of thousands of members worldwide.</p>
                    <p>We believe in the power of sharing knowledge and building connections. Our platform is designed to encourage meaningful conversations while keeping things simple and accessible.</p>
                </div>

                <div class="about-card">
                    <h2>What We Offer</h2>
                    <ul style="list-style-type: none;">
                        <li style="margin-bottom: 10px; padding-left: 20px; position: relative;">
                            <span style="position: absolute; left: 0; color: var(--accent);">•</span>
                            Friendly, moderated discussions
                        </li>
                        <li style="margin-bottom: 10px; padding-left: 20px; position: relative;">
                            <span style="position: absolute; left: 0; color: var(--accent);">•</span>
                            Expert and beginner-friendly content
                        </li>
                        <li style="margin-bottom: 10px; padding-left: 20px; position: relative;">
                            <span style="position: absolute; left: 0; color: var(--accent);">•</span>
                            Organized categories for easy navigation
                        </li>
                        <li style="margin-bottom: 10px; padding-left: 20px; position: relative;">
                            <span style="position: absolute; left: 0; color: var(--accent);">•</span>
                            A respectful community environment
                        </li>
                    </ul>
                </div>

                <div class="about-card">
                    <h2>Community Stats</h2>
                    <div class="simple-stats">
                        <div class="stat-box">
                            <div class="stat-number">
                                <?php
                                $query = "SELECT COUNT(*) AS user_count FROM users";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo '<span class="stat-value">' . htmlspecialchars($row['user_count']) . '</span>';
                                echo '<span class="stat-label">Members</span>';
                                ?>
                            </div>
                            <div class="stat-label">Members</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number">
                                <?php
                                $query = "SELECT COUNT(*) AS thread_count FROM threads";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo '<span class="stat-value">' . htmlspecialchars($row['thread_count']) . '</span>';
                                echo '<span class="stat-label">Discussions</span>';
                                ?>

                            </div>
                            <div class="stat-label">Threads</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number">
                                <?php
                                $query = "SELECT COUNT(*) AS comment_count FROM comments";
                                $result = mysqli_query($conn, $query);
                                $row = mysqli_fetch_assoc($result);
                                echo '<span class="stat-value">' . htmlspecialchars($row['comment_count']) . '</span>';
                                echo '<span class="stat-label">Replies</span>';
                                ?>

                            </div>
                            <div class="stat-label">Replies</div>
                        </div>

                    </div>
                </div>

                <div class="about-card">
                    <h2>Our Team</h2>
                    <p>ForumName is maintained by a small, dedicated team who care about creating a positive online space:</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">
                        <div style="flex: 1; min-width: 200px;">
                            <h3 style="color: var(--primary);">Alex</h3>
                            <p style="color: var(--accent); font-weight: 500;">Founder</p>
                            <p>Created the forum to solve a need for quality discussions.</p>
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <h3 style="color: var(--primary);">Sam</h3>
                            <p style="color: var(--accent); font-weight: 500;">Moderator</p>
                            <p>Ensures our community remains welcoming to all.</p>
                        </div>
                    </div>
                </div>

                <div class="join-cta">
                    <h2>Ready to Join?</h2>
                    <p style="margin-bottom: 20px;">Become part of our growing community today.</p>
                    <a href="login_signup/signup.php" class="btn btn-primary">Sign Up Now</a>
                </div>
            </div>

            <!-- Right Sidebar -->
             <?php
            include_once "Right_Sidebar.php";
            // Include the right sidebar code here
             ?>


            </aside>
        </div>
    </main>
    <!-- Footer -->
    <?php
    include_once "footer.php";
    ?>
</body>

</html>