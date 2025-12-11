<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if user is counselor admin
$stmt = $pdo->prepare("SELECT is_counselor_admin FROM Users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user['is_counselor_admin']) {
    header('Location: feed.php');
    exit;
}

$success = '';
$error = '';

// Handle adding new peer counselor ID
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_counselor'])) {
    $counselor_id = trim($_POST['counselor_id']);
    $first_name = trim($_POST['pc_first_name']);
    $last_name = trim($_POST['pc_last_name']);
    
    if (!empty($counselor_id) && !empty($first_name) && !empty($last_name)) {
        // Check if ID already exists
        $stmt = $pdo->prepare("SELECT id FROM peer_counselor_ids WHERE counselor_id = ?");
        $stmt->execute([$counselor_id]);
        
        if ($stmt->fetch()) {
            $error = 'This Counselor ID already exists';
        } else {
            $stmt = $pdo->prepare("INSERT INTO peer_counselor_ids (counselor_id, first_name, last_name) VALUES (?, ?, ?)");
            if ($stmt->execute([$counselor_id, $first_name, $last_name])) {
                $success = 'Peer Counselor ID added successfully!';
            } else {
                $error = 'Failed to add peer counselor ID';
            }
        }
    } else {
        $error = 'All fields are required';
    }
}

// Handle deleting peer counselor ID
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM peer_counselor_ids WHERE id = ?");
    if ($stmt->execute([$delete_id])) {
        $success = 'Peer Counselor ID deleted successfully!';
    } else {
        $error = 'Failed to delete peer counselor ID';
    }
}

// Fetch all peer counselor IDs
$stmt = $pdo->prepare("SELECT * FROM peer_counselor_ids ORDER BY created_at DESC");
$stmt->execute();
$counselor_ids = $stmt->fetchAll();

// Fetch statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Users WHERE is_peer_counselor = 1");
$stmt->execute();
$verified_counselors = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts");
$stmt->execute();
$total_posts = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM resources");
$stmt->execute();
$total_resources = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Users WHERE is_counselor_admin = 0");
$stmt->execute();
$total_students = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counselor Admin - MindLink</title>
    <link rel="stylesheet" href="../css/counselor_admin.css">
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
                <a href="resources.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Resources
                </a>
                <a href="counselor_admin.php" class="nav-link active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M12 1v6m0 6v6m5.196-13.196l-4.242 4.242m0 6.364l4.242 4.242M23 12h-6m-6 0H5m13.196 5.196l-4.242-4.242m0-6.364l4.242-4.242"/>
                    </svg>
                    Admin Panel
                </a>
                <a href="profile.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Profile
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
            <div class="admin-container">
                <div class="admin-header">
                    <h2>Counselor Admin Dashboard</h2>
                    <div class="admin-badge">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Admin Access
                    </div>
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

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $total_students; ?></h3>
                            <p>Total Students</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $verified_counselors; ?></h3>
                            <p>Verified Counselors</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $total_posts; ?></h3>
                            <p>Total Posts</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $total_resources; ?></h3>
                            <p>Resources Shared</p>
                        </div>
                    </div>
                </div>

                <!-- Add Peer Counselor Form -->
                <div class="add-counselor-section">
                    <h3>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="16"/>
                            <line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                        Add New Peer Counselor ID
                    </h3>
                    <form method="POST" class="counselor-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="counselor_id">Counselor ID</label>
                                <input type="text" name="counselor_id" id="counselor_id" placeholder="e.g., PC004" required>
                            </div>
                            <div class="form-group">
                                <label for="pc_first_name">First Name</label>
                                <input type="text" name="pc_first_name" id="pc_first_name" placeholder="First name" required>
                            </div>
                            <div class="form-group">
                                <label for="pc_last_name">Last Name</label>
                                <input type="text" name="pc_last_name" id="pc_last_name" placeholder="Last name" required>
                            </div>
                        </div>
                        <button type="submit" name="add_counselor" class="add-button">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="16"/>
                                <line x1="8" y1="12" x2="16" y2="12"/>
                            </svg>
                            Add Counselor ID
                        </button>
                    </form>
                </div>
                    
                <!-- List of Peer Counselor IDs -->
                <div class="counselors-list-section">
                    <h3>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11H3v2h6m-6 4h6v2H3m14 0h4v-2h-4m4-4h-4V9h4m-9 6l3.5 3.5L22 8"/>
                        </svg>
                        Registered Peer Counselor IDs
                    </h3>
                    <div class="counselors-list">
                        <?php if (count($counselor_ids) > 0): ?>
                            <?php foreach ($counselor_ids as $counselor): ?>
                                <div class="counselor-card">
                                    <div class="counselor-info">
                                        <div class="counselor-id-badge"><?php echo htmlspecialchars($counselor['counselor_id']); ?></div>
                                        <div class="counselor-name"><?php echo htmlspecialchars($counselor['first_name'] . ' ' . $counselor['last_name']); ?></div>
                                        <div class="counselor-date">Added: <?php echo date('M j, Y', strtotime($counselor['created_at'])); ?></div>
                                    </div>
                                    <a href="?delete_id=<?php echo $counselor['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this counselor ID?');">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                        Delete
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-counselors">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <p>No peer counselor IDs registered yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>