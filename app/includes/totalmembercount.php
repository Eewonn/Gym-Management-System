<?php

require_once __DIR__ . '/../../db/db.php';

// count active members
$stmt = $pdo->query("SELECT COUNT(*) AS total_members FROM members");
$res = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['total_members' => $res['total_members']]);
?>