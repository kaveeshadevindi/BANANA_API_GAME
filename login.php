<?php
require_once 'utils.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}
$user = trim($_POST['user'] ?? '');
$pass = $_POST['pass'] ?? '';

if (!$user || !$pass) {
    $_SESSION['flash'] = "Enter username/email and password.";
    header('Location: index.php'); exit;
}

$pdo = db();
$stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ? OR email = ?");
$stmt->execute([$user, $user]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && password_verify($pass, $row['password_hash'])) {
    $_SESSION['user_id'] = $row['id'];
    header('Location: game.php');
} else {
    $_SESSION['flash'] = "Invalid credentials.";
    header('Location: index.php');
}
