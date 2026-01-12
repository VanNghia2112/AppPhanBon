<?php
header("Content-Type: application/json");
require_once "../app/config/database.php";

$db = new database();
$conn = $db->getConnection();

// Lấy dữ liệu gửi từ Flutter
$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";
$fullname = $_POST["fullname"] ?? "";
$phone = $_POST["phone"] ?? "";

// Kiểm tra rỗng
if (!$username || !$password || !$fullname || !$phone) {
    echo json_encode(["success" => false, "message" => "Vui lòng nhập đầy đủ thông tin"]);
    exit;
}

// Kiểm tra username trùng
$sql = "SELECT id FROM account WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$username]);

if ($stmt->rowCount() > 0) {
    echo json_encode(["success" => false, "message" => "Tên đăng nhập đã tồn tại"]);
    exit;
}

// Hash mật khẩu
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Lưu vào DB
$sqlInsert = "INSERT INTO account (username, fullname, phone, password, role) 
              VALUES (?, ?, ?, ?, 'user')";
$stmtInsert = $conn->prepare($sqlInsert);

if ($stmtInsert->execute([$username, $fullname, $phone, $hashed])) {
    echo json_encode(["success" => true, "message" => "Đăng ký thành công"]);
} else {
    echo json_encode(["success" => false, "message" => "Đăng ký thất bại"]);
}
