<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/InventoryModel.php';
require_once 'app/helpers/SessionHelper.php';

class InventoryController
{
    private $db;
    private $productModel;
    private $inventoryModel;

    public function __construct()
    {
        SessionHelper::start();

        // Chỉ cho admin truy cập (nếu bạn có SessionHelper::isAdmin)
        if (!SessionHelper::isAdmin()) {
            header("Location: /Website-PhanBon");
            exit;
        }

        $database = new database();
        $this->db = $database->getConnection();

        $this->productModel   = new ProductModel($this->db);
        $this->inventoryModel = new InventoryModel($this->db);
    }

    // Trang xem tồn kho
    public function index()
    {
        $products        = $this->productModel->getAllWithStock();
        $lowStockProducts = $this->productModel->getLowStockProducts();

        require 'app/views/inventory/index.php';
    }

    // Form nhập / xuất / điều chỉnh
    public function movementForm()
    {
        $products = $this->productModel->getAllProductsSimple();
        require 'app/views/inventory/movement_form.php';
    }

    // Lưu cập nhật nhập–xuất–tồn
    public function saveMovement()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $type      = $_POST['type'] ?? '';
        $quantity  = (int)($_POST['quantity'] ?? 0);
        $reason    = $_POST['reason'] ?? '';
        $userId    = $_SESSION['user_id'] ?? null; // ✔ Sửa lỗi SessionHelper::get()

        if ($productId > 0 && in_array($type, ['import', 'export', 'adjust']) && $quantity > 0) {
            $ok = $this->inventoryModel->addMovement($productId, $type, $quantity, $reason, $userId);

            if ($ok) {
                $_SESSION['flash_success'] = "Cập nhật tồn kho thành công.";
            } else {
                $_SESSION['flash_error'] = "Có lỗi xảy ra khi cập nhật tồn kho.";
            }
        } else {
            $_SESSION['flash_error'] = "Dữ liệu không hợp lệ.";
        }
    }

    header("Location: /Website-PhanBon/Inventory/index");
    exit;
}

}
