<?php
require_once 'config.php';
require_once 'includes/auth.php';

if (isset($_POST['guest_login']) || isset($_GET['guest'])) {
    Auth::createGuest();
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['guest_login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (Auth::login($username, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Oh no! Your royal credentials didn\'t match our records. 🏰';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌸 Royal Login - Royal Banana Kingdom</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <?php include 'background_template.php'; ?>
    <?php include 'background_template.php'; ?>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2><i class="fas fa-crown"></i> Royal Entry</h2>
                <p>Welcome back to your magical banana kingdom! ✨</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user-crown"></i> Royal Name or Royal Email</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Enter your royal name or email">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-key"></i> Royal Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your magical password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-door-open"></i> Enter the Castle
                </button>
            </form>
            
            <div class="guest-section">
                <form method="POST">
                    <input type="hidden" name="guest_login" value="1">
                    <button type="submit" class="btn btn-guest btn-block">
                        <i class="fas fa-sparkles"></i> Quick Magical Visit
                    </button>
                </form>
                <p class="guest-note">✨ No royal account needed! Your magical journey will be saved for this visit. ✨</p>
            </div>
            
            <div class="auth-footer">
                <p>Not yet a Royal? <a href="register.php"><i class="fas fa-gem"></i> Join the Royalty</a></p>
                <a href="index.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Kingdom
                </a>
            </div>
        </div>
    </div>
</body>
</html>