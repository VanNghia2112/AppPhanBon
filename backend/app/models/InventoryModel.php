<?php
class InventoryModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addMovement($productId, $type, $quantity, $reason, $userId = null)
    {
        $quantity = (int)$quantity;
        if ($quantity <= 0) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            // 1. Lưu lịch sử vào stock_movements
            $sql = "INSERT INTO stock_movements (product_id, type, quantity, reason, user_id)
                    VALUES (:product_id, :type, :quantity, :reason, :user_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':product_id' => $productId,
                ':type'       => $type,
                ':quantity'   => $quantity,
                ':reason'     => $reason,
                ':user_id'    => $userId
            ]);

            // 2. Cập nhật stock trong product
            $delta = 0;
            if ($type === 'import') {
                $delta = $quantity;         // nhập: +quantity
            } elseif ($type === 'export') {
                $delta = -$quantity;        // xuất: -quantity
            } elseif ($type === 'adjust') {
                $delta = $quantity;         // điều chỉnh: tuỳ bạn muốn + hay - (ở form có thể cho nhập số âm)
            }

            $sql2 = "UPDATE product
                     SET stock = stock + :delta
                     WHERE id = :product_id";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->execute([
                ':delta'      => $delta,
                ':product_id' => $productId
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            // Bạn có thể log lỗi ở đây
            return false;
        }
    }
}
