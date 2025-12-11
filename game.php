<?php
require_once 'includes/auth.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = Auth::getCurrentUser();
$gameSession = Auth::getCurrentGameSession();

if (isset($_GET['new'])) {
    // Create new game session
    Auth::createGameSession($user['id']);
    $gameSession = Auth::getCurrentGameSession();
}

$currentStage = $gameSession['current_stage'] ?? 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌸 Magic Banana Game - Your Banana Kingdom</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- MathJax for rendering mathematical expressions -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script src="js/game.js" defer></script>
</head>
<body class="game-page">
    <!-- Girly Background Elements -->
    <div class="girly-background"></div>

    <!-- Sparkles -->
    <div class="sparkle s1"></div>
    <div class="sparkle s2"></div>
    <div class="sparkle s3"></div>
    <div class="sparkle s4"></div>
    <div class="sparkle s5"></div>

    <!-- Floating Decorations -->
    <div class="floating-element h1 heart">💖</div>
    <div class="floating-element h2 heart">💕</div>
    <div class="floating-element h3 heart">💗</div>
    <div class="floating-element f1 flower">🌸</div>
    <div class="floating-element f2 flower">🌺</div>
    <div class="floating-element f3 flower">🌷</div>
    <div class="floating-element s1 star">⭐</div>
    <div class="floating-element s2 crown">👑</div>
    <div class="floating-element s3 banana">🍌</div>
    
    <div class="game-container">
        <!-- Game Header -->
        <div class="game-header">
            <div class="player-info">
                <div class="princess-name">Your: <?php echo htmlspecialchars($user['username']); ?></div>
                <div class="magic-session">Magic Code: <?php echo substr($gameSession['session_token'], 0, 8); ?>✨</div>
            </div>
            <h1>Castle <?php echo $currentStage; ?> 🏰</h1>
            <div class="game-controls">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-crown"></i> Royal Dashboard
                </a>
                <button id="pauseBtn" class="btn btn-secondary">
                    <i class="fas fa-pause"></i> Pause Magic
                </button>
            </div>
        </div>
        
        <div class="game-area">
            <!-- Game Stats -->
            <div class="game-stats">
                <div class="stat-box">
                    <div class="stat-label">Sparkles ✨</div>
                    <div class="stat-value" id="score">0</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Magic Bananas 🍌</div>
                    <div class="stat-value" id="bananas">0</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Magic Time ⏳</div>
                    <div class="stat-value" id="time">60s</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Castle Stage 🏰</div>
                    <div class="stat-value" id="stage"><?php echo $currentStage; ?></div>
                </div>
            </div>
            
            <!-- Banana Game Container -->
            <div class="banana-api-container">
                <div id="bananaGame">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p>Summoning magical bananas... ✨</p>
                    </div>
                </div>
                <div class="game-instructions">
                    <h3><i class="fas fa-scroll"></i> Royal Instructions:</h3>
                    <p>✨ Solve the magical banana puzzle! Click the correct answer.</p>
                    <p>✨ Each correct answer gives you sparkles and magic bananas.</p>
                    <p>✨ Reach the magical threshold to unlock the next castle!</p>
                    <div class="difficulty-info">
                        <p><strong>🏰 Castle <?php echo $currentStage; ?> Magic:</strong></p>
                        <ul>
                            <li>✨ Sparkles per answer: <?php echo 10 * $currentStage; ?></li>
                            <li>🍌 Magic bananas per answer: <?php echo 2 * $currentStage; ?></li>
                            <li>⏳ Time magic penalty: 5 seconds</li>
                            <li>💖 Hint magic cost: 5 bananas</li>
                            <li>🌀 Skip spell cost: 10 seconds</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Progress Section -->
            <div class="progress-section">
                <div class="stage-progress-bar">
                    <div class="progress-label">Castle Progress ✨</div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="stageProgress" style="width: 0%"></div>
                    </div>
                    <div class="progress-text" id="progressText">0/<?php 
                        $thresholds = [1 => 100, 2 => 300, 3 => 600, 4 => 1000, 5 => 1500];
                        echo $thresholds[min($currentStage, 5)] ?? 100; 
                    ?></div>
                </div>
                
                <div class="stage-threshold">
                    <div class="threshold-label">Next Castle Unlocks at:</div>
                    <div class="threshold-value" id="nextStageThreshold">
                        <?php 
                        $thresholds = [1 => 100, 2 => 300, 3 => 600, 4 => 1000, 5 => 1500];
                        echo $thresholds[min($currentStage, 5)] ?? 100; 
                        ?> sparkles ✨
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Game Controls -->
        <div class="game-controls-bottom">
            <button id="hintBtn" class="btn btn-hint">
                <i class="fas fa-lightbulb"></i> Magical Hint (5🍌)
            </button>
            <button id="skipBtn" class="btn btn-skip">
                <i class="fas fa-forward"></i> Skip Spell (10⏳)
            </button>
            <button id="saveScoreBtn" class="btn btn-save">
                <i class="fas fa-save"></i> Save & Return to Castle
            </button>
        </div>
        
        <!-- Game Messages -->
        <div class="game-messages" id="gameMessages"></div>
    </div>
    
    <!-- Hidden form for saving score -->
    <form id="saveScoreForm" action="save_score.php" method="POST" style="display: none;">
        <input type="hidden" name="score" id="hiddenScore">
        <input type="hidden" name="bananas" id="hiddenBananas">
        <input type="hidden" name="stage" id="hiddenStage">
        <input type="hidden" name="play_time" id="hiddenPlayTime">
        <input type="hidden" name="session_token" value="<?php echo $gameSession['session_token']; ?>">
    </form>
    
    <script>
        const gameConfig = {
            sessionToken: "<?php echo $gameSession['session_token']; ?>",
            currentStage: <?php echo $currentStage; ?>,
            stageThresholds: <?php echo json_encode([1 => 100, 2 => 300, 3 => 600, 4 => 1000, 5 => 1500]); ?>,
            bananaApiUrl: "<?php echo BANANA_API_URL; ?>"
        };
    </script>
    
    <script src="js/auth.js"></script>
</body>
</html>