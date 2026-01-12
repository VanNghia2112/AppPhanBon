<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/SessionHelper.php';

class ProductController {
    private $productModel;
    private $db;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function isAdmin() { return SessionHelper::isAdmin(); }

    // ====================== SẢN PHẨM ======================
    public function index() {
        $products = $this->productModel->getProducts();
        include 'app/views/product/list.php';
    }

    public function show($id) {
        $product = $this->productModel->getProductById($id);
        if ($product) include 'app/views/product/show.php'; else echo "Không thấy sản phẩm.";
    }

    public function add() {
        if (!$this->isAdmin()) { echo "Bạn không có quyền!"; exit; }
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/product/add.php';
    }

    public function save() {
        if (!$this->isAdmin()) { echo "Bạn không có quyền!"; exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? ''; $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? ''; $category_id = $_POST['category_id'] ?? null;
            $image = (isset($_FILES['image']) && $_FILES['image']['error'] == 0) ? $this->uploadImage($_FILES['image']) : "";
            $this->productModel->addProduct($name, $description, $price, $category_id, $image);
            header('Location: /Website-PhanBon/Product/manage'); exit; // Sửa lại redirect về trang quản lý
        }
    }

    public function edit($id) {
        if (!$this->isAdmin()) { echo "Bạn không có quyền!"; exit; }
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/product/edit.php';
    }

    public function update() {
        if (!$this->isAdmin()) { echo "Bạn không có quyền!"; exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id']; $name = $_POST['name']; $description = $_POST['description'];
            $price = $_POST['price']; $category_id = $_POST['category_id'];
            $image = (isset($_FILES['image']) && $_FILES['image']['error'] == 0) ? $this->uploadImage($_FILES['image']) : $_POST['existing_image'];
            $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
            header('Location: /Website-PhanBon/Product/manage'); exit; // Sửa lại redirect về trang quản lý
        }
    }

    public function delete($id) {
        if (!$this->isAdmin()) { echo "Bạn không có quyền!"; exit; }
        $this->productModel->deleteProduct($id);
        header('Location: /Website-PhanBon/Product/manage'); exit; // Sửa lại redirect về trang quản lý
    }

