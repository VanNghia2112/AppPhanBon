<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../app/config/database.php";

$db = new database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

$fullname = $data["fullname"] ?? "";
$phone = $data["phone"] ?? "";
$address = $data["address"] ?? "";
$note = $data["note"] ?? "";
$payment_method = $data["payment_method"] ?? "COD";
$items = $data["items"] ?? [];

if (empty($fullname) || empty($phone) || empty($address) || empty($items)) {
    echo json_encode(["success" => false, "message" => "Thiếu thông tin"]);
    exit;
}

// Trạng thái theo phương thức
$status = ($payment_method === "MOMO") ? "paid" : "pending";

$conn->beginTransaction();

try {
    // 1. Tạo đơn hàng
    $stmt = $conn->prepare("
        INSERT INTO orders (name, phone, address, note, payment_method, status, total)
        VALUES (?, ?, ?, ?, ?, ?, 0)
    ");
    $stmt->execute([$fullname, $phone, $address, $note, $payment_method, $status]);

    $order_id = $conn->lastInsertId();
    $total = 0;

    // 2. Chi tiết đơn
    foreach ($items as $item) {
        $pid = intval($item["product_id"]);
        $qty = intval($item["quantity"]);

        $p = $conn->prepare("SELECT price FROM product WHERE id = ?");
        $p->execute([$pid]);
        $price = $p->fetchColumn();

        if (!$price) continue;

        $total += $price * $qty;

        $stmt2 = $conn->prepare("
            INSERT INTO order_details (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt2->execute([$order_id, $pid, $qty, $price]);
    }

    // 3. Update tổng tiền
    $conn->prepare("UPDATE orders SET total = ? WHERE id = ?")
         ->execute([$total, $order_id]);

    $conn->commit();

    echo json_encode([
        "success" => true,
        "order_id" => $order_id
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(["success" => false, "message" => "Create order failed"]);
}
