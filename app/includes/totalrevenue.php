<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

// Check if user_id is set
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT SUM(amount) AS total_revenue FROM payments WHERE status = 'PAID' AND user_id = ?");
$stmt->execute([$userId]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

// Fallback in case there are no paid payments
$total = $res['total_revenue'] ?? 0;
$formattedRevenue = number_format($total, 0, '.', ',');

header('Content-Type: application/json');
echo json_encode(['total_revenue' => $formattedRevenue]);
?>
