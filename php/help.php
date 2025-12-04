<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help - MindLink</title>
    <link rel="stylesheet" href="../css/help.css">
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
                <a href="profile.php" class="nav-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Profile
                </a>
                <a href="help.php" class="nav-link active">
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
            <div class="help-container">
                <h2>Frequently Asked Questions</h2>
                <p class="subtitle">Everything you need to know about MindLink</p>
                
                <div class="faq-section">
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <div class="faq-content">
                            <h3>What is MindLink?</h3>
                            <p>MindLink is a student-centered mental wellness platform that provides a safe, anonymous space for students to express their feelings and connect with peers. It's built on the belief that healing begins when someone feels seen and safe.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="faq-content">
                            <h3>Is MindLink really anonymous?</h3>
                            <p>Yes! When you post on the feed or comment, you have the option to post anonymously. Your identity will not be revealed to other users when you choose this option. Your privacy and comfort are our top priorities.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <div class="faq-content">
                            <h3>How do I become a peer counselor?</h3>
                            <p>To become a verified peer counselor, go to your profile settings and submit your Peer Counselor ID along with your registered name. Our system will verify your credentials against our database. Once verified, you'll be able to post mental wellness resources for the community.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <div class="faq-content">
                            <h3>How do I interact with posts?</h3>
                            <p>You can like posts by clicking the heart icon and comment on them by clicking the comment icon. This takes you to a detailed view where you can read all comments and add your own thoughts - anonymously if you prefer.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <div class="faq-content">
                            <h3>How do I report inappropriate content?</h3>
                            <p>If you see content that violates our community guidelines or makes you uncomfortable, please contact us immediately at support@mindlink.com. We take all reports seriously and act quickly to maintain a safe environment for everyone.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <div class="faq-content">
                            <h3>Is my data secure?</h3>
                            <p>Absolutely. We take your privacy and security very seriously. All personal information is encrypted, and we never share your data with third parties without your explicit consent. Your mental wellness journey is private and protected.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </div>
                        <div class="faq-content">
                            <h3>What makes MindLink different?</h3>
                            <p>MindLink is designed with empathy at its core. We don't just provide a platform - we create a community. Every feature is thoughtfully designed to make you feel safe, heard, and supported. Your voice matters, and your feelings are valid.</p>
                        </div>
                    </div>
                </div>
                
                <div class="contact-info">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <h3>Still need help?</h3>
                    <p>We're here for you. Reach out anytime.</p>
                    <a href="mailto:support@mindlink.com" class="contact-button">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        support@mindlink.com
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>