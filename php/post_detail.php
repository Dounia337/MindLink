<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_content'])) {
    $content = trim($_POST['comment_content']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, is_anonymous) VALUES (?, ?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $content, $is_anonymous]);
        header("Location: post_detail.php?id=$post_id");
        exit;
    }
}

// Fetch post details
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
        COUNT(DISTINCT l.id) as like_count,
        MAX(CASE WHEN l.user_id = ? THEN 1 ELSE 0 END) as user_liked
    FROM posts p
    JOIN Users u ON p.user_id = u.id
    LEFT JOIN likes l ON p.id = l.post_id
    WHERE p.id = ?
    GROUP BY p.id
");
$stmt->execute([$user_id, $post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: feed.php');
    exit;
}

// Fetch comments
$stmt = $pdo->prepare("
    SELECT 
        c.id,
        c.content,
        c.created_at,
        c.is_anonymous,
        CASE 
            WHEN c.is_anonymous THEN 'Anonymous'
            ELSE CONCAT(u.first_name, ' ', u.last_name)
        END as author_name
    FROM comments c
    JOIN Users u ON c.user_id = u.id
    WHERE c.post_id = ?
    ORDER BY c.created_at ASC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();

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
    <title><?php echo htmlspecialchars($post['title']); ?> - MindLink</title>
    <link rel="stylesheet" href="../css/post_detail.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="../images/MindLink.png" alt="MindLink Logo">
            </div>
            <nav>
                <a href="feed.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    Back to Feed
                </a>
            </nav>
        </header>

        <main>
            <div class="post-detail-container">
                <div class="post-card">
                    <div class="post-header">
                        <div class="post-author-info">
                            <span class="author"><?php echo htmlspecialchars($post['author_name']); ?></span>
                            <span class="date"><?php echo timeAgo($post['created_at']); ?></span>
                        </div>
                    </div>
                    
                    <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                    
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>
                    
                    <div class="post-stats">
                        <div class="stat">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo $post['user_liked'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            <?php echo $post['like_count']; ?> likes
                        </div>
                        <div class="stat">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <?php echo count($comments); ?> comments
                        </div>
                    </div>
                </div>

                <div class="comments-section">
                    <h2>Comments</h2>
                    
                    <div class="comment-form-container">
                        <h3>Leave a comment</h3>
                        <form method="POST" class="comment-form">
                            <textarea name="comment_content" placeholder="Share your thoughts..." required></textarea>
                            <div class="form-options">
                                <label class="anonymous-checkbox">
                                    <input type="checkbox" name="is_anonymous">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Comment anonymously
                                </label>
                                <button type="submit" class="submit-button">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="22" y1="2" x2="11" y2="13"/>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                    </svg>
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="comments-list">
                        <?php if (count($comments) > 0): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment">
                                    <div class="comment-header">
                                        <span class="comment-author"><?php echo htmlspecialchars($comment['author_name']); ?></span>
                                        <span class="comment-date"><?php echo timeAgo($comment['created_at']); ?></span>
                                    </div>
                                    <div class="comment-content">
                                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-comments">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                <p>No comments yet. Be the first to share your thoughts!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>