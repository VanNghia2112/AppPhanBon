<?php
require_once('app/config/database.php');
require_once('app/models/OrderModel.php');

class OrderController {
    private $orderModel;
    private $db;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($this->db);
    }

    private function isAdmin() {
        return SessionHelper::isAdmin();
    }

    // Trang quản lý đơn hàng (chỉ Admin)
    public function manage() {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập!";
            exit;
        }
        
        $orders = $this->orderModel->getAllOrders();
        $statistics = $this->orderModel->getOrderStatistics();
        
        include 'app/views/order/manage.php';
    }

    // Xem chi tiết đơn hàng
    public function detail($orderId) {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập!";
            exit;
        }
        
        $order = $this->orderModel->getOrderById($orderId);
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        
        if (!$order) {
            echo "Không tìm thấy đơn hàng!";
            exit;
        }
        
        include 'app/views/order/detail.php';
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus() {
        if (!$this->isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
            exit;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['order_id']) || !isset($data['status'])) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit;
        }

        $result = $this->orderModel->updateOrderStatus($data['order_id'], $data['status']);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
        }
        exit;
    }

    // Cập nhật thông tin đơn hàng
    public function update() {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập!";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'];
            $data = [
                'payment_method' => $_POST['payment_method'],
                'shipping_fee' => $_POST['shipping_fee'],
                'note' => $_POST['note']
            ];

            $result = $this->orderModel->updateOrderInfo($orderId, $data);
            
            if ($result) {
                header('Location: /Website-PhanBon/Order/detail/' . $orderId);
            } else {
                echo "Cập nhật thất bại!";
            }
        }
    }

    // Xóa đơn hàng
    public function delete($orderId) {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập!";
            exit;
        }

        $result = $this->orderModel->deleteOrder($orderId);
        
        if ($result) {
            header('Location: /Website-PhanBon/Order/manage');
        } else {
            echo "Xóa đơn hàng thất bại!";
        }
    }

    // Xuất Excel
    public function exportExcel() {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập!";
            exit;
        }

        $orders = $this->orderModel->getAllOrdersForExport();
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="orders_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="UTF-8"></head>';
        echo '<body>';
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Mã ĐH</th>';
        echo '<th>Khách hàng</th>';
        echo '<th>Số điện thoại</th>';
        echo '<th>Địa chỉ</th>';
        echo '<th>Tổng tiền</th>';
        echo '<th>Phí ship</th>';
        echo '<th>Thanh toán</th>';
        echo '<th>Trạng thái</th>';
        echo '<th>Ngày đặt</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($orders as $order) {
            echo '<tr>';
            echo '<td>' . $order->id . '</td>';
            echo '<td>' . htmlspecialchars($order->name) . '</td>';
            echo '<td>' . htmlspecialchars($order->phone) . '</td>';
            echo '<td>' . htmlspecialchars($order->address) . '</td>';
            echo '<td>' . number_format($order->total_amount, 0) . '</td>';
            echo '<td>' . number_format($order->shipping_fee, 0) . '</td>';
            echo '<td>' . htmlspecialchars($order->payment_method) . '</td>';
            echo '<td>' . htmlspecialchars($order->status) . '</td>';
            echo '<td>' . $order->created_at . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }
    
}