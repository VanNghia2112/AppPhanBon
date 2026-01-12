<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../app/config/database.php";

$db = new database();
$conn = $db->getConnection();

// Lấy phone từ param ?phone=...
$phone = $_GET["phone"] ?? "";

if ($phone == "") {
    echo json_encode(["success" => false, "message" => "Phone required"]);
    exit;
}

$sql = "SELECT id, name, phone, address, payment_method, note, created_at 
        FROM orders 
        WHERE phone = :phone
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([":phone" => $phone]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "data" => $orders
]);
