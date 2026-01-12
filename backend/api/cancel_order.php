<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../app/config/database.php";

$db = new database();
$conn = $db->getConnection();

$order_id = $_POST["order_id"] ?? 0;

if (!$order_id) {
    echo json_encode(["success" => false]);
    exit;
}

/* Chỉ cho huỷ khi pending */
$sql = "
    UPDATE orders 
    SET status = 'cancelled'
    WHERE id = ? AND status = 'pending'
";

$stmt = $conn->prepare($sql);
$success = $stmt->execute([$order_id]);

echo json_encode([
    "success" => $success
]);
