<?php
// File: app/controllers/AdminController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class AdminController
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        
        // Kiểm tra session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Chỉ admin mới được truy cập
        if (!SessionHelper::isAdmin()) {
            header('Location: /Website-PhanBon/Product/');
            exit();
        }
    }

    /**
     * Trang Dashboard tổng quan
     */
    public function dashboard()
    {
        // Tương tự reports nhưng đơn giản hơn
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Trang Báo Cáo Chi Tiết
     */
    public function reports()
    {
        require_once __DIR__ . '/../views/admin/reports.php';
    }

    /**
     * Xuất báo cáo Excel
     */
    public function exportExcel()
    {
        try {
            // Lấy dữ liệu từ database
            $stmt = $this->conn->query("
                SELECT 
                    o.id,
                    o.customer_name,
                    o.total_amount,
                    o.status,
                    o.created_at,
                    COUNT(od.id) as item_count
                FROM orders o
                LEFT JOIN order_details od ON o.id = od.order_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Tạo file CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=bao-cao-' . date('Y-m-d') . '.csv');
            
            // Thêm BOM để Excel hiển thị đúng tiếng Việt
            echo "\xEF\xBB\xBF";
            
            $output = fopen('php://output', 'w');
            
            // Header
            fputcsv($output, ['Mã ĐH', 'Khách hàng', 'Tổng tiền', 'Trạng thái', 'Số SP', 'Ngày tạo']);
            
            // Data
            foreach ($orders as $order) {
                fputcsv($output, [
                    $order['id'],
                    $order['customer_name'],
                    number_format($order['total_amount'], 0, ',', '.') . ' VNĐ',
                    $this->translateStatus($order['status']),
                    $order['item_count'],
                    date('d/m/Y H:i', strtotime($order['created_at']))
                ]);
            }
            
            fclose($output);
            exit();

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi khi xuất Excel: ' . $e->getMessage();
            header('Location: /Website-PhanBon/Admin/reports');
            exit();
        }
    }

    /**
     * Xuất báo cáo PDF (Simple version)
     */
    public function exportPDF()
    {
        try {
            // Lấy dữ liệu
            $stmt = $this->conn->query("
                SELECT 
                    COUNT(*) as total_products,
                    (SELECT COUNT(*) FROM account) as total_users,
                    (SELECT COUNT(*) FROM orders) as total_orders,
                    (SELECT SUM(total_amount) FROM orders WHERE status = 'completed') as total_revenue
                FROM product
            ");
            $stats = $stmt->fetch(PDO::FETCH_OBJ);

            // Tạo HTML content cho PDF
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: "DejaVu Sans", Arial, sans-serif; }
                    h1 { color: #00A74F; text-align: center; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                    th { background-color: #00A74F; color: white; }
                    .stats { background-color: #f0f0f0; padding: 15px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <h1>BÁO CÁO TỔNG HỢP</h1>
                <p style="text-align: center;">Ngày xuất: ' . date('d/m/Y H:i') . '</p>
                
                <div class="stats">
                    <h3>Thống Kê Tổng Quan</h3>
                    <p><strong>Tổng sản phẩm:</strong> ' . number_format($stats->total_products) . '</p>
                    <p><strong>Tổng người dùng:</strong> ' . number_format($stats->total_users) . '</p>
                    <p><strong>Tổng đơn hàng:</strong> ' . number_format($stats->total_orders) . '</p>
                    <p><strong>Tổng doanh thu:</strong> ' . number_format($stats->total_revenue, 0, ',', '.') . ' VNĐ</p>
                </div>
            </body>
            </html>
            ';

            // Output HTML (Simple version - real PDF needs library like TCPDF or DOMPDF)
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: inline; filename=bao-cao-' . date('Y-m-d') . '.html');
            echo $html;
            exit();

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi khi xuất PDF: ' . $e->getMessage();
            header('Location: /Website-PhanBon/Admin/reports');
            exit();
        }
    }

    /**
     * Cài đặt hệ thống
     */
    public function settings()
    {
        require_once __DIR__ . '/../views/admin/settings.php';
    }

    /**
     * Helper function - Dịch trạng thái
     */
    private function translateStatus($status)
    {
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];
        
        return $statusMap[$status] ?? $status;
    }

    /**
     * API: Lấy dữ liệu thống kê (JSON)
     */
    public function getStats()
    {
        header('Content-Type: application/json');
        
        try {
            $stats = [
                'products' => $this->conn->query("SELECT COUNT(*) FROM product")->fetchColumn(),
                'users' => $this->conn->query("SELECT COUNT(*) FROM account")->fetchColumn(),
                'orders' => $this->conn->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
                'revenue' => $this->conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn()
            ];
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }
}
?>