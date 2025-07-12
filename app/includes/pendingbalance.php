<?php

require_once __DIR__ . '/../../db/db.php';

$stmt = $pdo->query("SELECT SUM(amount) AS total_pending_balance  FROM payments WHERE status = 'PENDING'");
$res = $stmt->fetch(PDO::FETCH_ASSOC);

$formattedRevenue = number_format($res['total_pending_balance'], 0, '.', ',');

header('Content-Type: application/json');
echo json_encode(['total_pending_balance' => $formattedRevenue]);
?>