    private function uploadImage($file) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/Website-PhanBon/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $unique = uniqid() . '_' . time() . '.' . strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        move_uploaded_file($file["tmp_name"], $target_dir . $unique);
        return "uploads/" . $unique;
    }

    // ====================== GIỎ HÀNG ======================
    public function cart() {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        include 'app/views/product/cart.php';
    }

    public function addToCart($id) {
        header('Content-Type: application/json; charset=utf-8');
        if (!SessionHelper::isLoggedIn()) { echo json_encode(['success' => false, 'message' => 'Cần đăng nhập.']); exit; }
        $product = $this->productModel->getProductById($id);
        if (!$product) { echo json_encode(['success' => false]); exit; }
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]['quantity']++;
        else $_SESSION['cart'][$id] = ['name' => $product->name, 'price' => $product->price, 'quantity' => 1, 'image' => $product->image];
        echo json_encode(['success' => true, 'cartCount' => array_sum(array_column($_SESSION['cart'], 'quantity'))]); exit;
    }

    public function updateCart() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id']; $qty = (int)$data['quantity'];
        if (isset($_SESSION['cart'][$id]) && $qty > 0) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
            $total = 0; $count = 0;
            foreach ($_SESSION['cart'] as $item) { $total += $item['price'] * $item['quantity']; $count += $item['quantity']; }
            echo json_encode(['success' => true, 'itemSubtotal' => number_format($_SESSION['cart'][$id]['price']*$qty, 0,',','.').' VND', 'cartTotal' => number_format($total, 0,',','.').' VND', 'cartCount' => $count]);
        } else echo json_encode(['success' => false]); exit;
    }

    public function removeFromCart() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $total = 0; $count = 0;
            foreach ($_SESSION['cart'] as $item) { $total += $item['price'] * $item['quantity']; $count += $item['quantity']; }
            echo json_encode(['success' => true, 'cartTotal' => number_format($total, 0,',','.').' VND', 'cartCount' => $count, 'cartEmpty' => empty($_SESSION['cart'])]);
        } else echo json_encode(['success' => false]); exit;
    }

    // ====================== THANH TOÁN (GIỮ NGUYÊN) ======================
    public function checkout() { include 'app/views/product/checkout.php'; }

    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name']; $phone = $_POST['phone']; $address = $_POST['address'];
            $method = $_POST['payment_method'] ?? 'COD';
            if (empty($_SESSION['cart'])) { echo "Giỏ hàng trống."; return; }
            
            $_SESSION['pending_order_info'] = ['name' => $name, 'phone' => $phone, 'address' => $address];
            $total = 0; foreach ($_SESSION['cart'] as $item) $total += $item['price'] * $item['quantity'];

            switch ($method) {
                case 'MOMO': $this->createMomoPayment($total); break;
                case 'VNPAY': $this->createVnpayPayment($total); break;
                default: $this->saveOrderToDatabase($name, $phone, $address, 'COD', 'pending'); 
                         header('Location: /Website-PhanBon/Product/orderConfirmation'); break;
            }
        }
    }

    private function saveOrderToDatabase($name, $phone, $address, $method, $status) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO orders (name, phone, address, status, payment_method) VALUES (:name, :phone, :address, :status, :method)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':name'=>$name, ':phone'=>$phone, ':address'=>$address, ':status'=>$status, ':method'=>$method]);
            $order_id = $this->db->lastInsertId();
            
            foreach ($_SESSION['cart'] as $pid => $item) {
                $q2 = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:oid, :pid, :qty, :price)";
                $stmt2 = $this->db->prepare($q2);
                $stmt2->execute([':oid'=>$order_id, ':pid'=>$pid, ':qty'=>$item['quantity'], ':price'=>$item['price']]);
            }
            unset($_SESSION['cart']); $this->db->commit();
        } catch (Exception $e) { $this->db->rollBack(); echo "Lỗi: " . $e->getMessage(); exit; }
    }

    private function createMomoPayment($amount) { include 'app/views/product/momo_simulate.php'; exit; }
    public function momoReturn() {
        if (isset($_GET['resultCode']) && $_GET['resultCode'] == '0') {
            $info = $_SESSION['pending_order_info'];
            $this->saveOrderToDatabase($info['name'], $info['phone'], $info['address'], 'MOMO', 'processing');
            unset($_SESSION['pending_order_info']);
            header('Location: /Website-PhanBon/Product/orderConfirmation'); exit;
        }
    }

    private function createVnpayPayment($amount) { include 'app/views/product/vnpay_simulate.php'; exit; }
    public function vnpayReturn() {
        if (isset($_GET['vnp_ResponseCode']) && $_GET['vnp_ResponseCode'] == '00') {
            $info = $_SESSION['pending_order_info'];
            $this->saveOrderToDatabase($info['name'], $info['phone'], $info['address'], 'VNPAY', 'paid');
            unset($_SESSION['pending_order_info']);
            header('Location: /Website-PhanBon/Product/orderConfirmation'); exit;
        }
    }
    public function orderConfirmation() { include 'app/views/product/orderConfirmation.php'; }

    // ====================== QUẢN LÝ ĐƠN HÀNG ======================
    public function manageOrders() {
        if (!$this->isAdmin()) { echo "Bạn không có quyền!"; exit; }
        
        $sqlStats = "SELECT COUNT(*) as total_orders, 
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders FROM orders";
        $stmtStats = $this->db->prepare($sqlStats);
        $stmtStats->execute();
        $statistics = $stmtStats->fetch(PDO::FETCH_OBJ);

        $stmt = $this->db->prepare("SELECT * FROM orders ORDER BY id DESC");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
        include 'app/views/product/manage.php'; // Lưu ý: View này có thể đang dùng chung, cần kiểm tra kỹ
    }

    public function deleteOrder() {
        header('Content-Type: application/json');
        if (!$this->isAdmin()) { echo json_encode(['success' => false, 'message' => 'Không có quyền.']); exit; }
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        if ($id) {
            try {
                $this->db->prepare("DELETE FROM order_details WHERE order_id = :id")->execute([':id'=>$id]);
                if ($this->db->prepare("DELETE FROM orders WHERE id = :id")->execute([':id'=>$id])) {
                    echo json_encode(['success' => true]);
                } else echo json_encode(['success' => false]);
            } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        } else echo json_encode(['success' => false]);
        exit;
    }

    public function updateOrderStatus() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data && isset($data['order_id'])) {
            $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            if ($stmt->execute([$data['status'], $data['order_id']])) echo json_encode(['success'=>true]);
            else echo json_encode(['success'=>false]);
        }
        exit;
    }
    
    // ====================== QUẢN LÝ SẢN PHẨM (ĐÃ SỬA) ======================
    public function manage() {
        if (!$this->isAdmin()) { echo "Bạn không có quyền!"; exit; }
        
        // 1. Lấy danh sách sản phẩm
        $products = $this->productModel->getProducts();
        
        // 2. Lấy danh sách danh mục (ĐÃ THÊM MỚI ĐỂ SỬA LỖI)
        $categoryModel = new CategoryModel($this->db);
        $categories = $categoryModel->getCategories(); 
        
        // 3. Truyền cả 2 biến vào view
        include 'app/views/product/manage.php';
    }
}
?>