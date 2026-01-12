<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

// Nạp file cần thiết
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/ProductModel.php';

// Lấy ID từ URL
if (!isset($_GET['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing product id"
    ]);
    exit;
}

$id = intval($_GET['id']);

// Kết nối DB
$database = new Database();
$db = $database->getConnection();

// Model
$productModel = new ProductModel($db);

// Lấy dữ liệu
$product = $productModel->getProductById($id);

// Kiểm tra kết quả
if ($product) {
    echo json_encode([
        "success" => true,
        "data" => $product
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Product not found"
    ]);
}
