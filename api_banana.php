<?php

require_once 'config.php';
header('Content-Type: application/json');


$out = $_GET['out'] ?? 'json';
$base64 = $_GET['base64'] ?? 'no';

$base = BANANA_BASE_URL; 
$url = $base . "?out=" . urlencode($out) . "&base64=" . urlencode($base64);


$resp = @file_get_contents($url);
if ($resp === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to fetch Banana API']);
    exit;
}


echo $resp;
