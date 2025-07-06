<?php

require_once __DIR__ . '/../../db/db.php';

$stmt = $pdo->query("SELECT SUM(amount) AS total_revenue FROM payments WHERE status = 'PAID'");
$res = $stmt->fetch(PDO::FETCH_ASSOC);

$formattedRevenue = number_format($res['total_revenue'], 0, '.', ',');

header('Content-Type: application/json');
echo json_encode(['total_revenue' => $formattedRevenue]);
?>
