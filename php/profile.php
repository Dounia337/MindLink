<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user data
$stmt = $pdo->prepare("SELECT first_name, last_name, email, profile_pic, about, is_peer_counselor, peer_counselor_id FROM Users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $about = trim($_POST['about']);
    
    // Handle profile picture upload
    $profile_pic = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_pic']['type'];
        
        if (in_array($file_type, $allowed_types)) {

            
          $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/MindLink/uploads/profile_pics/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
                // Delete old profile picture if exists
                if ($profile_pic && file_exists('../' . $profile_pic)) {
                    unlink('../' . $profile_pic);
                }
                $profile_pic = '/uploads/profile_pics/' . $filename;
            } else {
                $error = 'Failed to upload image.';
            }
        } else {
            $error = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        }
    }
    
    // Handle peer counselor verification
    $is_peer_counselor = $user['is_peer_counselor'];
    $peer_counselor_id = $user['peer_counselor_id'];
    
    if (isset($_POST['verify_counselor']) && !$is_peer_counselor) {
        $counselor_id_input = trim($_POST['counselor_id']);
        $verify_first_name = trim($_POST['verify_first_name']);
        $verify_last_name = trim($_POST['verify_last_name']);
        
        // Only verify if all fields are filled
        if (!empty($counselor_id_input) && !empty($verify_first_name) && !empty($verify_last_name)) {
            // Check if credentials match in peer_counselor_ids table
            $stmt = $pdo->prepare("SELECT id FROM peer_counselor_ids WHERE counselor_id = ? AND first_name = ? AND last_name = ?");
            $stmt->execute([$counselor_id_input, $verify_first_name, $verify_last_name]);
            
            if ($stmt->fetch()) {
                $is_peer_counselor = 1;
                $peer_counselor_id = $counselor_id_input;
                $success = 'Congratulations! You are now verified as a Peer Counselor.';
            } else {
                $error = 'Verification failed. Please check your credentials and try again.';
            }
        }
        // If fields are empty, just ignore the verification attempt and continue with profile update
    }
    
    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE Users SET first_name = ?, last_name = ?, about = ?, profile_pic = ?, is_peer_counselor = ?, peer_counselor_id = ? WHERE id = ?");
        
        if ($stmt->execute([$first_name, $last_name, $about, $profile_pic, $is_peer_counselor, $peer_counselor_id, $user_id])) {
            $_SESSION['first_name'] = $first_name;
            if (empty($success)) {
                $success = 'Profile updated successfully!';
            }
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT first_name, last_name, email, profile_pic, about, is_peer_counselor, peer_counselor_id FROM Users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } else {
            $error = 'Failed to update profile.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - MindLink</title>
    <link rel="stylesheet" href="../css/profile.css">
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
                <a href="profile.php" class="nav-link active">
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
            <div class="profile-container">
                <h2>Profile Settings</h2>
                
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
                
                <?php if ($success): ?>
                    <div class="success-message">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="profile-form">
                    <div class="profile-picture-section">
                        <div class="current-picture">
                            <img src="<?php echo $user['profile_pic'] ?: '/images/default-profile.png'; ?> " alt="Profile Picture">
                        </div>
                        <div class="upload-picture">
                            <label for="profile_pic">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                Upload new picture
                            </label>
                            <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" 
                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" 
                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email (cannot be changed)</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="about">About Me</label>
                        <textarea name="about" id="about" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['about']); ?></textarea>
                    </div>
                    
                    <?php if (!$user['is_peer_counselor']): ?>
                        <div class="verification-section">
                            <h3>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                Become a Peer Counselor
                            </h3>
                            <p>Verify your identity to share mental wellness resources with the community</p>
                            
                            <div class="form-group">
                                <label for="counselor_id">Peer Counselor ID</label>
                                <input type="text" name="counselor_id" id="counselor_id" placeholder="Enter your counselor ID">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="verify_first_name">First Name (as registered)</label>
                                    <input type="text" name="verify_first_name" id="verify_first_name" placeholder="First name">
                                </div>
                                <div class="form-group">
                                    <label for="verify_last_name">Last Name (as registered)</label>
                                    <input type="text" name="verify_last_name" id="verify_last_name" placeholder="Last name">
                                </div>
                            </div>
                            
                            <input type="hidden" name="verify_counselor" value="1">
                        </div>
                    <?php else: ?>
                        <div class="counselor-badge">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            Verified Peer Counselor
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="update-button">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Save Changes
                    </button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>