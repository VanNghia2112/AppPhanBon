<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/User.php';

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "Missing id"]);
    exit;
}

$id = intval($_GET['id']);

$database = new Database();
$db = $database->getConnection();

$userModel = new User($db);
$user = $userModel->find($id);

if ($user) {
    echo json_encode(["success" => true, "data" => $user]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}
