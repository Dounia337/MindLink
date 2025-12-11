<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Check if user is peer counselor OR counselor admin
$stmt = $pdo->prepare("SELECT is_peer_counselor, is_counselor_admin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$is_peer_counselor = $user['is_peer_counselor'] || $user['is_counselor_admin'];
$is_counselor_admin = $user['is_counselor_admin'];

// Handle resource submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['content'])) {
    if (!$is_peer_counselor) {
        $error = 'Only verified peer counselors can post resources.';
    } else {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        
        if (!empty($title) && !empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO resources (user_id, title, content) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $title, $content]);
            $success = 'Resource posted successfully!';
        } else {
            $error = 'Please fill in all fields.';
        }
    }
}

// Fetch resources
$stmt = $pdo->prepare("
    SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as author_name
    FROM resources r
    JOIN users u ON r.user_id = u.id
    WHERE u.is_peer_counselor = 1
    ORDER BY r.created_at DESC
");
$stmt->execute();
$resources = $stmt->fetchAll();

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
    <title>Resources - MindLink</title>
    <link rel="stylesheet" href="../css/resources.css">
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
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Feed
                </a>
                <a href="resources.php" class="nav-link active">
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
            <div class="resources-container">
                <div class="resources-header">
                    <h2>Mental Wellness Resources</h2>
                    <p class="subtitle">Expert guidance and support from verified peer counselors</p>
                </div>
                
                <?php if ($success): ?>
                    <div class="success-message">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="error-message">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($is_peer_counselor): ?>
                    <div class="resource-form-container">
                        <h3>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                <polyline points="2 17 12 22 22 17"/>
                                <polyline points="2 12 12 17 22 12"/>
                            </svg>
                            Share a Wellness Resource
                        </h3>
                        <form method="POST" class="resource-form">
                            <div class="form-group">
                                <input type="text" name="title" placeholder="Resource Title" required>
                            </div>
                            <div class="form-group">
                                <textarea name="content" placeholder="Share your wisdom, tips, or guidance..." required></textarea>
                            </div>
                            <button type="submit" class="post-button">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                Post Resource
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="info-banner">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <div>
                            <strong>Want to share resources?</strong>
                            <p>Verify as a peer counselor in your profile settings to contribute mental wellness content.</p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="resources-list">
                    <?php if (count($resources) > 0): ?>
                        <?php foreach ($resources as $resource): ?>
                            <div class="resource-item">
                                <div class="resource-badge">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                        <polyline points="2 17 12 22 22 17"/>
                                        <polyline points="2 12 12 17 22 12"/>
                                    </svg>
                                    Wellness Tip
                                </div>
                                <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                <div class="resource-meta">
                                    <span class="author">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        <?php echo htmlspecialchars($resource['author_name']); ?>
                                    </span>
                                    <span class="date">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        <?php echo timeAgo($resource['created_at']); ?>
                                    </span>
                                </div>
                                <div class="resource-content">
                                    <?php echo nl2br(htmlspecialchars($resource['content'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-resources">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                            <p>No resources available yet. Check back soon!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>