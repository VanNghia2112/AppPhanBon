<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../app/config/database.php";
$db = new database();
$conn = $db->getConnection();

$phone = $_GET["phone"] ?? "";

if ($phone == "") {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT id, name, phone, total, status, created_at
    FROM orders
    WHERE phone = :phone
    ORDER BY id DESC
");
$stmt->execute([":phone" => $phone]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($orders);
