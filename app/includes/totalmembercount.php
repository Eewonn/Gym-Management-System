<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) AS total_members FROM members WHERE user_id = ?");
$stmt->execute([$userId]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['total_members' => $res['total_members']]);
