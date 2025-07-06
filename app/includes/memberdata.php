<?php
require_once __DIR__ . '/../../db/db.php';

$sql = "
    SELECT 
        join_date, 
        (SELECT COUNT(*) FROM members AS m2 WHERE m2.join_date <= m1.join_date) AS total_members
    FROM 
        (SELECT DISTINCT join_date FROM members WHERE status = 'active') AS m1
    ORDER BY join_date
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
