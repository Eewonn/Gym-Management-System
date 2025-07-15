<?php

require_once __DIR__ . '/../../db/db.php';

$stmt = $pdo->query("SELECT count(amount) AS total_unpaid FROM payments WHERE status = 'PENDING'");
$res = $stmt->fetch(PDO::FETCH_ASSOC);

$formattedRevenue = number_format($res['total_unpaid'], 0, '.', ',');

header('Content-Type: application/json');
echo json_encode(['total_unpaid' => $formattedRevenue]);
?>
