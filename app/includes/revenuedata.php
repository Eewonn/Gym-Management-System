<?php
require_once __DIR__ . '/../../db/db.php';

$sql = "
    SELECT DATE_FORMAT(payment_date, '%M %Y') AS payment_month, SUM(amount) AS total_revenue
    FROM payments
    WHERE status = 'PAID'
    GROUP BY payment_month
    ORDER BY payment_month ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
