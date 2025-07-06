<?php

require_once __DIR__ . '/../../db/db.php';

// count active members
$stmt = $pdo->query("SELECT COUNT(*) AS total_active_members FROM members WHERE status = 'active'");
$res = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['total_active_members' => $res['total_active_members']]);
?>