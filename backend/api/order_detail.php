<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../app/config/database.php";

$db = new database();
$conn = $db->getConnection();

$order_id = $_GET["order_id"] ?? 0;

if (!$order_id) {
    echo json_encode([]);
    exit;
}

/* ===== LẤY THÔNG TIN ĐƠN ===== */
$sqlOrder = "
    SELECT id, name, phone, address, payment_method, status, total, created_at
    FROM orders
    WHERE id = ?
";

$stmt = $conn->prepare($sqlOrder);
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode([]);
    exit;
}

/* ===== LẤY CHI TIẾT SẢN PHẨM ===== */
$sqlItems = "
    SELECT p.name AS product_name, od.quantity, od.price
    FROM order_details od
    JOIN product p ON p.id = od.product_id
    WHERE od.order_id = ?
";

$stmt = $conn->prepare($sqlItems);
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===== TRẢ JSON ===== */
echo json_encode([
    "order" => $order,
    "items" => $items
]);
