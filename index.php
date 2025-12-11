<?php
require_once 'includes/auth.php';

// Show logout message if redirected from logout
if (isset($_GET['logout'])) {
    $logoutMessage = "👋 Farewell, Royal! Come back soon to continue your banana adventure! ✨";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌸 Royal Banana Kingdom - Welcome!</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="home-page">
    <?php include 'background_template.php'; ?>
    <?php include 'background_template.php'; ?>
    
    <div class="container">
        <!-- Princess Header -->
        <header class="princess-header">
            <div class="crown-container">
                <div class="crown">👑</div>
            </div>
            <div class="header-content">
                <h1>Welcome to <span class="princess-text">Devindi's Banana Kingdom</span>! 👑</h1>
                <p class="tagline">A magical world of puzzles, sparkles, and sweet adventures! ✨</p>
            </div>
        </header>
        
        <!-- Logout Message -->
        <?php if (isset($logoutMessage)): ?>
        <div class="alert alert-success">
            <?php echo $logoutMessage; ?>
        </div>
        <?php endif; ?>
        
        <main class="main-content">
            <div class="welcome-section">
                <?php if (Auth::isLoggedIn()): ?>
                    <?php $user = Auth::getCurrentUser(); ?>
                    <div class="welcome-message">
                        <h2>✨ Welcome back, <?php echo htmlspecialchars($user['username']); ?>! ✨</h2>
                        <p>Your magical banana adventure awaits in the sparkly kingdom...</p>
                        <div class="action-buttons" style="margin-top: 30px;">
                            <a href="dashboard.php" class="btn btn-primary btn-large">
                                <i class="fas fa-crown"></i> Enter Your Castle (Login)
                            </a>
                            <a href="game.php?new=true" class="btn btn-secondary btn-large">
                                <i class="fas fa-wand-sparkles"></i> Start New Adventure (Register)
                            </a>
                            <a href="logout.php" class="btn btn-logout">
                                <i class="fas fa-sign-out-alt"></i> Leave Kingdom (Logout)
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-options">
                        <div class="option-card">
                            <h3><i class="fas fa-magic"></i> Magical Guest Entry (Guest Login)</h3>
                            <p>Start your sparkly adventure immediately without any formalities!</p>
                            <form action="login.php" method="POST">
                                <input type="hidden" name="guest_login" value="1">
                                <button type="submit" class="btn btn-guest btn-block">
                                    <i class="fas fa-sparkles"></i> Quick Guest Play
                                </button>
                            </form>
                        </div>
                        
                        <div class="option-card">
                            <h3><i class="fas fa-star"></i> Become a Royal Member (Register)</h3>
                            <p>Join our royal community and save your magical progress forever!</p>
                            <a href="register.php" class="btn btn-register btn-block">
                                <i class="fas fa-gem"></i> Register Now 
                            </a>
                        </div>
                        
                        <div class="option-card">
                            <h3><i class="fas fa-crown"></i> Royal Login</h3>
                            <p>Already a royal in our kingdom? Enter with your royal credentials!</p>
                            <a href="login.php" class="btn btn-login btn-block">
                                <i class="fas fa-key"></i> Login Here
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="game-preview">
                <div class="preview-screen">
                    <div class="banana-animation">🍌</div>
                    <div class="stage-indicator">Castle Stage 1 ✨</div>
                    <div class="score-preview">Sparkles: 0 ✨</div>
                </div>
               <!-- <div class="features-list">
                    <h3><i class="fas fa-sparkles"></i> Magical Features:</h3>
                    <ul>
                        <li>👑 Progressive castle levels with sparkly rewards</li>
                        <li>🏆 Royal leaderboard with Your rankings</li>
                        <li>🔐 Magical secure authentication</li>
                        <li>💾 Save your Your progress in the cloud</li>
                        <li>🎨 Beautiful girly interface with animations</li>
                        <li>✨ Earn sparkles and magical bananas</li>
                        <li>🌸 Collect special Your achievements</li>
                        <li>💖 Make friends in the Your community</li>
                    </ul>
                </div> -->
            </div>
        </main>
        
        <!-- Princess Footer -->
        <footer class="princess-footer">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-crown"></i>
                    <span class="logo-text">Royal Banana Kingdom</span>
                </div>
                <div class="footer-links">
                    <a href="#"><i class="fas fa-home"></i> Home</a>
                    <a href="#"><i class="fas fa-info-circle"></i> About</a>
                    <a href="#"><i class="fas fa-question-circle"></i> Help</a>
                    <a href="#"><i class="fas fa-envelope"></i> Contact</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>✨ Made with love and sparkles for all divine's around the world! ✨</p>
            </div>
        </footer>
    </div>
    
    <script src="js/auth.js"></script>
</body>
</html>