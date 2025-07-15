<?php
session_start();
require_once __DIR__ . '/../../db/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "
    SELECT DATE_FORMAT(payment_date, '%M %Y') AS payment_month, SUM(amount) AS total_revenue
    FROM payments
    WHERE status = 'PAID' AND user_id = :user_id
    GROUP BY payment_month
    ORDER BY STR_TO_DATE(payment_month, '%M %Y') ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>