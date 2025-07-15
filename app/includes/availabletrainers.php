<?php

session_start();
require_once __DIR__ . '/../../db/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

// count active trainers  for the logged-in user only
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_active_trainers FROM trainers WHERE status = 'active' AND user_id = ?");
$stmt->execute([$userId]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['total_active_trainers' => isset($res['total_active_trainers']) ? $res['total_active_trainers'] : 0]);
?>