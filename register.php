<?php
require_once 'utils.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $recaptcha = $_POST['g-recaptcha-response'] ?? '';

    
    if (!$username || !$email || !$password) $errors[] = "All fields required.";

    
    $resp = null;
    if ($recaptcha) {$recaptchaSuccess = true;} else {
       $recaptchaSuccess = true;
    }

    if (empty($errors)) {
    try {
        $pdo = db();

        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $errors[] = "Username or email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $ins->execute([$username, $email, $hash]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            header("Location: game.php");
            exit;
        }
    } catch (Exception $e) {
        echo "Database Error: " . $e->getMessage();
        exit;
    }
}
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register — Banana Game</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
  <div class="center-card">
    <h2>Create account</h2>
    <?php if ($errors): ?>
      <div class="errors"><?=implode('<br>',array_map('htmlspecialchars',$errors))?></div>
    <?php endif; ?>
   <form method="post" class="auth-form">
    <label for="username">Username</label>
    <input id="username" name="username" placeholder="Enter username" required>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" placeholder="Enter email" required>

    <label for="password">Password</label>
    <input id="password" name="password" type="password" placeholder="Enter password" required>

    <div class="g-recaptcha" data-sitekey="<?=RECAPTCHA_SITE_KEY?>"></div>

    <button type="submit">Register</button>
</form>

    <p><a href="index.php">Back to login</a></p>
  </div>
  <div class="bg-play"></div>
</body>
</html>
