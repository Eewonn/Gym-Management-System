<?php
require_once __DIR__ . '/../../db/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "
    SELECT 
        join_date, 
        (
            SELECT COUNT(*) 
            FROM members AS m2 
            WHERE m2.join_date <= m1.join_date 
              AND m2.status = 'active' 
              AND m2.user_id = :user_id
        ) AS total_members
    FROM 
        (SELECT DISTINCT join_date 
         FROM members 
         WHERE status = 'active' AND user_id = :user_id) AS m1
    ORDER BY join_date
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

