<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: feed.php');
    exit;
}

$error = '';
$success = isset($_SESSION['signup_success']) ? $_SESSION['signup_success'] : '';
unset($_SESSION['signup_success']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../config/database.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $stmt = $pdo->prepare("SELECT id, first_name, password FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            header('Location: feed.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MindLink</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="logo">
                <img src="../images/MindLink.png" alt="MindLink Logo">
            </div>
            
            <div class="auth-box">
                <h2>Welcome Back</h2>
                <p class="subtitle">Login to continue your journey</p>
                
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
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="your.email@example.com" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    </div>
                    
                    <button type="submit" class="auth-button">
                        <span>Login</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </form>
                
                <div class="auth-redirect">
                    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>