<?php
header("Content-Type: application/json");

// 1. Nạp file Database
require_once __DIR__ . '/../app/config/database.php';

// 2. Nạp ProductModel
require_once __DIR__ . '/../app/models/ProductModel.php';

// 3. Khởi tạo DB
$database = new Database();          // class Database trong config/database.php
$db = $database->getConnection();    // lấy PDO connection

// 4. Khởi tạo Model
$productModel = new ProductModel($db);

// 5. Lấy dữ liệu
$products = $productModel->getProducts();

// 6. Trả JSON về Flutter
echo json_encode([
    "success" => true,
    "data" => $products
]);
