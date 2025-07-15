<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT SUM(amount) AS total_pending_balance FROM payments WHERE status = 'PENDING' AND user_id = ?");
$stmt->execute([$userId]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle null result (no rows)
$total = $res['total_pending_balance'] ?? 0;
$formattedRevenue = number_format($total, 0, '.', ',');

header('Content-Type: application/json');
echo json_encode(['total_pending_balance' => $formattedRevenue]);
?>