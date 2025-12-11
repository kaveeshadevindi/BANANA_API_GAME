<?php
require_once 'includes/auth.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = Auth::getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = intval($_POST['score'] ?? 0);
    $bananas = intval($_POST['bananas'] ?? 0);
    $stage = intval($_POST['stage'] ?? 1);
    $playTime = intval($_POST['play_time'] ?? 0);
    $sessionToken = $_POST['session_token'] ?? '';
    
    $db = getDB();
    
    // Save game score
    $stmt = $db->prepare("
        INSERT INTO game_scores 
        (user_id, session_id, stage, score, bananas_collected, play_time) 
        SELECT ?, gs.id, ?, ?, ?, ?
        FROM game_sessions gs 
        WHERE gs.session_token = ?
    ");
    $stmt->execute([$user['id'], $stage, $score, $bananas, $playTime, $sessionToken]);
    
    // Update game session stats
    $stmt = $db->prepare("
        UPDATE game_sessions 
        SET current_stage = ?, 
            total_score = total_score + ?, 
            high_score = GREATEST(high_score, ?),
            updated_at = CURRENT_TIMESTAMP
        WHERE session_token = ?
    ");
    $stmt->execute([$stage, $score, $score, $sessionToken]);
    
    // Return JSON response for AJAX calls
    if (isset($_POST['action']) && $_POST['action'] === 'update_stage') {
        echo json_encode(['success' => true, 'stage' => $stage]);
        exit();
    }
    
    // Redirect to dashboard
    header('Location: dashboard.php?score_saved=true');
    exit();
}
?>