<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];

// Check if user is counselor admin
$stmt = $pdo->prepare("SELECT is_counselor_admin FROM Users WHERE id = ?");
$stmt->execute([$user_id]);
$user_check = $stmt->fetch();
$is_counselor_admin = $user_check['is_counselor_admin'];

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_posts_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_likes_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_comments_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT l.user_id) as count FROM likes l JOIN posts p ON l.post_id = p.id WHERE p.user_id = ?");
$stmt->execute([$user_id]);
$user_received_likes = $stmt->fetch()['count'];

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['content'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, is_anonymous) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $content, $is_anonymous]);
        header('Location: feed.php');
        exit;
    }
}

// Fetch posts with like counts and user like status - randomize order on each load
$random_seed = isset($_GET['seed']) ? intval($_GET['seed']) : time();
$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.title,
        p.content,
        p.created_at,
        p.is_anonymous,
        CASE 
            WHEN p.is_anonymous THEN 'Anonymous'
            ELSE CONCAT(u.first_name, ' ', u.last_name)
        END as author_name,
        'post' as type,
        COUNT(DISTINCT l.id) as like_count,
        COUNT(DISTINCT c.id) as comment_count,
        MAX(CASE WHEN l.user_id = ? THEN 1 ELSE 0 END) as user_liked,
        RAND(?) as random_order
    FROM posts p
    JOIN Users u ON p.user_id = u.id
    LEFT JOIN likes l ON p.id = l.post_id
    LEFT JOIN comments c ON p.id = c.post_id
    GROUP BY p.id
    
    UNION ALL
    
    SELECT 
        r.id,
        r.title,
        r.content,
        r.created_at,
        0 as is_anonymous,
        CONCAT(u.first_name, ' ', u.last_name) as author_name,
        'resource' as type,
        0 as like_count,
        0 as comment_count,
        0 as user_liked,
        RAND(?) as random_order
    FROM resources r
    JOIN Users u ON r.user_id = u.id
    WHERE u.is_peer_counselor = 1
    
    ORDER BY random_order, created_at DESC
    LIMIT 20
");
$stmt->execute([$user_id, $random_seed, $random_seed]);
$posts = $stmt->fetchAll();

// Function to calculate time ago
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $current = time();
    $diff = $current - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed - MindLink</title>
    <link rel="stylesheet" href="../css/feed.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="../images/MindLink.png" alt="MindLink Logo">
            </div>
            <nav>
                <a href="feed.php" class="nav-link active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Feed
                </a>
                <a href="resources.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Resources
                </a>
                <?php if ($is_counselor_admin): ?>
                <a href="counselor_admin.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M12 1v6m0 6v6m5.196-13.196l-4.242 4.242m0 6.364l4.242 4.242M23 12h-6m-6 0H5m13.196 5.196l-4.242-4.242m0-6.364l4.242-4.242"/>
                    </svg>
                    Admin Panel
                </a>
                <?php endif; ?>
                <a href="profile.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Profile
                </a>
                <a href="help.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    Help
                </a>
                <a href="logout.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Logout
                </a>
            </nav>
        </header>

        <main>
            <div class="feed-container">
                <div class="left-sidebar">
                    <div class="post-form-container">
                        <h3>What's on your mind, <?php echo htmlspecialchars($first_name); ?>?</h3>
                        <form method="POST" class="post-form">
                            <input type="text" name="title" placeholder="Give your post a title..." required class="post-title-input">
                            <textarea name="content" placeholder="Share your thoughts..." required></textarea>
                            <div class="form-options">
                                <label class="anonymous-checkbox">
                                    <input type="checkbox" name="is_anonymous">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Post anonymously
                                </label>
                                <button type="submit" class="post-button">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="22" y1="2" x2="11" y2="13"/>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                    </svg>
                                    Post
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="user-stats-card">
                        <h3>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Your Activity
                        </h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $user_posts_count; ?></div>
                                <div class="stat-label">Posts</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $user_likes_count; ?></div>
                                <div class="stat-label">Likes Given</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $user_comments_count; ?></div>
                                <div class="stat-label">Comments</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $user_received_likes; ?></div>
                                <div class="stat-label">Likes Received</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="posts-container">
                    <?php foreach ($posts as $post): ?>
                        <div class="post <?php echo $post['type']; ?>">
                            <div class="post-header">
                                <div class="post-author-info">
                                    <span class="author"><?php echo htmlspecialchars($post['author_name']); ?></span>
                                    <span class="date"><?php echo timeAgo($post['created_at']); ?></span>
                                </div>
                                <?php if ($post['type'] == 'resource'): ?>
                                    <div class="resource-badge">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                            <polyline points="2 17 12 22 22 17"/>
                                            <polyline points="2 12 12 17 22 12"/>
                                        </svg>
                                        Wellness Tip
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                            
                            <div class="post-content">
                                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                            </div>
                            
                            <?php if ($post['type'] == 'post'): ?>
                                <div class="post-actions">
                                    <button class="action-btn like-btn <?php echo $post['user_liked'] ? 'liked' : ''; ?>" 
                                            data-post-id="<?php echo $post['id']; ?>">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo $post['user_liked'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                        </svg>
                                        <span class="like-count"><?php echo $post['like_count']; ?></span>
                                    </button>
                                    <button class="action-btn comment-btn" data-post-id="<?php echo $post['id']; ?>">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                        </svg>
                                        <span><?php echo $post['comment_count']; ?></span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../js/feed.js"></script>
</body>
</html>