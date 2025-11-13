<?php
require_once 'utils.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guest'])) {
   
    $_SESSION['guest'] = true;
    $_SESSION['guest_name'] = 'Guest_' . bin2hex(random_bytes(3));
    header('Location: game.php');
    exit;
}


if (!isLoggedIn() && empty($_SESSION['guest'])) {
    header('Location: index.php'); exit;
}

$user = currentUser();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Play — Banana Game</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar">
    <div class="left">Banana Game Final Project Game By Devindi</div>
    <div class="right">
      <?php if ($user): ?>
        Hello, <?=htmlspecialchars($user['username'])?> |
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <?=htmlspecialchars($_SESSION['guest_name'] ?? 'Guest')?> |
        <a href="logout.php">End session</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="game-wrap">
    <section class="hud">
      <div>Score: <span id="score">0</span></div>
      <div>Stage: <span id="stage">1</span></div>
      <div id="timer">Time: <span id="timeLeft">30</span>s</div>
    </section>

    <section class="play-area">
      <div class="question-card">
        <img id="questionImg" src="" alt="loading..." />
        <div class="question-controls">
          <input id="answer" placeholder="Type answer here">
          <button id="submitAnswer">Submit</button>
        </div>
      </div>
      <div id="message" class="message"></div>
    </section>

    <section class="controls">
      <button id="newQ">New Question</button>
      <?php if ($user): ?>
      <button id="saveScore">Save Score</button>
      <?php else: ?>
      <button disabled title="Save available for registered users">Save (register to save)</button>
      <?php endif; ?>
    </section>
  </main>

  <div class="bg-play"></div>

  <script>
  const API_BANANA = 'api_banana.php';
  const SAVE_SCORE_ENDPOINT = 'save_score.php';
  </script>
  <script src="assets/game.js"></script>
</body>
</html>
