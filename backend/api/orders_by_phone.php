<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../app/config/database.php";

/* ====== KẾT NỐI DB ====== */
$db = new database();
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode([]);
    exit;
}

/* ====== LẤY PHONE ====== */
$phone = $_GET["phone"] ?? "";

if (empty($phone)) {
    echo json_encode([]);
    exit;
}

/* ====== QUERY ====== */
$sql = "
    SELECT id, total, status, created_at
    FROM orders
    WHERE phone = ?
    ORDER BY id DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute([$phone]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($orders);
