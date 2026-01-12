<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../app/config/database.php";

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data["user_id"] ?? null;
$name    = $data["name"] ?? "";
$phone   = $data["phone"] ?? "";

if (!$user_id || empty($name) || empty($phone)) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE account 
    SET name = ?, phone = ?
    WHERE id = ?
");

$result = $stmt->execute([$name, $phone, $user_id]);

echo json_encode([
    "success" => $result
]);
