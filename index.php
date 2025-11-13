<?php
require_once 'utils.php';
if (isLoggedIn()) header('Location: game.php');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Banana Game — Login</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
  <div class="center-card">
    <h1>Banana Game Developed By Devindi</h1>

    <form method="post" action="login.php" class="auth-form">
      <label>Username or Email</label>
      <input name="user" required>
      <label>Password</label>
      <input type="password" name="pass" required>
      <button type="submit">Login</button>
    </form>

    <div class="or">— or —</div>

    <a class="btn" href="register.php">Create account</a>
    <form method="post" action="game.php" style="display:inline">
      <!-- Guest login eka -->
      <button class="btn secondary" type="submit" name="guest" value="1">Play as Guest</button>
    </form>
  </div>
  <div class="bg-play"></div>
</body>
</html>
