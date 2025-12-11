<?php
require_once 'config.php';
require_once 'includes/google_recaptcha.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    //$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All royal fields are required for Your registration! 👑';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Your royal email doesn\'t look quite magical enough! ✨';
    } elseif ($password !== $confirm_password) {
        $error = 'Your magical passwords don\'t match! Try again. 🔮';
    } elseif (strlen($password) < 6) {
        $error = 'Your magical password needs at least 6 sparkles! ✨✨✨';
    //} elseif (!verifyRecaptcha($recaptcha_response)) {
        //$error = 'Please prove you\'re not a robot trying to enter our castle! 🐉';
    } else {
        try {
            if (Auth::register($username, $email, $password)) {
                Auth::login($username, $password);
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Registration spell failed! This Your name or email might already be taken. 🏰';
            }
        } catch (PDOException $e) {
            $error = 'Magical registration error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌸 Join Royalty - Banana Kingdom</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="auth-page">
    <?php include 'background_template.php'; ?>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2><i class="fas fa-gem"></i> Become a Member </h2>
                <p>Join our magical banana kingdom and start your adventure! ✨</p>
               
                
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <!-- Honeypot field for dragon protection -->
                <input type="text" name="royal_dragon" style="display:none;" tabindex="-1" autocomplete="off">
                
                <div class="form-group">
                    <label for="username"><i class="fas fa-crown"></i> Name</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Choose your magical name" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Royal Email</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="Enter your magical email address" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-key"></i> Magic Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Create a magical password (6+ sparkles)">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-key"></i> Confirm Magic</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="Re-enter your magical password">
                </div>
                
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-magic"></i> Complete Royal Registration
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Already a Member? <a href="login.php"><i class="fas fa-door-open"></i> Enter Castle Here</a></p>
                <p>Just visiting? <a href="login.php?guest=1"><i class="fas fa-sparkles"></i> Quick Magical Visit</a></p>
                <a href="index.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Kingdom
                </a>
            </div>
        </div>
    </div>
</body>
</html>