<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(amount) AS total_unpaid FROM payments WHERE status = 'PENDING' AND user_id = ?");
$stmt->execute([$userId]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

$totalUnpaid = $res['total_unpaid'] ?? 0;
$formattedRevenue = number_format($totalUnpaid, 0, '.', ',');

header('Content-Type: application/json');
echo json_encode(['total_unpaid' => $formattedRevenue]);
?>
