<?php
require_once 'includes/auth.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = Auth::getCurrentUser();
$gameSession = Auth::getCurrentGameSession();

if (!$gameSession) {
    Auth::createGameSession($user['id']);
    $gameSession = Auth::getCurrentGameSession();
}

// Fetch user stats
$db = getDB();
$stmt = $db->prepare("
    SELECT 
        COUNT(DISTINCT stage) as stages_unlocked,
        SUM(score) as total_score,
        MAX(score) as personal_best,
        COUNT(*) as games_played
    FROM game_scores 
    WHERE user_id = ?
");
$stmt->execute([$user['id']]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch leaderboard (top 50 players)
$stmt = $db->prepare("
    SELECT 
        u.username,
        u.is_guest,
        MAX(gs.score) as high_score,
        SUM(gs.bananas_collected) as total_bananas,
        MAX(gs.stage) as max_stage,
        COUNT(DISTINCT gs.session_id) as sessions_played
    FROM users u
    LEFT JOIN game_scores gs ON u.id = gs.user_id
    WHERE u.is_guest = FALSE
    GROUP BY u.id
    ORDER BY high_score DESC, total_bananas DESC, max_stage DESC
    LIMIT 50
");
$stmt->execute();
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's position in leaderboard
$stmt = $db->prepare("
    SELECT position FROM (
        SELECT 
            u.id,
            u.username,
            MAX(gs.score) as high_score,
            ROW_NUMBER() OVER (ORDER BY MAX(gs.score) DESC) as position
        FROM users u
        LEFT JOIN game_scores gs ON u.id = gs.user_id
        WHERE u.is_guest = FALSE
        GROUP BY u.id
    ) ranked_users
    WHERE id = ?
");
$stmt->execute([$user['id']]);
$userRank = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch recent games
$stmt = $db->prepare("
    SELECT stage, score, bananas_collected, played_at 
    FROM game_scores 
    WHERE user_id = ? 
    ORDER BY played_at DESC 
    LIMIT 5
");
$stmt->execute([$user['id']]);
$recentGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch achievement badges
$achievements = [];
if ($stats['personal_best'] >= 1000) $achievements[] = ['icon' => '👑', 'name' => 'Banana Queen', 'color' => '#FF69B4'];
if ($stats['personal_best'] >= 500) $achievements[] = ['icon' => '🌟', 'name' => 'Star Player', 'color' => '#FFD700'];
if ($stats['stages_unlocked'] >= 5) $achievements[] = ['icon' => '🚀', 'name' => 'Stage Master', 'color' => '#9370DB'];
if ($stats['games_played'] >= 10) $achievements[] = ['icon' => '🎯', 'name' => 'Dedicated', 'color' => '#40E0D0'];
if ($stats['total_score'] >= 2000) $achievements[] = ['icon' => '🏆', 'name' => 'High Scorer', 'color' => '#FF6347'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌸 Royal Dashboard - Banana Adventure</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-page girly-theme">
    <!-- Animated Background Elements -->
    <div class="girly-bg">
        <div class="sparkle s1"></div>
        <div class="sparkle s2"></div>
        <div class="sparkle s3"></div>
        <div class="sparkle s4"></div>
        <div class="sparkle s5"></div>
        
    </div>
    
    <div class="container">
        <!-- Your Header -->
        <header class="princess-header">
            <div class="crown-container">
                <div class="crown">👑</div>
            </div>
            <div class="header-content">
                <h1><span class="princess-text">Royal</span> <span class="username-highlight"><?php echo htmlspecialchars($user['username']); ?></span>'s Castle</h1>
                <p class="tagline">Welcome to your magical banana kingdom! ✨</p>
            </div>
            <div class="header-actions">
                <div class="user-badges">
                    <?php if ($user['is_guest']): ?>
                        <span class="badge guest-badge">👑 Guest Royal</span>
                    <?php else: ?>
                        <span class="badge member-badge">⭐ Royal Member</span>
                    <?php endif; ?>
                    <?php if ($userRank && $userRank['position'] <= 10): ?>
                        <span class="badge top-player">🏆 Top <?php echo $userRank['position']; ?> Player</span>
                    <?php endif; ?>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Leave Castle
                </a>
            </div>
        </header>
        
        <!-- Main Dashboard -->
        <main class="dashboard-content girly-dashboard">
            <div class="dashboard-grid">
                <!-- Left Column: Player Stats -->
                <div class="dashboard-left">
                    <!-- Player Card -->
                    <div class="princess-card stats-card">
                        <div class="card-header pink-header">
                            <h3><i class="fas fa-chart-line"></i> Your Royal Statistics</h3>
                            <div class="header-ribbon"></div>
                        </div>
                        <div class="stats-grid">
                            <div class="stat-item girly-stat">
                                <div class="stat-icon">👑</div>
                                <div class="stat-content">
                                    <span class="stat-label">Royal Rank</span>
                                    <span class="stat-value">#<?php echo $userRank ? $userRank['position'] : 'N/A'; ?></span>
                                </div>
                            </div>
                            <div class="stat-item girly-stat">
                                <div class="stat-icon">🏰</div>
                                <div class="stat-content">
                                    <span class="stat-label">Castle Stage</span>
                                    <span class="stat-value"><?php echo $gameSession['current_stage']; ?></span>
                                </div>
                            </div>
                            <div class="stat-item girly-stat">
                                <div class="stat-icon">⭐</div>
                                <div class="stat-content">
                                    <span class="stat-label">Total Sparkles</span>
                                    <span class="stat-value"><?php echo $stats['total_score'] ?? 0; ?></span>
                                </div>
                            </div>
                            <div class="stat-item girly-stat">
                                <div class="stat-icon">🍌</div>
                                <div class="stat-content">
                                    <span class="stat-label">Magic Bananas</span>
                                    <span class="stat-value"><?php 
                                        $stmt = $db->prepare("SELECT SUM(bananas_collected) as total FROM game_scores WHERE user_id = ?");
                                        $stmt->execute([$user['id']]);
                                        $bananaTotal = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $bananaTotal['total'] ?? 0;
                                    ?></span>
                                </div>
                            </div>
                            <div class="stat-item girly-stat">
                                <div class="stat-icon">🎮</div>
                                <div class="stat-content">
                                    <span class="stat-label">Games Played</span>
                                    <span class="stat-value"><?php echo $stats['games_played'] ?? 0; ?></span>
                                </div>
                            </div>
                            <div class="stat-item girly-stat">
                                <div class="stat-icon">💎</div>
                                <div class="stat-content">
                                    <span class="stat-label">Personal Best</span>
                                    <span class="stat-value"><?php echo $stats['personal_best'] ?? 0; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Achievements -->
                    <?php if (!empty($achievements)): ?>
                    <div class="princess-card achievements-card">
                        <div class="card-header purple-header">
                            <h3><i class="fas fa-trophy"></i> Royal Achievements</h3>
                            <div class="header-ribbon"></div>
                        </div>
                        <div class="achievements-grid">
                            <?php foreach ($achievements as $achievement): ?>
                            <div class="achievement-badge" style="border-color: <?php echo $achievement['color']; ?>;">
                                <div class="achievement-icon" style="color: <?php echo $achievement['color']; ?>;">
                                    <?php echo $achievement['icon']; ?>
                                </div>
                                <div class="achievement-name"><?php echo $achievement['name']; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quick Actions -->
                    <div class="princess-card action-card">
                        <div class="card-header teal-header">
                            <h3><i class="fas fa-wand-sparkles"></i> Magic Actions</h3>
                            <div class="header-ribbon"></div>
                        </div>
                        <div class="action-buttons girly-buttons">
                            <a href="game.php" class="btn girly-btn btn-play">
                                <i class="fas fa-gamepad"></i>Continue Adventure (Exicting Game)
                            </a>
                            <a href="game.php?new=true" class="btn girly-btn btn-new">
                                <i class="fas fa-plus-circle"></i> New Adventure (New Game)
                            </a>
                            <a href="index.php" class="btn girly-btn btn-home">
                                <i class="fas fa-home"></i> Royal Home (Back to Index)
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Leaderboard -->
                <div class="dashboard-right">
                    <!-- Leaderboard Card -->
                    <div class="princess-card leaderboard-card">
                        <div class="card-header gold-header">
                            <h3><i class="fas fa-crown"></i> Royal Leaderboard</h3>
                            <div class="header-ribbon"></div>
                        </div>
                        
                        <div class="leaderboard-filters">
                            <button class="filter-btn active" data-filter="all">All Players</button>
                            <button class="filter-btn" data-filter="top10">Top 10</button>
                            <button class="filter-btn" data-filter="friends">Friends</button>
                        </div>
                        
                        <div class="leaderboard-container">
                            <div class="leaderboard-header">
                                <div class="rank-col">Rank</div>
                                <div class="player-col">Your</div>
                                <div class="score-col">Sparkles</div>
                                <div class="bananas-col">🍌</div>
                                <div class="stage-col">Castle</div>
                            </div>
                            
                            <div class="leaderboard-body">
                                <?php if (empty($leaderboard)): ?>
                                    <div class="no-leaderboard">
                                        <i class="fas fa-crown fa-3x"></i>
                                        <p>Be the first to claim the throne! 🏆</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($leaderboard as $index => $player): ?>
                                        <?php 
                                            $rank = $index + 1;
                                            $isCurrentUser = ($player['username'] === $user['username']);
                                            $rankClass = '';
                                            if ($rank === 1) $rankClass = 'rank-1';
                                            elseif ($rank === 2) $rankClass = 'rank-2';
                                            elseif ($rank === 3) $rankClass = 'rank-3';
                                            elseif ($isCurrentUser) $rankClass = 'current-user';
                                        ?>
                                        <div class="leaderboard-row <?php echo $rankClass; ?> <?php echo $isCurrentUser ? 'my-rank' : ''; ?>">
                                            <div class="rank-col">
                                                <div class="rank-badge">
                                                    <?php if ($rank === 1): ?>
                                                        <span class="rank-1">👑</span>
                                                    <?php elseif ($rank === 2): ?>
                                                        <span class="rank-2">🥈</span>
                                                    <?php elseif ($rank === 3): ?>
                                                        <span class="rank-3">🥉</span>
                                                    <?php else: ?>
                                                        <span class="rank-number">#<?php echo $rank; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="player-col">
                                                <div class="player-info">
                                                    <div class="player-avatar">
                                                        <?php 
                                                            $avatarColors = ['#FF69B4', '#9370DB', '#FF1493', '#DA70D6', '#FFB6C1'];
                                                            $colorIndex = $rank % count($avatarColors);
                                                        ?>
                                                        <div class="avatar-circle" style="background: <?php echo $avatarColors[$colorIndex]; ?>;">
                                                            <?php echo strtoupper(substr($player['username'], 0, 1)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="player-details">
                                                        <div class="player-name">
                                                            <?php echo htmlspecialchars($player['username']); ?>
                                                            <?php if ($isCurrentUser): ?>
                                                                <span class="you-badge">✨ YOU</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="player-sessions">
                                                            <?php echo $player['sessions_played']; ?> adventures
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="score-col">
                                                <div class="score-value sparkle-score">
                                                    <i class="fas fa-star"></i> <?php echo $player['high_score'] ? number_format($player['high_score']) : '0'; ?>
                                                </div>
                                            </div>
                                            <div class="bananas-col">
                                                <div class="banana-count">
                                                    <i class="fas fa-banana"></i> <?php echo $player['total_bananas'] ? number_format($player['total_bananas']) : '0'; ?>
                                                </div>
                                            </div>
                                            <div class="stage-col">
                                                <div class="stage-badge">
                                                    Castle <?php echo $player['max_stage'] ?: '1'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($userRank && $userRank['position'] > 10): ?>
                        <div class="my-position">
                            <div class="position-header">Your Royal Position</div>
                            <div class="position-row">
                                <div class="rank-col">
                                    <div class="rank-badge">
                                        <span class="rank-number">#<?php echo $userRank['position']; ?></span>
                                    </div>
                                </div>
                                <div class="player-col">
                                    <div class="player-info">
                                        <div class="player-avatar">
                                            <div class="avatar-circle" style="background: #FF69B4;">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="player-details">
                                            <div class="player-name">
                                                <?php echo htmlspecialchars($user['username']); ?>
                                                <span class="you-badge">✨ YOU</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="score-col">
                                    <div class="score-value">
                                        <i class="fas fa-star"></i> <?php echo $stats['personal_best'] ?? 0; ?>
                                    </div>
                                </div>
                                <div class="bananas-col">
                                    <div class="banana-count">
                                        <i class="fas fa-banana"></i> <?php 
                                            $bananaTotal = $bananaTotal['total'] ?? 0;
                                            echo $bananaTotal;
                                        ?>
                                    </div>
                                </div>
                                <div class="stage-col">
                                    <div class="stage-badge">
                                        Castle <?php echo $gameSession['current_stage']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Recent Games -->
                    <?php if ($recentGames): ?>
                    <div class="princess-card recent-games-card">
                        <div class="card-header pink-header">
                            <h3><i class="fas fa-history"></i> Recent Adventures</h3>
                            <div class="header-ribbon"></div>
                        </div>
                        <div class="recent-games-list">
                            <?php foreach ($recentGames as $game): ?>
                            <div class="recent-game">
                                <div class="game-icon">🎮</div>
                                <div class="game-details">
                                    <div class="game-title">
                                        Castle <?php echo $game['stage']; ?> Adventure
                                    </div>
                                    <div class="game-stats">
                                        <span class="game-stat"><i class="fas fa-star"></i> <?php echo $game['score']; ?></span>
                                        <span class="game-stat"><i class="fas fa-banana"></i> <?php echo $game['bananas_collected']; ?></span>
                                        <span class="game-time">
                                            <i class="far fa-clock"></i> 
                                            <?php echo date('M d, g:i A', strtotime($game['played_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="game-result">
                                    <?php if ($game['score'] >= 500): ?>
                                        <span class="result-badge excellent">Excellent! ✨</span>
                                    <?php elseif ($game['score'] >= 200): ?>
                                        <span class="result-badge good">Good! 🌟</span>
                                    <?php else: ?>
                                        <span class="result-badge okay">Nice! 💖</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="princess-footer">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-crown"></i>
                    <span class="logo-text">Royal Banana Kingdom</span>
                </div>
                <div class="footer-links">
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="game.php"><i class="fas fa-gamepad"></i> Play</a>
                    <a href="#"><i class="fas fa-question-circle"></i> Help</a>
                    <a href="#"><i class="fas fa-envelope"></i> Contact</a>
                </div>
                <div class="footer-stats">
                    <span class="stat"><i class="fas fa-users"></i> <?php 
                        $stmt = $db->prepare("SELECT COUNT(DISTINCT user_id) as total FROM game_scores");
                        $stmt->execute();
                        $totalPlayers = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo $totalPlayers['total'] ?? 0;
                    ?> Players</span>
                    <span class="stat"><i class="fas fa-banana"></i> <?php 
                        $stmt = $db->prepare("SELECT SUM(bananas_collected) as total FROM game_scores");
                        $stmt->execute();
                        $totalBananas = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo $totalBananas['total'] ?? 0;
                    ?> Bananas Collected</span>
                    <span class="stat"><i class="fas fa-gamepad"></i> <?php 
                        $stmt = $db->prepare("SELECT COUNT(*) as total FROM game_scores");
                        $stmt->execute();
                        $totalGames = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo $totalGames['total'] ?? 0;
                    ?> Games Played</span>
                </div>
            </div>
            <div class="footer-bottom">
                <p>✨ Made with love for all royals! ✨</p>
            </div>
        </footer>
    </div>
    
    <script>
    // Leaderboard filtering
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Filter logic (for demo purposes)
            if (filter === 'top10') {
                // Show only top 10
                const rows = document.querySelectorAll('.leaderboard-row');
                rows.forEach((row, index) => {
                    row.style.display = index < 10 ? 'flex' : 'none';
                });
            } else if (filter === 'friends') {
                // Show friends (for demo, show current user and top 3)
                const rows = document.querySelectorAll('.leaderboard-row');
                rows.forEach((row, index) => {
                    row.style.display = (index < 3 || row.classList.contains('my-rank')) ? 'flex' : 'none';
                });
            } else {
                // Show all
                const rows = document.querySelectorAll('.leaderboard-row');
                rows.forEach(row => {
                    row.style.display = 'flex';
                });
            }
        });
    });
    
    // Add some sparkle animations
    document.querySelectorAll('.sparkle-score').forEach(el => {
        el.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.color = '#FFD700';
        });
        el.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.color = '';
        });
    });
    </script>
</body>
</html>