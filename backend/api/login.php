<?php
header("Content-Type: application/json");
require_once "../app/config/database.php";

$db = new database();
$conn = $db->getConnection();

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

$sql = "SELECT * FROM account WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$username]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user["password"])) {
    echo json_encode([
        "success" => true,
        "username" => $user["username"],
        "fullname" => $user["fullname"],
        "phone" => $user["phone"],     
        "role" => $user["role"]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Sai tài khoản hoặc mật khẩu"
    ]);
}
