<?php
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = intval($data["user_id"] ?? 0);
$name = $data["name"] ?? "";
$phone = $data["phone"] ?? "";
$address = $data["address"] ?? "";
$note = $data["note"] ?? "";
$items = $data["items"] ?? [];

if ($user_id <= 0 || empty($items)) {
  echo json_encode(["success" => false, "message" => "Invalid data"]);
  exit;
}

$conn->begin_transaction();

try {
  // 1. Tạo order
  $stmt = $conn->prepare(
    "INSERT INTO orders(user_id, name, phone, address, note) VALUES (?, ?, ?, ?, ?)"
  );
  $stmt->bind_param("issss", $user_id, $name, $phone, $address, $note);
  $stmt->execute();
  $order_id = $stmt->insert_id;

  $total = 0;

  // 2. Insert order_details
  foreach ($items as $it) {
    $pid = intval($it["product_id"]);
    $qty = intval($it["quantity"]);

    $p = $conn->query("SELECT price FROM product WHERE id=$pid LIMIT 1");
    if ($p->num_rows == 0) continue;

    $price = $p->fetch_assoc()["price"];
    $total += $price * $qty;

    $stmt2 = $conn->prepare(
      "INSERT INTO order_details(order_id, product_id, quantity, price)
       VALUES (?, ?, ?, ?)"
    );
    $stmt2->bind_param("iiid", $order_id, $pid, $qty, $price);
    $stmt2->execute();
  }

  // 3. Update total
  $conn->query("UPDATE orders SET total=$total WHERE id=$order_id");

  $conn->commit();
  echo json_encode(["success" => true, "order_id" => $order_id]);

} catch (Exception $e) {
  $conn->rollback();
  echo json_encode(["success" => false, "message" => "Create order failed"]);
}
