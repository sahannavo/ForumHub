<!-- Right Sidebar -->
<aside class="animate-fadeIn delay-3">
    <div class="sidebar-card">
        <h2>Community Stats</h2>
        <!-- user count -->
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <?php
                $query = "SELECT COUNT(*) AS user_count FROM users";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                echo '<span class="stat-value">' . htmlspecialchars($row['user_count']) . '</span>';
                echo '<span class="stat-label">Members</span>';
                ?>
            </div>
        </div>
        <!-- posts count -->
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-info">
                <?php
                $query = "SELECT COUNT(*) AS thread_count FROM threads";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                echo '<span class="stat-value">' . htmlspecialchars($row['thread_count']) . '</span>';
                echo '<span class="stat-label">Discussions</span>';
                ?>
            </div>
        </div>
        <!-- comments count -->
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <div class="stat-info">
                <?php
                $query = "SELECT COUNT(*) AS comment_count FROM comments";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                echo '<span class="stat-value">' . htmlspecialchars($row['comment_count']) . '</span>';
                echo '<span class="stat-label">Replies</span>';
                ?>
            </div>
        </div>
    </div>


</aside>