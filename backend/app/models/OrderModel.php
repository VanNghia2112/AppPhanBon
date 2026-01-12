<?php
class OrderModel {
    private $conn;
    private $orders_table = "orders";
    private $order_details_table = "order_details";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả đơn hàng
    public function getAllOrders() {
        $query = "SELECT o.*, 
                  COALESCE(SUM(od.quantity * od.price), 0) as total_amount,
                  COUNT(od.id) as total_items
                  FROM " . $this->orders_table . " o
                  LEFT JOIN " . $this->order_details_table . " od ON o.id = od.order_id
                  GROUP BY o.id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy đơn hàng theo ID
    public function getOrderById($orderId) {
        $query = "SELECT o.*, 
                  COALESCE(SUM(od.quantity * od.price), 0) as total_amount
                  FROM " . $this->orders_table . " o
                  LEFT JOIN " . $this->order_details_table . " od ON o.id = od.order_id
                  WHERE o.id = :order_id
                  GROUP BY o.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Lấy chi tiết đơn hàng
    public function getOrderDetails($orderId) {
        $query = "SELECT od.*, p.name as product_name, p.image as product_image
                  FROM " . $this->order_details_table . " od
                  LEFT JOIN product p ON od.product_id = p.id
                  WHERE od.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus($orderId, $status) {
        $query = "UPDATE " . $this->orders_table . " 
                  SET status = :status 
                  WHERE id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $orderId);
        
        return $stmt->execute();
    }

    // Cập nhật thông tin đơn hàng
    public function updateOrderInfo($orderId, $data) {
        $query = "UPDATE " . $this->orders_table . " 
                  SET payment_method = :payment_method,
                      shipping_fee = :shipping_fee,
                      note = :note
                  WHERE id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':shipping_fee', $data['shipping_fee']);
        $stmt->bindParam(':note', $data['note']);
        $stmt->bindParam(':order_id', $orderId);
        
        return $stmt->execute();
    }

    // Xóa đơn hàng
    public function deleteOrder($orderId) {
        $this->conn->beginTransaction();
        
        try {
            // Xóa chi tiết đơn hàng
            $query1 = "DELETE FROM " . $this->order_details_table . " WHERE order_id = :order_id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(':order_id', $orderId);
            $stmt1->execute();
            
            // Xóa đơn hàng
            $query2 = "DELETE FROM " . $this->orders_table . " WHERE id = :order_id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':order_id', $orderId);
            $stmt2->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Thống kê đơn hàng
    public function getOrderStatistics() {
        $query = "SELECT 
                  COUNT(*) as total_orders,
                  SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                  SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                  SUM(CASE WHEN status = 'shipping' THEN 1 ELSE 0 END) as shipping_orders,
                  SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                  SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
                  FROM " . $this->orders_table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Xuất dữ liệu cho Excel
    public function getAllOrdersForExport() {
        $query = "SELECT o.*, 
                  COALESCE(SUM(od.quantity * od.price), 0) as total_amount
                  FROM " . $this->orders_table . " o
                  LEFT JOIN " . $this->order_details_table . " od ON o.id = od.order_id
                  GROUP BY o.id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Trong file app/models/OrderModel.php

    // FILE: app/models/OrderModel.php
}