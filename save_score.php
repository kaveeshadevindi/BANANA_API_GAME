<?php
require_once 'utils.php';
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error'=>'login required']); exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$score = intval($data['score'] ?? 0);
$stage = intval($data['stage'] ?? 1);

if ($score < 0) $score = 0;
$pdo = db();
$stmt = $pdo->prepare("INSERT INTO scores (user_id, score, stage) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $score, $stage]);


echo json_encode(['ok'=>true]);
